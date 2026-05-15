<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>@yield('title')</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Add these lines in the head section -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/SansPro/SansPro.min.css') }}">
    <script src="https://cdn.tiny.cloud/1/ffwdbcjhyfw4al7yr7y1e8shivh4g9nuipefj3gwz8y9s8h8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

    @if (App::getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/custom_rtl.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/admin/css/mycustomstyle.css') }}">
    @yield('css')
</head>
<body class="hold-transition sidebar-mini">
    <?php $user = auth()->user(); ?>
    <div class="wrapper">
        <!-- Navbar -->
        @include('admin.includes.navbar')
        <!-- Main Sidebar Container -->
        @include('admin.includes.sidebar')
        <!-- Content Wrapper. Contains page content -->
        @include('admin.includes.content')
        <!-- Footer -->
        @include('admin.includes.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/general.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize TinyMCE for all textareas with class 'rich-text'
        tinymce.init({
            selector: 'textarea.rich-text',
            height: 300,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function (editor) {
                editor.on('init', function () {
                    var textarea = document.getElementById(editor.id);
                    if (textarea) textarea.removeAttribute('required');
                });
            }
        });

        // Form submission handler
        $('form').on('submit', function(e) {
            var form = this;
            var isValid = true;

            // Sync TinyMCE content
            tinymce.triggerSave();

            // Check all required TinyMCE fields
            $('textarea.rich-text[required]').each(function() {
                var textarea = $(this);
                if (textarea.val().trim() === '') {
                    isValid = false;
                    textarea.addClass('is-invalid');
                    if (textarea.next('.invalid-feedback').length === 0) {
                        textarea.after('<div class="invalid-feedback">This field is required.</div>');
                    }
                } else {
                    textarea.removeClass('is-invalid');
                    textarea.next('.invalid-feedback').remove();
                }
            });

            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({ scrollTop: $('.is-invalid:first').offset().top - 100 }, 'slow');
            }
        });

        // Remove error class on input
        $(document).on('input', '.tox-textarea', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });
    });
    </script>
    @stack('scripts')
    @yield('script')
    @yield('js')
</body>
</html>