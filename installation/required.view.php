<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../admin/resources/vendors/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../admin/resources/css/adminlte.min.css">

    <link rel="stylesheet" href="../admin/resources/css/main.css">

    <script src="../admin/resources/vendors/jquery/jquery.min.js"></script>

    <title>CraftMyWebsite | ERROR</title>
</head>
<body>

<div class="container">

    <h1 class="text-danger text-center">Erreur</h1>

    <p>CraftMyWebsite ne supporte pas votre version de PHP, merci de changer pour pouvoir installer le CMS.</p>
    <p>Votre version: <strong><?=phpversion()?></strong></p>
    <p>Version minimum <strong>7.4</strong></p>

    <div class="text-center mt-4">
        <button class="btn btn-success" onclick="location.reload()">Recommencer l'installation</button>
    </div>

</div>

</body>
</html>