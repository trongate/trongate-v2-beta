<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/themes/prism-atom-dark.css">
    <link rel="stylesheet" href="css/prism.css">
    <link href='//fonts.googleapis.com/css?family=Raleway:400,300,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/trongate.css">
    <link rel="stylesheet" href="documentation_module/css/docs_ahoy.css">
    <title>Document</title>
</head>
<body>
    <div class="docs-container">
        <?= Template::partial('partials/breadcrumbs', $data) ?>
        <div class="page-content"><?= Template::display($data) ?></div>
    </div>
    <?= Modules::run('documentation/_render_theme_color_css', $theme) ?>
<script src="js/prism.js"></script>
<script src="documentation_module/js/docs-ahoy.js"></script>
</body>
</html>
