<!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
  <div class="container-fluid">
    <div
      class="footer-content d-flex flex-wrap justify-content-between align-items-center py-3 flex-md-row flex-column">
      <div class="text-muted">
        ©
        <script>
          document.write(new Date().getFullYear());
        </script>
        , made with ❤️ by
        <a href="{{ config('app.author_url', 'https://pixinvent.com') }}" target="_blank" class="footer-link fw-medium">{{ config('app.author', 'ThemeSelection') }}</a>
      </div>
      <div class="d-flex flex-column flex-md-row gap-2 gap-md-0">
        <div class="d-flex flex-wrap gap-2">
          <a
            href="{{ config('app.license_url', '#') }}"
            class="footer-link me-0 me-md-4"
            target="_blank">License</a
          >
          <a
            href="{{ config('app.documentation_url', '#') }}"
            class="footer-link me-0 me-md-4"
            target="_blank">Documentation</a
          >
          <a
            href="{{ config('app.support_url', '#') }}"
            class="footer-link"
            target="_blank">Support</a
          >
        </div>
      </div>
    </div>
  </div>
</footer>
<!-- /Footer -->