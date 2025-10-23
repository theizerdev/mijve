<!DOCTYPE html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-skin="default"
  data-bs-theme="light"
  data-assets-path="{{ asset('materialize/assets/') }}/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ config('app.name') }} - {{ config('app.description') }}" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('materialize/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="/materialize/assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css -->

    <link rel="stylesheet" href="/materialize/assets/vendor/libs/node-waves/node-waves.css" />

    <script src="/materialize/assets/vendor/libs/@algolia/autocomplete-js.js"></script>

    <link rel="stylesheet" href="/materialize/assets/vendor/libs/pickr/pickr-themes.css" />

    <link rel="stylesheet" href="/materialize/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="/materialize/assets/css/demo.css" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="/materialize/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <link rel="stylesheet" href="/materialize/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="/materialize/assets/vendor/libs/swiper/swiper.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="/materialize/assets/vendor/css/pages/cards-statistics.css" />

    <!-- Helpers -->
    <script src="/materialize/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js. -->
    <script src="/materialize/assets/vendor/js/template-customizer.js"></script>

    <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file. -->

    <script src="/materialize/assets/js/config.js"></script>

     <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

  <style>
    .photo-preview-card {
      transition: transform 0.2s;
    }

    .photo-preview-card:hover {
      transform: scale(1.02);
    }

    .camera-feed {
      background: #000;
      border-radius: 0.5rem;
    }

    .capture-btn {
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
  </style>

    @stack('styles')
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        @include('components.partials.menu')

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @include('components.partials.navbar')

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-fluid flex-grow-1 container-p-y">
              {{ $slot }}
            </div>
            <!-- /Content -->

            <!-- Footer -->
            @include('components.partials.footer')

            <div class="content-backdrop fade"></div>
          </div>
          <!-- /Content wrapper -->
        </div>
        <!-- /Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- /Layout wrapper -->

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

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="/materialize/assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="/materialize/assets/vendor/libs/swiper/swiper.js"></script>

    <!-- Main JS -->

    <script src="/materialize/assets/js/main.js"></script>


   <!-- Cropper.js JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

    @stack('scripts')
  </body>
</html>
