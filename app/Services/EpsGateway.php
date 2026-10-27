<?php

namespace App\Services;

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

    public function token(): string
    {
        $baseUrl = $this->getBaseUrl();
        $username = trim($this->config['username'] ?? '');
        $password = trim($this->config['password'] ?? '');

        if (empty($baseUrl) || empty($username) || empty($password)) {
            throw new \Exception('EPS credentials are not configured in environment settings.');
        }

        try {
            $response = Http::timeout(20)->withHeaders([
                'x-hash' => $this->hash($username),
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/Auth/GetToken', [
                'userName' => $username,
                'password' => $password,
            ]);

            if (!$response->successful() || empty($response->json('token'))) {
                Log::error('EPS Token Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                $errorMsg = $response->json('message') ?? $response->json('Message') ?? $response->body();
                throw new \Exception('EPS token failed: ' . ($errorMsg ?: 'HTTP ' . $response->status()));
            }

            return $response->json('token');
        } catch (\Throwable $e) {
            Log::error('EPS Token Exception: ' . $e->getMessage());
            throw $e;
        }
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

            'successUrl' => route('eps.success'),
            'failUrl' => route('eps.fail'),
            'cancelUrl' => route('eps.cancel'),

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
            $response = Http::timeout(25)->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'x-hash' => $this->hash($transactionId),
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/EPSEngine/InitializeEPS', $payload);

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
        $token = retry(2, fn () => $this->token(), 200);

        $response = retry(3, function () use ($baseUrl, $token, $merchantTransactionId) {
            return Http::timeout(20)->withToken($token)->withHeaders([
                'x-hash' => $this->hash($merchantTransactionId),
            ])->get($baseUrl . '/EPSEngine/CheckMerchantTransactionStatus', [
                'merchantTransactionId' => $merchantTransactionId,
            ]);
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
