/**
 * QR Access
 */

'use strict';

(function() {
    // Variables
    let html5QrcodeScanner = null;
    let livewireComponent = null;
    let initialized = false;

    // Función para manejar el éxito del escaneo
    function onScanSuccess(decodedText, decodedResult) {
        // Emitir evento a Livewire con el resultado del escaneo
        if (livewireComponent) {
            livewireComponent.call('processQrScan', decodedText);
        }

        // Opcional: detener el escáner después de un escaneo exitoso
        // html5QrcodeScanner.stop();
    }

    // Función para manejar errores de escaneo
    function onScanFailure(error) {
        // Manejar errores de escaneo (opcional)
        console.warn(`Código QR error = ${error}`);
    }

    // Inicializar el escáner cuando el modo de cámara está activo
    function initQrScanner() {
        if (!livewireComponent) return;

        const scanMode = livewireComponent.get('scanMode');

        if (scanMode === 'camera') {
            if (!html5QrcodeScanner && !initialized) {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader",
                    { 
                        fps: 10, 
                        qrbox: {width: 250, height: 250},
                        aspectRatio: 1.0
                    }
                );
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                initialized = true;
            }
        } else {
            // Detener el escáner si cambia a modo manual
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
                initialized = false;
            }
        }
    }

    // Inicialización cuando el DOM está listo
    document.addEventListener('DOMContentLoaded', function() {
        // Esperar a que Livewire se inicialice
        setTimeout(() => {
            // Obtener la instancia del componente Livewire
            livewireComponent = Livewire.first();

            if (livewireComponent) {
                initQrScanner();

                // Manejar eventos de sonido
                window.addEventListener('play-sound', function(event) {
                    const soundType = event.detail;
                    let soundElement = null;

                    switch(soundType) {
                        case 'beep':
                        case 'success':
                            soundElement = document.getElementById('beep-sound');
                            break;
                        case 'error':
                            soundElement = document.getElementById('error-sound');
                            break;
                        case 'notification':
                            soundElement = document.getElementById('notification-sound');
                            break;
                    }

                    if (soundElement && livewireComponent.get('soundEnabled')) {
                        soundElement.play().catch(e => console.error('Error al reproducir sonido:', e));
                    }
                });

                // Escuchar cambios en el modo de escaneo
                Livewire.hook('message.processed', (message, component) => {
                    initQrScanner();
                });
            }
        }, 500);
    });
})();
