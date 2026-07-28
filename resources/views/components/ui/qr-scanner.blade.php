@props([
    'onScan' => '$wire.scanCheckInWithCode(code)',
])

<div
    x-data="{
        scanning: false,
        processing: false,
        html5QrCode: null,
        lastScannedCode: null,
        lastScanTime: 0,
        errorMessage: null,
        isUnsecureContext: false,
        httpsUrl: '',
        cameras: [],
        selectedCameraId: '',

        async init() {
            if (window.location.protocol === 'http:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                this.isUnsecureContext = true;
                this.httpsUrl = 'https://' + window.location.host + window.location.pathname + window.location.search;
            }

            if (typeof Html5Qrcode === 'undefined') {
                await this.loadScript().catch(() => {});
            }
        },

        loadScript() {
            return new Promise((resolve, reject) => {
                if (document.getElementById('html5-qrcode-script')) {
                    resolve();
                    return;
                }
                const script = document.createElement('script');
                script.id = 'html5-qrcode-script';
                script.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
                script.onload = () => resolve();
                script.onerror = () => reject(new Error('Failed to load QR scanner script'));
                document.head.appendChild(script);
            });
        },

        async startScanner() {
            this.errorMessage = null;

            if (this.isUnsecureContext) {
                this.errorMessage = '{{ __('Camera access requires HTTPS or localhost. Click below to switch to HTTPS or run `herd secure` in terminal.') }}';
                return;
            }

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.errorMessage = '{{ __('Camera API is not supported by your browser or connection.') }}';
                return;
            }

            this.scanning = true;
            await this.$nextTick();

            try {
                if (typeof Html5Qrcode === 'undefined') {
                    await this.loadScript();
                }

                if (this.html5QrCode) {
                    try {
                        await this.html5QrCode.stop();
                        await this.html5QrCode.clear();
                    } catch (e) {}
                    this.html5QrCode = null;
                }

                this.html5QrCode = new Html5Qrcode('qr-reader-container');

                try {
                    this.cameras = await Html5Qrcode.getCameras();
                } catch (e) {
                    this.cameras = [];
                }

                let cameraConfig = { facingMode: 'environment' };

                if (this.selectedCameraId) {
                    cameraConfig = { deviceId: { exact: this.selectedCameraId } };
                }

                await this.html5QrCode.start(
                    cameraConfig,
                    {
                        fps: 15,
                        qrbox: (viewfinderWidth, viewfinderHeight) => {
                            const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                            const qrboxSize = Math.floor(minEdge * 0.7);
                            return { width: qrboxSize, height: qrboxSize };
                        },
                        aspectRatio: 1.0,
                    },
                    (decodedText) => this.handleDecodedText(decodedText),
                    () => {}
                );
            } catch (err) {
                console.error('Camera Scanner Initialization Error:', err);
                if (err && (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError' || String(err).includes('Permission denied'))) {
                    this.errorMessage = '{{ __('Camera permission was blocked. Please grant camera access in your browser settings and try again.') }}';
                } else if (err && (err.name === 'NotFoundError' || String(err).includes('No camera'))) {
                    this.errorMessage = '{{ __('No camera hardware found on your device.') }}';
                } else {
                    this.errorMessage = '{{ __('Unable to start camera stream. Please ensure no other app is using your camera.') }}';
                }
                this.scanning = false;
            }
        },

        async stopScanner() {
            if (this.html5QrCode && this.scanning) {
                try {
                    await this.html5QrCode.stop();
                    await this.html5QrCode.clear();
                } catch (e) {
                    console.error('Error stopping scanner:', e);
                }
                this.scanning = false;
                this.processing = false;
                this.html5QrCode = null;
            }
        },

        async switchCamera(deviceId) {
            this.selectedCameraId = deviceId;
            if (this.scanning) {
                await this.stopScanner();
                await this.startScanner();
            }
        },

        async handleDecodedText(decodedText) {
            const now = Date.now();

            // Lock out duplicate triggers while processing or within 4 seconds
            if (this.processing || (decodedText === this.lastScannedCode && (now - this.lastScanTime) < 4000)) {
                return;
            }

            this.processing = true;
            this.lastScannedCode = decodedText;
            this.lastScanTime = now;
            this.playBeep();

            // Pause QR frame decoding while processing check-in
            if (this.html5QrCode && typeof this.html5QrCode.pause === 'function') {
                try {
                    this.html5QrCode.pause(true);
                } catch (e) {}
            }

            const code = decodedText.trim();
            try {
                await {{ $onScan }};
            } catch (err) {
                console.error('Scan check-in execution error:', err);
            }

            // Keep loading overlay visible for 1.8s to give visual feedback and prevent accidental double scans
            setTimeout(() => {
                this.processing = false;
                if (this.html5QrCode && typeof this.html5QrCode.resume === 'function') {
                    try {
                        this.html5QrCode.resume();
                    } catch (e) {}
                }
            }, 1800);
        },

        playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0.1, ctx.currentTime);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.15);
            } catch (e) {}
        }
    }"
    x-on:unmounted="stopScanner()"
    class="space-y-3"
>
    <style>
        #qr-reader-container video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 0.75rem;
        }
    </style>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <flux:button
            x-show="!scanning"
            x-on:click="startScanner()"
            variant="primary"
            icon="camera"
            class="bg-purple-600 hover:bg-purple-500 text-white"
        >
            {{ __('Start Camera Scanner') }}
        </flux:button>

        <flux:button
            x-show="scanning"
            x-on:click="stopScanner()"
            variant="danger"
            icon="x-mark"
        >
            {{ __('Stop Camera Scanner') }}
        </flux:button>

        <template x-if="scanning && cameras.length > 1">
            <select
                x-on:change="switchCamera($event.target.value)"
                class="text-xs rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 px-2.5 py-1.5 focus:outline-none"
            >
                <template x-for="cam in cameras" :key="cam.id">
                    <option :value="cam.id" :selected="cam.id === selectedCameraId" x-text="cam.label || 'Camera ' + cam.id"></option>
                </template>
            </select>
        </template>
    </div>

    <!-- HTTP Warning Box if accessing via HTTP -->
    <div x-show="isUnsecureContext" x-cloak class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 space-y-2 text-xs text-amber-800 dark:text-amber-200">
        <div class="flex items-center gap-2 font-bold text-amber-900 dark:text-amber-100">
            <flux:icon name="shield-exclamation" class="size-4 shrink-0 text-amber-600" />
            <span>{{ __('HTTPS Required for Camera Access') }}</span>
        </div>
        <p>
            {{ __('Modern web browsers block camera access over unsecure HTTP connections.') }}
        </p>
        <ul class="list-disc list-inside space-y-1 font-medium pl-1">
            <li>{{ __('Click to switch to HTTPS URL: ') }} <a :href="httpsUrl" class="font-bold underline text-purple-700 dark:text-purple-400" x-text="httpsUrl"></a></li>
            <li>{{ __('If using Laravel Herd, run') }} <code class="bg-amber-100 dark:bg-amber-900/60 px-1.5 py-0.5 rounded font-mono font-bold">herd secure</code> {{ __('in your project directory.') }}</li>
        </ul>
    </div>

    <!-- Error Message Alert -->
    <div x-show="errorMessage" x-cloak class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 text-xs text-red-700 dark:text-red-300 flex flex-col gap-2">
        <div class="flex items-center gap-2 font-bold">
            <flux:icon name="exclamation-circle" class="size-4 shrink-0 text-red-500" />
            <span>{{ __('Camera Scanner Warning') }}</span>
        </div>
        <p x-text="errorMessage" class="font-medium"></p>
    </div>

    <!-- Camera Viewport Box -->
    <div
        x-show="scanning"
        x-cloak
        class="relative overflow-hidden rounded-xl border-2 border-purple-500/40 dark:border-purple-800/60 bg-black max-w-md mx-auto aspect-square shadow-md"
    >
        <div id="qr-reader-container" class="w-full h-full"></div>

        <!-- Processing Loading Overlay -->
        <div
            x-show="processing"
            x-cloak
            class="absolute inset-0 bg-purple-950/85 backdrop-blur-xs z-20 flex flex-col items-center justify-center gap-3 text-white transition-all duration-200"
        >
            <div class="size-12 rounded-full border-4 border-white/20 border-t-purple-400 animate-spin"></div>
            <div class="font-bold text-sm tracking-wide text-center px-4">
                {{ __('Processing Check-In...') }}
            </div>
            <div class="text-xs text-purple-200/80 font-mono" x-text="lastScannedCode ? lastScannedCode.substring(0, 18) + '...' : ''"></div>
        </div>

        <div x-show="!processing" class="absolute bottom-2 left-2 right-2 bg-black/60 backdrop-blur-xs text-white text-[11px] px-2.5 py-1 rounded-md text-center font-medium z-10 pointer-events-none">
            {{ __('Point camera at attendee digital ticket QR code') }}
        </div>
    </div>
</div>
