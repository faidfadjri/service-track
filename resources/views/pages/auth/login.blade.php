<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
        integrity="sha384-b6lVK+yci+bfDmaY1u0zE8YYJt0TZxLEAFyYSLHId4xoVvsrQu3INevFKo+Xir8e" crossorigin="anonymous">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/login.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/mobile/login.css') }}">
</head>

<body class="center-column">

    <div class="main-container">
        <form id="login-form" class="center-column">
            <div class="logo-wrap center-row">
                <img class="logo" src={{ asset('assets/img/logo.png') }}></img>
                <span class="title">Service Tracking</span>
            </div>
            <div class="form-wrap center-column">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" required class="form-control" class="form-control"
                        placeholder="Masukan username" name="username" id="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" required class="form-control" placeholder="Masukan password" name="password"
                        id="password">
                </div>
                <button class="btn-login" type="submit">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Masuk
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
        integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#login-form').submit(function(event) {
                event.preventDefault();

                $('.spinner-border').removeClass('d-none');

                $.ajax({
                    url: '/login',
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('.spinner-border').addClass('d-none');
                        window.location.href = '/admin';
                    },
                    error: function(xhr, status, error) {
                        // Hide the spinner if there's an error
                        $('.spinner-border').addClass('d-none');

                        console.log(xhr, status, error);

                        if (xhr.status === 401) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Masuk',
                                text: 'Username atau password salah!',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Masuk',
                                text: error,
                            });
                        }
                    }
                });
            });
        });
    </script>

</body>

</html>
