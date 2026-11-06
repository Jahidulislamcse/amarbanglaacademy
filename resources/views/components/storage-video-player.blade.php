@props([
    'meeting',
    'user' => null,
    'autoplay' => false,
    'playerId' => null,
])

@php
    $pId = $playerId ?? ('storage_player_' . ($meeting->id ?? 'preview'));
    $youtubeId = $meeting->youtube_id ?? \App\Models\LiveMeeting::extractYoutubeId($meeting->recording_url ?? '');
    $isDirect = $meeting->isDirectVideo() || empty($youtubeId);
    $currentUser = $user ?? Auth::user();
@endphp

<div id="{{ $pId }}"
     class="storage-player-root relative w-full aspect-video bg-black rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl select-none group border border-slate-800"
     tabindex="0"
     data-player-id="{{ $pId }}"
     data-is-direct="{{ $isDirect ? '1' : '0' }}"
     data-youtube-id="{{ $youtubeId }}"
     data-direct-url="{{ $meeting->recording_url ?? '' }}"
     data-autoplay="{{ $autoplay ? '1' : '0' }}">

    <style>
        /* Unbranded Storage Video Player Styling */
        #{{ $pId }} .yt-cropper-wrap {
            position: absolute;
            top: -12%;
            left: -10%;
            width: 120%;
            height: 124%;
            pointer-events: none;
            overflow: hidden;
        }
        #{{ $pId }} .yt-cropper-wrap iframe {
            width: 100% !important;
            height: 100% !important;
            border: 0 !important;
            pointer-events: none !important;
        }
        #{{ $pId }} input[type=range]::-webkit-slider-thumb {
            appearance: none;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #10b981;
            cursor: pointer;
        }
    </style>

    <!-- 1. VIDEO LAYER (Cropped & Scaled to eliminate YouTube UI completely) -->
    <div class="video-stage absolute inset-0 overflow-hidden bg-black pointer-events-none">
        @if($isDirect && !empty($meeting->recording_url))
            <video id="{{ $pId }}_video"
                   playsinline
                   preload="metadata"
                   class="w-full h-full object-contain pointer-events-none">
                <source src="{{ $meeting->recording_url }}" type="video/mp4">
            </video>
        @else
            <div class="yt-cropper-wrap">
                <div id="{{ $pId }}_yt_frame" class="w-full h-full pointer-events-none"></div>
            </div>
        @endif
    </div>

    <!-- 2. CLICK SURFACE (Intercepts play/pause & double click seek) -->
    <div class="click-surface absolute inset-0 z-10 cursor-pointer pointer-events-auto"
         title="ক্লিক করে প্লে / পজ করুন"></div>

    <!-- 3. CENTER STATE OVERLAY (Big Play Button, Buffering Spinner, Ripple Feedback) -->
    <div class="center-overlay absolute inset-0 z-20 pointer-events-none flex items-center justify-center">
        <!-- Big Initial Play Button -->
        <button type="button" class="big-play-btn w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-emerald-600/90 hover:bg-emerald-500 text-white flex items-center justify-center text-2xl sm:text-3xl shadow-2xl shadow-emerald-500/50 backdrop-blur-sm pointer-events-auto transition-transform hover:scale-110 active:scale-95">
            <i class="fa-solid fa-play ml-1"></i>
        </button>

        <!-- Loading / Buffering Spinner -->
        <div class="loading-spinner hidden flex-col items-center gap-2">
            <div class="w-12 h-12 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
            <span class="text-xs font-semibold text-emerald-300 drop-shadow">ভিডিও লোড হচ্ছে...</span>
        </div>

        <!-- Ripple Action Feedback (Play/Pause/Seek) -->
        <div class="action-ripple opacity-0 scale-75 transition-all duration-300 w-16 h-16 rounded-full bg-black/60 backdrop-blur-xs text-white flex items-center justify-center text-2xl">
            <i class="fa-solid fa-play"></i>
        </div>
    </div>

    <!-- 4. TOP SECURE STORAGE TITLE & BADGE BAR -->
    <div class="top-bar absolute top-0 inset-x-0 z-30 p-3 sm:p-5 bg-gradient-to-b from-black/80 via-black/40 to-transparent flex items-start justify-between gap-3 transition-opacity duration-300 pointer-events-none">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs font-black shadow-sm shrink-0">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="leading-tight">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] sm:text-xs font-bold text-emerald-400 uppercase tracking-wider bg-emerald-950/90 px-2 py-0.5 rounded border border-emerald-800">
                        সুরক্ষিত স্টোরেজ প্লেয়ার
                    </span>
                    <span class="text-[10px] text-slate-300 font-mono bg-slate-900/90 px-1.5 py-0.5 rounded border border-slate-700">
                        HD 1080p
                    </span>
                </div>
                <h3 class="text-xs sm:text-sm font-bold text-white mt-1 drop-shadow-md line-clamp-1">
                    {{ $meeting->title }}
                </h3>
            </div>
        </div>

        <div class="text-right hidden sm:block">
            <span class="text-[11px] text-slate-300 font-medium block">আমার বাংলা একাডেমি</span>
            <span class="text-[10px] text-emerald-400 block font-bold">{{ $meeting->course?->bangla_title ?? 'সাংবাদিকতা কোর্স' }}</span>
        </div>
    </div>

    <!-- 5. STUDENT SECURITY WATERMARK (Subtle moving watermark to protect content) -->
    @if($currentUser)
        <div class="student-watermark absolute z-25 pointer-events-none text-[10px] sm:text-[11px] font-mono text-white/25 font-semibold tracking-wider select-none transition-all duration-1000 ease-in-out"
             style="bottom: 22%; right: 6%;">
            <span>🔒 {{ $currentUser->name }} • {{ $currentUser->phone ?? 'ID#'.$currentUser->id }}</span>
        </div>
    @endif

    <!-- 6. BOTTOM CONTROL BAR -->
    <div class="control-bar absolute bottom-0 inset-x-0 z-30 px-3 sm:px-5 pb-3 pt-8 bg-gradient-to-t from-black/95 via-black/60 to-transparent transition-opacity duration-300 pointer-events-auto">
        <!-- Scrubber Progress Bar -->
        <div class="scrubber-box relative w-full h-3 group/scrub cursor-pointer flex items-center mb-2">
            <!-- Scrubber Track Background -->
            <div class="scrubber-track relative w-full h-1 group-hover/scrub:h-1.5 bg-white/20 rounded-full overflow-hidden transition-all duration-150">
                <!-- Buffer Progress -->
                <div class="scrubber-buffer absolute inset-y-0 left-0 bg-white/30 rounded-full w-0"></div>
                <!-- Played Progress -->
                <div class="scrubber-played absolute inset-y-0 left-0 bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full w-0"></div>
            </div>
            <!-- Thumb / Handle -->
            <div class="scrubber-thumb absolute top-1/2 -translate-y-1/2 -ml-1.5 w-3 h-3 sm:w-3.5 sm:h-3.5 bg-white rounded-full shadow-md scale-0 group-hover/scrub:scale-100 transition-transform duration-150 pointer-events-none"></div>
            <!-- Hover Tooltip -->
            <div class="scrubber-tooltip absolute -top-7 hidden group-hover/scrub:block -translate-x-1/2 px-1.5 py-0.5 rounded bg-black/90 text-[10px] font-mono text-white pointer-events-none border border-slate-700 shadow">
                00:00
            </div>
        </div>

        <!-- Controls Row -->
        <div class="flex items-center justify-between text-white text-xs sm:text-sm">
            <!-- Left Controls: Play/Pause, 10s Rewind/Forward, Time Counter, Volume -->
            <div class="flex items-center gap-1.5 sm:gap-3">
                <!-- Play/Pause -->
                <button type="button" class="btn-play-toggle p-1.5 text-white hover:text-emerald-400 transition" title="প্লে / পজ (Space)">
                    <i class="fa-solid fa-play text-sm"></i>
                </button>

                <!-- 10s Rewind -->
                <button type="button" class="btn-skip-back p-1 text-slate-300 hover:text-white transition" title="১০ সেকেন্ড পেছনে (←)">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </button>

                <!-- 10s Forward -->
                <button type="button" class="btn-skip-forward p-1 text-slate-300 hover:text-white transition" title="১০ সেকেন্ড সামনে (→)">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                </button>

                <!-- Time Counter -->
                <div class="time-counter font-mono text-[11px] sm:text-xs text-slate-300 select-none ml-1">
                    <span class="time-current">00:00</span> / <span class="time-duration">{{ $meeting->recording_duration ?: '00:00' }}</span>
                </div>

                <!-- Volume & Mute -->
                <div class="flex items-center gap-1.5 ml-1 sm:ml-2">
                    <button type="button" class="btn-vol-toggle p-1 text-slate-300 hover:text-white transition" title="মিউট / আনমিউট (M)">
                        <i class="fa-solid fa-volume-high text-xs sm:text-sm"></i>
                    </button>
                    <input type="range" min="0" max="100" value="100"
                           class="vol-slider w-12 sm:w-16 h-1 bg-white/20 accent-emerald-500 rounded-lg cursor-pointer">
                </div>
            </div>

            <!-- Right Controls: Speed Selector, Fullscreen -->
            <div class="flex items-center gap-1.5 sm:gap-2.5">
                <!-- Speed Dropdown -->
                <div class="relative speed-wrapper">
                    <button type="button" class="btn-speed-toggle px-2 py-0.5 rounded bg-white/10 hover:bg-white/20 text-[11px] font-mono font-bold transition">
                        <span class="speed-label">1x</span>
                    </button>
                    <div class="speed-menu absolute bottom-full right-0 mb-2 hidden bg-slate-900/95 border border-slate-700 rounded-xl shadow-xl py-1 text-xs font-mono min-w-[70px] z-50">
                        <button type="button" data-val="0.5" class="speed-item w-full px-3 py-1 text-left hover:bg-emerald-600/30 text-slate-200">0.5x</button>
                        <button type="button" data-val="0.75" class="speed-item w-full px-3 py-1 text-left hover:bg-emerald-600/30 text-slate-200">0.75x</button>
                        <button type="button" data-val="1" class="speed-item w-full px-3 py-1 text-left hover:bg-emerald-600/30 text-emerald-400 font-bold">1.0x</button>
                        <button type="button" data-val="1.25" class="speed-item w-full px-3 py-1 text-left hover:bg-emerald-600/30 text-slate-200">1.25x</button>
                        <button type="button" data-val="1.5" class="speed-item w-full px-3 py-1 text-left hover:bg-emerald-600/30 text-slate-200">1.5x</button>
                        <button type="button" data-val="2" class="speed-item w-full px-3 py-1 text-left hover:bg-emerald-600/30 text-slate-200">2.0x</button>
                    </div>
                </div>

                <!-- Fullscreen -->
                <button type="button" class="btn-fullscreen-toggle p-1.5 text-slate-300 hover:text-white transition" title="ফুলস্ক্রিন (F)">
                    <i class="fa-solid fa-expand text-xs sm:text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- 7. REPLAY OVERLAY (When video ends) -->
    <div class="replay-overlay absolute inset-0 z-40 bg-black/90 hidden flex-col items-center justify-center p-6 text-center">
        <div class="w-16 h-16 rounded-full bg-emerald-600/20 text-emerald-400 flex items-center justify-center text-3xl mb-3 border border-emerald-500/30">
            <i class="fa-solid fa-check"></i>
        </div>
        <h4 class="text-base sm:text-lg font-bold text-white">ক্লাস রেকর্ডিংটি সম্পন্ন হয়েছে</h4>
        <p class="text-xs text-slate-400 mt-1 max-w-sm">
            আপনি সফলভাবে এই ক্লাস লেকচারটি সম্পন্ন করেছেন। যেকোনো সময় পুনরায় দেখতে নিচের বাটনে ক্লিক করুন।
        </p>
        <button type="button" class="btn-replay-action mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition">
            <i class="fa-solid fa-rotate-right"></i>
            <span>পুনরায় দেখুন</span>
        </button>
    </div>
</div>

<script>
(function() {
    const root = document.getElementById('{{ $pId }}');
    if (!root) return;

    // Elements
    const clickSurface = root.querySelector('.click-surface');
    const bigPlayBtn = root.querySelector('.big-play-btn');
    const loadingSpinner = root.querySelector('.loading-spinner');
    const actionRipple = root.querySelector('.action-ripple');
    const topBar = root.querySelector('.top-bar');
    const controlBar = root.querySelector('.control-bar');
    const btnPlayToggle = root.querySelector('.btn-play-toggle');
    const btnSkipBack = root.querySelector('.btn-skip-back');
    const btnSkipForward = root.querySelector('.btn-skip-forward');
    const timeCurrent = root.querySelector('.time-current');
    const timeDuration = root.querySelector('.time-duration');
    const btnVolToggle = root.querySelector('.btn-vol-toggle');
    const volSlider = root.querySelector('.vol-slider');
    const btnSpeedToggle = root.querySelector('.btn-speed-toggle');
    const speedLabel = root.querySelector('.speed-label');
    const speedMenu = root.querySelector('.speed-menu');
    const speedItems = root.querySelectorAll('.speed-item');
    const btnFullscreenToggle = root.querySelector('.btn-fullscreen-toggle');
    const scrubberBox = root.querySelector('.scrubber-box');
    const scrubberBuffer = root.querySelector('.scrubber-buffer');
    const scrubberPlayed = root.querySelector('.scrubber-played');
    const scrubberThumb = root.querySelector('.scrubber-thumb');
    const scrubberTooltip = root.querySelector('.scrubber-tooltip');
    const replayOverlay = root.querySelector('.replay-overlay');
    const btnReplayAction = root.querySelector('.btn-replay-action');
    const watermark = root.querySelector('.student-watermark');

    const isDirect = root.getAttribute('data-is-direct') === '1';
    const youtubeId = root.getAttribute('data-youtube-id');
    const directVideo = root.querySelector('video');

    let ytPlayer = null;
    let isPlaying = false;
    let duration = 0;
    let currentTime = 0;
    let currentVolume = 100;
    let isMuted = false;
    let currentSpeed = 1;
    let updateInterval = null;
    let controlsTimeout = null;
    let watermarkInterval = null;

    // Helper: format time MM:SS or HH:MM:SS
    function formatTime(sec) {
        if (!sec || isNaN(sec) || sec < 0) return '00:00';
        sec = Math.floor(sec);
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        if (h > 0) {
            return `${h}:${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
        }
        return `${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
    }

    // Ripple feedback
    function showRipple(iconClass) {
        if (!actionRipple) return;
        actionRipple.innerHTML = `<i class="${iconClass}"></i>`;
        actionRipple.classList.remove('opacity-0', 'scale-75');
        actionRipple.classList.add('opacity-100', 'scale-100');
        setTimeout(() => {
            actionRipple.classList.remove('opacity-100', 'scale-100');
            actionRipple.classList.add('opacity-0', 'scale-75');
        }, 500);
    }

    // Controls Auto-Hide
    function resetControlsTimer() {
        topBar.classList.remove('opacity-0');
        controlBar.classList.remove('opacity-0');
        root.style.cursor = 'default';

        if (controlsTimeout) clearTimeout(controlsTimeout);

        if (isPlaying) {
            controlsTimeout = setTimeout(() => {
                topBar.classList.add('opacity-0');
                controlBar.classList.add('opacity-0');
                speedMenu.classList.add('hidden');
                root.style.cursor = 'none';
            }, 3200);
        }
    }

    root.addEventListener('mousemove', resetControlsTimer);
    root.addEventListener('mouseleave', () => {
        if (isPlaying) {
            topBar.classList.add('opacity-0');
            controlBar.classList.add('opacity-0');
            speedMenu.classList.add('hidden');
        }
    });

    // Disable Right-Click on player
    root.addEventListener('contextmenu', e => e.preventDefault());

    // Watermark random wander
    if (watermark) {
        const positions = [
            { top: '15%', left: '8%' },
            { top: '20%', right: '10%', left: 'auto' },
            { bottom: '22%', right: '6%', top: 'auto', left: 'auto' },
            { bottom: '25%', left: '8%', top: 'auto', right: 'auto' },
            { top: '45%', right: '15%', bottom: 'auto', left: 'auto' },
        ];
        let pIndex = 0;
        watermarkInterval = setInterval(() => {
            pIndex = (pIndex + 1) % positions.length;
            const p = positions[pIndex];
            watermark.style.top = p.top || 'auto';
            watermark.style.bottom = p.bottom || 'auto';
            watermark.style.left = p.left || 'auto';
            watermark.style.right = p.right || 'auto';
        }, 20000);
    }

    // Play/Pause State UI
    function setPlayState(playing) {
        isPlaying = playing;
        if (playing) {
            btnPlayToggle.innerHTML = '<i class="fa-solid fa-pause text-sm"></i>';
            bigPlayBtn.classList.add('hidden');
            replayOverlay.classList.add('hidden');
            replayOverlay.classList.remove('flex');
            resetControlsTimer();
        } else {
            btnPlayToggle.innerHTML = '<i class="fa-solid fa-play text-sm"></i>';
            topBar.classList.remove('opacity-0');
            controlBar.classList.remove('opacity-0');
            root.style.cursor = 'default';
        }
    }

    // Core Play/Pause execution
    function togglePlay() {
        if (isDirect && directVideo) {
            if (directVideo.paused) {
                directVideo.play();
                showRipple('fa-solid fa-play');
            } else {
                directVideo.pause();
                showRipple('fa-solid fa-pause');
            }
        } else if (ytPlayer && ytPlayer.getPlayerState) {
            const state = ytPlayer.getPlayerState();
            if (state === 1) { // playing
                ytPlayer.pauseVideo();
                showRipple('fa-solid fa-pause');
            } else {
                ytPlayer.playVideo();
                showRipple('fa-solid fa-play');
            }
        }
    }

    // Seek execution
    function seekTo(targetSec) {
        targetSec = Math.max(0, Math.min(targetSec, duration || 0));
        currentTime = targetSec;
        if (isDirect && directVideo) {
            directVideo.currentTime = targetSec;
        } else if (ytPlayer && ytPlayer.seekTo) {
            ytPlayer.seekTo(targetSec, true);
        }
        updateProgressUI();
    }

    function skip(seconds) {
        seekTo(currentTime + seconds);
        showRipple(seconds > 0 ? 'fa-solid fa-rotate-right' : 'fa-solid fa-rotate-left');
    }

    // Volume execution
    function setVolume(vol) {
        vol = Math.max(0, Math.min(100, vol));
        currentVolume = vol;
        volSlider.value = vol;

        if (vol === 0) {
            isMuted = true;
            btnVolToggle.innerHTML = '<i class="fa-solid fa-volume-xmark text-xs sm:text-sm"></i>';
        } else if (vol < 50) {
            isMuted = false;
            btnVolToggle.innerHTML = '<i class="fa-solid fa-volume-low text-xs sm:text-sm"></i>';
        } else {
            isMuted = false;
            btnVolToggle.innerHTML = '<i class="fa-solid fa-volume-high text-xs sm:text-sm"></i>';
        }

        if (isDirect && directVideo) {
            directVideo.volume = vol / 100;
            directVideo.muted = isMuted;
        } else if (ytPlayer && ytPlayer.setVolume) {
            ytPlayer.setVolume(vol);
            if (isMuted) ytPlayer.mute(); else ytPlayer.unMute();
        }
    }

    function toggleMute() {
        if (isMuted) {
            setVolume(currentVolume || 80);
        } else {
            setVolume(0);
        }
    }

    // Speed execution
    function setSpeed(spd) {
        currentSpeed = parseFloat(spd);
        speedLabel.textContent = `${spd}x`;
        speedMenu.classList.add('hidden');

        speedItems.forEach(item => {
            if (parseFloat(item.getAttribute('data-val')) === currentSpeed) {
                item.classList.add('text-emerald-400', 'font-bold');
                item.classList.remove('text-slate-200');
            } else {
                item.classList.remove('text-emerald-400', 'font-bold');
                item.classList.add('text-slate-200');
            }
        });

        if (isDirect && directVideo) {
            directVideo.playbackRate = currentSpeed;
        } else if (ytPlayer && ytPlayer.setPlaybackRate) {
            ytPlayer.setPlaybackRate(currentSpeed);
        }
    }

    // Fullscreen execution
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            if (root.requestFullscreen) {
                root.requestFullscreen();
            } else if (root.webkitRequestFullscreen) {
                root.webkitRequestFullscreen();
            } else if (root.msRequestFullscreen) {
                root.msRequestFullscreen();
            }
            btnFullscreenToggle.innerHTML = '<i class="fa-solid fa-compress text-xs sm:text-sm"></i>';
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
            btnFullscreenToggle.innerHTML = '<i class="fa-solid fa-expand text-xs sm:text-sm"></i>';
        }
    }

    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            btnFullscreenToggle.innerHTML = '<i class="fa-solid fa-expand text-xs sm:text-sm"></i>';
        } else {
            btnFullscreenToggle.innerHTML = '<i class="fa-solid fa-compress text-xs sm:text-sm"></i>';
        }
    });

    // Update Progress UI
    function updateProgressUI() {
        if (duration > 0) {
            const pct = (currentTime / duration) * 100;
            scrubberPlayed.style.width = `${pct}%`;
            scrubberThumb.style.left = `${pct}%`;
            timeCurrent.textContent = formatTime(currentTime);
            timeDuration.textContent = formatTime(duration);
        }
    }

    // Scrubber click & drag
    let isScrubbing = false;
    function handleScrub(e) {
        const rect = scrubberBox.getBoundingClientRect();
        const pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
        const target = pos * duration;
        seekTo(target);
    }

    scrubberBox.addEventListener('mousedown', e => {
        isScrubbing = true;
        handleScrub(e);
    });

    window.addEventListener('mousemove', e => {
        if (isScrubbing) handleScrub(e);
    });

    window.addEventListener('mouseup', () => {
        isScrubbing = false;
    });

    scrubberBox.addEventListener('mousemove', e => {
        const rect = scrubberBox.getBoundingClientRect();
        const pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
        const hoverSec = pos * duration;
        scrubberTooltip.textContent = formatTime(hoverSec);
        scrubberTooltip.style.left = `${pos * 100}%`;
    });

    // Wire buttons
    clickSurface.addEventListener('click', togglePlay);
    bigPlayBtn.addEventListener('click', togglePlay);
    btnPlayToggle.addEventListener('click', togglePlay);
    btnSkipBack.addEventListener('click', () => skip(-10));
    btnSkipForward.addEventListener('click', () => skip(10));
    btnVolToggle.addEventListener('click', toggleMute);
    volSlider.addEventListener('input', e => setVolume(parseFloat(e.target.value)));

    btnSpeedToggle.addEventListener('click', e => {
        e.stopPropagation();
        speedMenu.classList.toggle('hidden');
    });

    speedItems.forEach(item => {
        item.addEventListener('click', () => setSpeed(item.getAttribute('data-val')));
    });

    document.addEventListener('click', () => {
        if (speedMenu) speedMenu.classList.add('hidden');
    });

    btnFullscreenToggle.addEventListener('click', toggleFullscreen);

    btnReplayAction.addEventListener('click', () => {
        replayOverlay.classList.add('hidden');
        replayOverlay.classList.remove('flex');
        seekTo(0);
        togglePlay();
    });

    // Double-click to seek left (-10s) or right (+10s)
    let clickTimeout = null;
    clickSurface.addEventListener('dblclick', e => {
        const rect = clickSurface.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        if (clickX < rect.width / 2) {
            skip(-10);
        } else {
            skip(10);
        }
    });

    // Keyboard Shortcuts
    root.addEventListener('keydown', e => {
        if (['Space', 'KeyK'].includes(e.code)) {
            e.preventDefault();
            togglePlay();
        } else if (['ArrowLeft', 'KeyJ'].includes(e.code)) {
            e.preventDefault();
            skip(-5);
        } else if (['ArrowRight', 'KeyL'].includes(e.code)) {
            e.preventDefault();
            skip(5);
        } else if (e.code === 'ArrowUp') {
            e.preventDefault();
            setVolume(currentVolume + 10);
        } else if (e.code === 'ArrowDown') {
            e.preventDefault();
            setVolume(currentVolume - 10);
        } else if (e.code === 'KeyM') {
            e.preventDefault();
            toggleMute();
        } else if (e.code === 'KeyF') {
            e.preventDefault();
            toggleFullscreen();
        }
    });

    // A) If Direct Video (Native HTML5 <video>)
    if (isDirect && directVideo) {
        directVideo.addEventListener('loadedmetadata', () => {
            duration = directVideo.duration;
            timeDuration.textContent = formatTime(duration);
        });

        directVideo.addEventListener('timeupdate', () => {
            currentTime = directVideo.currentTime;
            updateProgressUI();
        });

        directVideo.addEventListener('progress', () => {
            if (directVideo.buffered.length > 0 && duration > 0) {
                const buf = directVideo.buffered.end(directVideo.buffered.length - 1);
                scrubberBuffer.style.width = `${(buf / duration) * 100}%`;
            }
        });

        directVideo.addEventListener('play', () => setPlayState(true));
        directVideo.addEventListener('pause', () => setPlayState(false));

        directVideo.addEventListener('ended', () => {
            setPlayState(false);
            replayOverlay.classList.remove('hidden');
            replayOverlay.classList.add('flex');
        });
    }
    // B) If YouTube Video (Masked & Cropped YouTube API Player)
    else if (youtubeId) {
        function initYT() {
            ytPlayer = new YT.Player('{{ $pId }}_yt_frame', {
                videoId: youtubeId,
                playerVars: {
                    autoplay: 0,
                    controls: 0,
                    disablekb: 1,
                    fs: 0,
                    modestbranding: 1,
                    rel: 0,
                    iv_load_policy: 3,
                    playsinline: 1,
                    enablejsapi: 1,
                    origin: window.location.origin
                },
                events: {
                    onReady: onYTReady,
                    onStateChange: onYTStateChange
                }
            });
        }

        function onYTReady(event) {
            duration = ytPlayer.getDuration();
            timeDuration.textContent = formatTime(duration);

            updateInterval = setInterval(() => {
                if (ytPlayer && ytPlayer.getCurrentTime) {
                    currentTime = ytPlayer.getCurrentTime();
                    duration = ytPlayer.getDuration() || duration;
                    updateProgressUI();

                    if (ytPlayer.getVideoLoadedFraction) {
                        const frac = ytPlayer.getVideoLoadedFraction();
                        scrubberBuffer.style.width = `${frac * 100}%`;
                    }
                }
            }, 300);
        }

        function onYTStateChange(event) {
            // YT.PlayerState: -1 unstarted, 0 ended, 1 playing, 2 paused, 3 buffering, 5 cued
            if (event.data === 1) { // Playing
                setPlayState(true);
                loadingSpinner.classList.add('hidden');
            } else if (event.data === 2) { // Paused
                setPlayState(false);
                loadingSpinner.classList.add('hidden');
            } else if (event.data === 3) { // Buffering
                loadingSpinner.classList.remove('hidden');
            } else if (event.data === 0) { // Ended
                setPlayState(false);
                ytPlayer.seekTo(0, true);
                ytPlayer.pauseVideo();
                replayOverlay.classList.remove('hidden');
                replayOverlay.classList.add('flex');
            }
        }

        // Load YouTube Iframe API if not loaded
        if (!window.YT || !window.YT.Player) {
            if (!document.getElementById('yt-iframe-api-script')) {
                const tag = document.createElement('script');
                tag.id = 'yt-iframe-api-script';
                tag.src = "https://www.youtube.com/iframe_api";
                const firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            }
            const prevOnReady = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = function() {
                if (prevOnReady) prevOnReady();
                initYT();
            };
        } else {
            initYT();
        }
    }
})();
</script>
