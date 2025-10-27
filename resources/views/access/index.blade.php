<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('access/vite.svg') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema de Control de Acceso - Recepcionista</title>
    <meta name="description" content="Sistema de control de acceso para estudiantes mediante códigos QR">
    
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="{{ asset('access/materialize/css/core.css') }}">
    
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <link rel="manifest" href="{{ asset('access/manifest.json') }}">
    <meta name="theme-color" content="#1976d2"/>
    <link rel="apple-touch-icon" href="{{ asset('access/logo192.png') }}">
    <script type="module" crossorigin src="{{ asset('access/assets/index-BcjhokND.js') }}"></script>
    <link rel="stylesheet" crossorigin href="{{ asset('access/assets/index-kFlDE7jn.css') }}">
  </head>
  <body>
    <div id="root"></div>
    
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('{{ asset('access/sw.js') }}')
            .then(registration => {
              console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
              console.log('SW registration failed: ', registrationError);
            });
        });
      }
    </script>
  </body>
</html>