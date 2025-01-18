<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>LifeSure - Life Insurance Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Inter:slnt,wght@-10..0,100..900&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="/guest/lib/animate/animate.min.css" />
    <link href="/guest/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="/guest/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="/guest/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="/guest/css/style.css" rel="stylesheet">

    @livewireStyles

    <style>
        * {
            font-family: "Inter", sans-serif;
        }
    </style>
    @stack('styles')

    @vite([])
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar & Hero Start -->
    <div class="container-fluid nav-bar px-0 px-lg-4 py-lg-0">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a href="#" class="navbar-brand p-0">
                    <h5 class="text-primary fw-bold mb-0">
                        {{ $setting->name }}
                    </h5>
                    <!-- <img src="/guest/img/logo.png" alt="Logo"> -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars">

                    </span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">

                    <div class="navbar-nav mx-0 mx-lg-auto">
                        <x-guest-nav></x-guest-nav>
                    </div>

                </div>
                <div class="d-none d-xl-flex flex-shrink-0 ps-4">
                    <a href="#" class="btn btn-light btn-lg-square rounded-circle position-relative wow tada"
                        data-wow-delay=".9s">
                        <i class="fa fa-phone-alt fa-2x">

                        </i>
                        <div class="position-absolute" style="top: 7px; right: 12px;">
                            <span>
                                <i class="fa fa-comment-dots text-secondary">
                                </i>
                            </span>
                        </div>
                    </a>
                    <div class="d-flex flex-column ms-3">
                        <span>Call to Our Experts</span>
                        <a href="tel:+ 0123 456 7890">
                            <a href="https://api.whatsapp.com/send/?phone=628978301766&text&type=phone_number&app_absent=0"
                                class="text-dark">
                                Free: {{ $setting->phone }}
                            </a>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar & Hero End -->

    <!-- Navbar & Hero Start -->
    {{ $slot }}
    <!-- Navbar & Hero End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4 text-center">
        <span class="text-body">
            <a href="#" class="border-bottom text-white text-decoration-none">
                <i class="fas fa-copyright text-light me-2">
                </i>{{ $setting->description }}
        </span>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square rounded-circle back-to-top">
        <i class="fa fa-arrow-up">
        </i>
    </a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="/guest/lib/wow/wow.min.js">
    </script>
    <script src="/guest/lib/easing/easing.min.js">
    </script>
    <script src="/guest/lib/waypoints/waypoints.min.js">
    </script>
    <script src="/guest/lib/counterup/counterup.min.js">
    </script>
    <script src="/guest/lib/lightbox/js/lightbox.min.js">
    </script>
    <script src="/guest/lib/owlcarousel/owl.carousel.min.js">
    </script>


    <!-- Template Javascript -->
    <script src="/guest/js/main.js">
    </script>

    @stack('scripts')
    @livewireScripts

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>
    <x-livewire-alert::scripts />
</body>

</html>
