/**
 * LaravelRetail barcode scanner — camera (html5-qrcode) + USB wedge keyboard input.
 */
(function (global) {
    'use strict';

    let scannerInstance = null;
    let wedgeEnabled = false;
    let wedgeBuffer = '';
    let wedgeLastKeyAt = 0;
    let wedgeCallback = null;

    function loadScript(src) {
        return new Promise((resolve, reject) => {
            if (document.querySelector(`script[src="${src}"]`)) {
                resolve();
                return;
            }
            const s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    function beep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 880;
            gain.gain.value = 0.05;
            osc.start();
            setTimeout(() => osc.stop(), 80);
        } catch (e) { /* ignore */ }
    }

    function onWedgeKeydown(e) {
        if (!wedgeEnabled || !wedgeCallback) return;
        if (e.ctrlKey || e.altKey || e.metaKey) return;
        const tag = (e.target && e.target.tagName) || '';
        if (['TEXTAREA'].includes(tag)) return;

        const now = Date.now();
        if (now - wedgeLastKeyAt > 120) {
            wedgeBuffer = '';
        }
        wedgeLastKeyAt = now;

        if (e.key === 'Enter') {
            const code = wedgeBuffer.trim();
            wedgeBuffer = '';
            if (code.length >= 3) {
                e.preventDefault();
                beep();
                wedgeCallback(code);
            }
            return;
        }

        if (e.key.length === 1) {
            wedgeBuffer += e.key;
        }
    }

    const LaravelRetailBarcode = {
        async openCamera(onScan) {
            const modalEl = document.getElementById('barcodeScannerModal');
            if (!modalEl) {
                alert('Barcode scanner UI not loaded.');
                return;
            }

            await loadScript('https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js');

            const statusEl = document.getElementById('barcodeScannerStatus');
            const readerId = 'barcode-qr-reader';
            statusEl.textContent = 'Starting camera…';

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();

            const onDetected = (decodedText) => {
                const code = decodedText.trim();
                if (!code) return;
                beep();
                statusEl.textContent = 'Scanned: ' + code;
                this.stopCamera();
                modal.hide();
                onScan(code);
            };

            modalEl.addEventListener('hidden.bs.modal', () => this.stopCamera(), { once: true });

            try {
                if (scannerInstance) {
                    await scannerInstance.stop();
                    scannerInstance.clear();
                }

                scannerInstance = new Html5Qrcode(readerId, { verbose: false });
                const cameras = await Html5Qrcode.getCameras();
                const cameraId = cameras.length ? cameras[cameras.length - 1].id : { facingMode: 'environment' };

                const formats = window.Html5QrcodeSupportedFormats
                    ? [
                        Html5QrcodeSupportedFormats.EAN_13,
                        Html5QrcodeSupportedFormats.EAN_8,
                        Html5QrcodeSupportedFormats.UPC_A,
                        Html5QrcodeSupportedFormats.UPC_E,
                        Html5QrcodeSupportedFormats.CODE_128,
                        Html5QrcodeSupportedFormats.CODE_39,
                        Html5QrcodeSupportedFormats.ITF,
                        Html5QrcodeSupportedFormats.QR_CODE,
                    ]
                    : undefined;

                await scannerInstance.start(
                    cameraId,
                    {
                        fps: 12,
                        qrbox: { width: 300, height: 140 },
                        aspectRatio: 1.5,
                        formatsToSupport: formats,
                    },
                    onDetected,
                    () => {}
                );
                statusEl.textContent = 'Point camera at barcode…';
            } catch (err) {
                statusEl.textContent = 'Camera error: ' + (err.message || err);
                console.error(err);
            }
        },

        async stopCamera() {
            if (scannerInstance) {
                try {
                    await scannerInstance.stop();
                    scannerInstance.clear();
                } catch (e) { /* ignore */ }
                scannerInstance = null;
            }
        },

        enableWedge(onScan) {
            wedgeCallback = onScan;
            if (!wedgeEnabled) {
                wedgeEnabled = true;
                document.addEventListener('keydown', onWedgeKeydown, true);
            }
        },

        disableWedge() {
            wedgeEnabled = false;
            wedgeCallback = null;
            wedgeBuffer = '';
            document.removeEventListener('keydown', onWedgeKeydown, true);
        },

        async lookupBarcode(url, code) {
            const res = await fetch(url + '?code=' + encodeURIComponent(code), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || 'Item not found');
            }
            return data;
        },
    };

    global.LaravelRetailBarcode = LaravelRetailBarcode;
})(window);
