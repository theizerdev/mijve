document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el elemento qr-reader existe en la página
    const qrReaderElement = document.getElementById('qr-reader');

    if (qrReaderElement) {
        // Cargar la biblioteca Html5-QRCode dinámicamente
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
        script.onload = function() {
            // Inicializar el escáner QR después de cargar la biblioteca
            initializeQrScanner();
        };
        document.head.appendChild(script);
    }
});

function initializeQrScanner() {
    const qrReaderElement = document.getElementById('qr-reader');

    if (!qrReaderElement || typeof Html5Qrcode === 'undefined') {
        console.error('Elemento qr-reader o Html5Qrcode no disponible');
        return;
    }

    // Configuración del escáner
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    // Crear instancia del escáner
    const html5QrCode = new Html5Qrcode('qr-reader');

    // Función para iniciar el escáner
    function startScanner() {
        html5QrCode.start(
            { facingMode: "environment" }, 
            config,
            (decodedText, decodedResult) => {
                // Enviar el resultado escaneado al componente Livewire
                if (window.Livewire) {
                    window.Livewire.dispatch('qr-scanned', { qrData: decodedText });
                }

                // Reproducir sonido de éxito
                playSound('success');

                // Pausar el escáner brevemente para evitar múltiples escaneos
                html5QrCode.pause();
                setTimeout(() => {
                    html5QrCode.resume();
                }, 2000);
            },
            (errorMessage) => {
                // Manejar errores silenciosamente (para no saturar la consola)
                // console.error(errorMessage);
            }
        ).catch((err) => {
            console.error(`Error al iniciar el escáner: ${err}`);
            showErrorMessage('No se pudo acceder a la cámara. Asegúrate de dar los permisos necesarios.');
        });
    }

    // Función para detener el escáner
    function stopScanner() {
        html5QrCode.stop().then(() => {
            console.log('Escáner detenido correctamente');
        }).catch((err) => {
            console.error(`Error al detener el escáner: ${err}`);
        });
    }

    // Iniciar el escáner cuando el componente se muestra
    startScanner();

    // Detener el escáner cuando el componente se oculta o se navega away
    window.addEventListener('beforeunload', () => {
        stopScanner();
    });

    // Detectar cambios en el modo de escaneo (cámara/manual)
    const scanModeToggle = document.querySelector('input[wire\:model\.live="scanMode"]');
    if (scanModeToggle) {
        scanModeToggle.addEventListener('change', function() {
            if (this.checked) {
                // Modo cámara activado
                startScanner();
            } else {
                // Modo manual activado
                stopScanner();
            }
        });
    }
}

// Función para reproducir sonidos
function playSound(type) {
    // Crear un elemento de audio para reproducir el sonido
    const audio = new Audio();

    // Determinar qué sonido reproducir según el tipo
    switch(type) {
        case 'success':
            audio.src = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT';
            break;
        case 'error':
            audio.src = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT';
            break;
        case 'notification':
            audio.src = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT';
            break;
        default:
            return; // No reproducir nada si el tipo no es reconocido
    }

    // Reproducir el sonido
    audio.play().catch(error => {
        console.error('Error al reproducir el sonido:', error);
    });
}

// Función para mostrar mensajes de error
function showErrorMessage(message) {
    // Crear un elemento toast para mostrar el error
    const toast = document.createElement('div');
    toast.className = 'toast error';
    toast.textContent = message;

    // Añadir estilos al toast
    toast.style.backgroundColor = '#f44336';
    toast.style.color = 'white';
    toast.style.padding = '12px 24px';
    toast.style.borderRadius = '4px';
    toast.style.boxShadow = '0 3px 5px rgba(0,0,0,0.2)';
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.left = '50%';
    toast.style.transform = 'translateX(-50%)';
    toast.style.zIndex = '9999';
    toast.style.maxWidth = '80%';
    toast.style.textAlign = 'center';

    // Añadir el toast al DOM
    document.body.appendChild(toast);

    // Animar la entrada
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transition = 'opacity 0.3s ease';
    }, 10);

    // Eliminar el toast después de 5 segundos
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 5000);
}

// Escuchar eventos de Livewire para reproducir sonidos
document.addEventListener('livewire:init', () => {
    Livewire.on('play-sound', (type) => {
        playSound(type);
    });

    Livewire.on('show-error', (message) => {
        showErrorMessage(message);
    });

    Livewire.on('show-success', (message) => {
        // Crear un elemento toast para mostrar el éxito
        const toast = document.createElement('div');
        toast.className = 'toast success';
        toast.textContent = message;

        // Añadir estilos al toast
        toast.style.backgroundColor = '#4CAF50';
        toast.style.color = 'white';
        toast.style.padding = '12px 24px';
        toast.style.borderRadius = '4px';
        toast.style.boxShadow = '0 3px 5px rgba(0,0,0,0.2)';
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.left = '50%';
        toast.style.transform = 'translateX(-50%)';
        toast.style.zIndex = '9999';
        toast.style.maxWidth = '80%';
        toast.style.textAlign = 'center';

        // Añadir el toast al DOM
        document.body.appendChild(toast);

        // Animar la entrada
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transition = 'opacity 0.3s ease';
        }, 10);

        // Eliminar el toast después de 3 segundos
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    });
});
