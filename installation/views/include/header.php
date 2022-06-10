<!DOCTYPE html>
<html lang="<?= $lang ?? '' ?>">
<head>
    <meta charset="utf-8"/>
    <title><?= INSTALL_TITLE ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <meta name="description" content="<?= INSTALL_DESC ?>">

    <link rel="icon" type="image/png"
          href="admin/resources/images/identity/logo_compact.png">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="admin/resources/vendors/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="admin/resources/css/adminlte.min.css">

    <link rel="stylesheet" href="admin/resources/css/main.css">

    <link rel="stylesheet" href="installation/views/assets/css/style.css">

    <script src="admin/resources/vendors/jquery/jquery.min.js"></script>

    <script src="admin/resources/js/main.js"></script>

    <style>
        code.dark {
            background: #2b2929;
            padding: 0 5px;
            border-radius: 3px;
        }
    </style>

</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light" id="navbarTop">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">

            <!-- DARKMODE -->
            <li class="nav-item">
                <a class="nav-link" href="#" id="darkModeToggle">
                    <i class="far fa-lightbulb" id="darkModeIcon"></i>
                </a>
            </li>

        </ul>
    </nav>