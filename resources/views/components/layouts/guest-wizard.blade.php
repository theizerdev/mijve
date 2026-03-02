@props(['title' => 'Registro de Participante'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-wide customizer-hide"
  dir="ltr"
  data-skin="default"
  data-bs-theme="light"
  data-assets-path="/materialize/assets/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — {{ 'mijve' }}</title>
    <meta name="description" content="{{ config('app.name') }} - Registro de Participante">

    <!-- Favicon -->
   <link rel="icon" type="image/x-icon" href="/logo/favicon.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />

    <link rel="stylesheet" href="/materialize/assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="/materialize/assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="/css/system.css" />
    <script src="/materialize/assets/vendor/libs/@algolia/autocomplete-js.js"></script>
    <link rel="stylesheet" href="/materialize/assets/vendor/libs/pickr/pickr-themes.css" />
    <link rel="stylesheet" href="/materialize/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="/materialize/assets/css/demo.css" />
    <link rel="stylesheet" href="/materialize/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- BS Stepper -->
    <link rel="stylesheet" href="/materialize/assets/vendor/libs/bs-stepper/bs-stepper.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="/materialize/assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="/materialize/assets/vendor/js/helpers.js"></script>
    <script src="/materialize/assets/vendor/js/template-customizer.js"></script>
    <script src="/materialize/assets/js/config.js"></script>

    @stack('styles')
    @livewireStyles
  </head>

  <body>
    {{ $slot }}

    <!-- Core JS -->
    <script src="/materialize/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="/materialize/assets/vendor/libs/popper/popper.js"></script>
    <script src="/materialize/assets/vendor/js/bootstrap.js"></script>
    <script src="/materialize/assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="/materialize/assets/vendor/libs/@algolia/autocomplete-js.js"></script>
    <script src="/materialize/assets/vendor/libs/pickr/pickr.js"></script>
    <script src="/materialize/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/materialize/assets/vendor/libs/hammer/hammer.js"></script>
    <script src="/materialize/assets/vendor/libs/i18n/i18n.js"></script>
    <script src="/materialize/assets/vendor/js/menu.js"></script>

    <!-- BS Stepper -->
    <script src="/materialize/assets/vendor/libs/bs-stepper/bs-stepper.js"></script>

    <!-- Main JS -->
    <script src="/materialize/assets/js/main.js"></script>

    @stack('scripts')
    @livewireScripts
  </body>
</html>
