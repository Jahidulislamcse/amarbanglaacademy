<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EpsGateway
{
    private array $config;

    public function __construct()
    {
        $this->config = config('services.eps') ?? [];
    }

    private function getBaseUrl(): string
    {
        return rtrim($this->config['base_url'] ?? '', '/');
    }

    private function hash(string $data): string
    {
        $hashKey = $this->config['hash_key'] ?? '';
        return base64_encode(hash_hmac(
            'sha512',
            $data,
            $hashKey,
            true
        ));
    }

    /**
     * Clear cached EPS authentication token
     */
    public function clearTokenCache(): void
    {
        $cacheKey = 'eps_auth_token_' . md5($this->config['username'] ?? '');
        Cache::forget($cacheKey);
    }

    /**
     * Obtain EPS JWT Bearer Token (Cached for 50 minutes to eliminate repetitive auth latency)
     */
    public function token(bool $forceRefresh = false): string
    {
        $cacheKey = 'eps_auth_token_' . md5($this->config['username'] ?? '');

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 3000, function () {
            $baseUrl = $this->getBaseUrl();
            $username = trim($this->config['username'] ?? '');
            $password = trim($this->config['password'] ?? '');

            if (empty($baseUrl) || empty($username) || empty($password)) {
                throw new \Exception('EPS credentials are not configured in environment settings.');
            }

            return retry(2, function ($attempt) use ($baseUrl, $username, $password) {
                try {
                    $response = Http::timeout(45)
                        ->connectTimeout(15)
                        ->withOptions([
                            'curl' => [
                                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                            ],
                        ])
                        ->withHeaders([
                            'x-hash' => $this->hash($username),
                            'Content-Type' => 'application/json',
                        ])->post($baseUrl . '/Auth/GetToken', [
                            'userName' => $username,
                            'password' => $password,
                        ]);

                    if (!$response->successful() || empty($response->json('token'))) {
                        Log::error('EPS Token Error (Attempt ' . $attempt . ')', [
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                        $errorMsg = $response->json('message') ?? $response->json('Message') ?? $response->body();
                        throw new \Exception('EPS token failed: ' . ($errorMsg ?: 'HTTP ' . $response->status()));
                    }

                    return $response->json('token');
                } catch (\Throwable $e) {
                    Log::warning("EPS Token attempt {$attempt} failed: " . $e->getMessage());
                    throw $e;
                }
            }, 1000);
        });
    }

    public function getCallbackUrl(string $routeName): string
    {
        $url = route($routeName);
        $parsed = parse_url($url);
        $host = strtolower($parsed['host'] ?? '');

        // 1. If current host is already amarbangla24.com.bd or any of its subdomains
        if ($host === 'amarbangla24.com.bd' || str_ends_with($host, '.amarbangla24.com.bd')) {
            return $url;
        }

        // 2. Custom Base URL from config
        $callbackBase = rtrim($this->config['callback_base_url'] ?? '', '/');
        if ($callbackBase) {
            $path = $parsed['path'] ?? '/payment/eps/' . str_replace('eps.', '', $routeName);
            return $callbackBase . $path;
        }

        // 3. Custom Domain from config
        $callbackDomain = $this->config['callback_domain'] ?? null;
        if ($callbackDomain) {
            $scheme = $parsed['scheme'] ?? 'https';
            $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
            $path = $parsed['path'] ?? '/payment/eps/' . str_replace('eps.', '', $routeName);
            return $scheme . '://' . $callbackDomain . $port . $path;
        }

        // 4. Fallback for localhost / local IP testing:
        // EPS strictly whitelists amarbangla24.com.bd, so we rewrite the host to satisfy EPS domain whitelist
        $path = $parsed['path'] ?? '/payment/eps/' . str_replace('eps.', '', $routeName);
        return 'https://amarbangla24.com.bd' . $path;
    }

    public function initialize(array $order): array
    {
        $baseUrl = $this->getBaseUrl();
        $token = $this->token();
        $transactionId = trim($order['transaction_id']);

        $ip = request()->ip();
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
            $ip = '103.145.118.20'; // Default valid public IP fallback if running locally
        }

        $merchantId = trim((string) ($this->config['merchant_id'] ?? ''));
        if (empty($merchantId)) {
            throw new \Exception('EPS Merchant ID is not configured in .env (EPS_MERCHANT_ID / EPSMerchentID).');
        }

        $rawStoreId = $this->config['store_id'] ?? 0;
        $storeId = is_numeric($rawStoreId) ? (int) $rawStoreId : trim((string) $rawStoreId);

        $payload = [
            'merchantId' => $merchantId,
            'storeId' => $storeId,
            'CustomerOrderId' => (string) ($order['order_id'] ?? $transactionId),
            'merchantTransactionId' => $transactionId,
            'transactionTypeId' => 1,
            'financialEntityId' => 0,
            'transitionStatusId' => 0,
            'totalAmount' => (float) $order['amount'],
            'ipAddress' => $ip,
            'version' => '1',

            'successUrl' => $this->getCallbackUrl('eps.success'),
            'failUrl' => $this->getCallbackUrl('eps.fail'),
            'cancelUrl' => $this->getCallbackUrl('eps.cancel'),

            'customerName' => $order['customer_name'] ?? 'Student',
            'customerEmail' => $order['customer_email'] ?? 'student@example.com',
            'customerAddress' => $order['customer_address'] ?? 'Dhaka',
            'customerAddress2' => '',
            'customerCity' => $order['customer_city'] ?? 'Dhaka',
            'customerState' => $order['customer_state'] ?? 'Dhaka',
            'customerPostcode' => $order['customer_postcode'] ?? '1200',
            'customerCountry' => 'BD',
            'customerPhone' => $order['customer_phone'] ?? '01700000000',

            'shippingMethod' => 'NO',
            'noOfItem' => '1',
            'productName' => $order['product_name'] ?? 'LMS Course Enrollment',
            'productProfile' => 'general',
            'productCategory' => 'Education',
        ];

        try {
            $response = retry(2, function ($attempt) use ($baseUrl, &$token, $payload, $transactionId) {
                try {
                    $res = Http::timeout(40)
                        ->connectTimeout(15)
                        ->withOptions([
                            'curl' => [
                                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                            ],
                        ])
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . $token,
                            'x-hash' => $this->hash($transactionId),
                            'Content-Type' => 'application/json',
                        ])->post($baseUrl . '/EPSEngine/InitializeEPS', $payload);

                    if ($res->status() === 401 && $attempt === 1) {
                        Log::warning('EPS Initialize received 401, refreshing token...');
                        $token = $this->token(true);
                        throw new \Exception('Token refreshed on 401, retrying initialize...');
                    }

                    return $res;
                } catch (\Throwable $e) {
                    if ($attempt === 1) {
                        Log::warning("EPS Initialize attempt {$attempt} failed: " . $e->getMessage() . ", retrying...");
                    }
                    throw $e;
                }
            }, 1000);

            Log::info('EPS initialize payload', [
                'transaction_id' => $transactionId,
                'amount' => $order['amount'],
                'merchantId' => $merchantId,
            ]);
            Log::info('EPS initialize raw response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $data = $response->json();

            if (!$response->successful() || !$data) {
                if (isset($data['errors']) && is_array($data['errors'])) {
                    $errList = [];
                    foreach ($data['errors'] as $f => $errs) {
                        $errList[] = is_array($errs) ? implode(', ', $errs) : (string) $errs;
                    }
                    $errorMsg = implode(' | ', $errList);
                } else {
                    $errorMsg = $data['message'] ?? $data['Message'] ?? $data['title'] ?? $response->body();
                }
                throw new \Exception('EPS initialize failed: ' . ($errorMsg ?: 'HTTP ' . $response->status()));
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('EPS Initialize Exception: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'payload' => $payload
            ]);
            throw $e;
        }
    }

    public function verify(string $merchantTransactionId): array
    {
        $baseUrl = $this->getBaseUrl();
        $token = $this->token();

        $response = retry(3, function ($attempt) use ($baseUrl, &$token, $merchantTransactionId) {
            try {
                $res = Http::timeout(35)
                    ->connectTimeout(15)
                    ->withOptions([
                        'curl' => [
                            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                        ],
                    ])
                    ->withToken($token)
                    ->withHeaders([
                        'x-hash' => $this->hash($merchantTransactionId),
                    ])->get($baseUrl . '/EPSEngine/CheckMerchantTransactionStatus', [
                        'merchantTransactionId' => $merchantTransactionId,
                    ]);

                if ($res->status() === 401 && $attempt === 1) {
                    Log::warning('EPS Verify received 401, refreshing token...');
                    $token = $this->token(true);
                    throw new \Exception('Token refreshed on 401, retrying verify...');
                }

                return $res;
            } catch (\Throwable $e) {
                if ($attempt < 3) {
                    Log::warning("EPS Verify attempt {$attempt} failed: " . $e->getMessage() . ", retrying...");
                }
                throw $e;
            }
        }, 1000);

        if (!$response->successful()) {
            Log::error('EPS verify failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'transaction_id' => $merchantTransactionId
            ]);
            throw new \Exception('EPS verify failed: ' . $response->body());
        }

        return $response->json();
    }
}
