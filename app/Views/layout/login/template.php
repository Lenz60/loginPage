<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwindcss -->
    <link rel="stylesheet" href="/css/app.css">
    <title><?= $title; ?></title>
</head>


<body class="bg-sky-600">
    <?= $this->renderSection('content'); ?>
</body>

</html>