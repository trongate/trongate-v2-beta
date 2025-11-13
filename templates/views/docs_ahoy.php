<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="http://localhost/trongate_live5/css/themes/prism-atom-dark.css">
    <link rel="stylesheet" href="css/prism.css">

    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/skeleton.css">
    <link rel="stylesheet" href="documentation_module/css/docs_ahoy.css">
    <title>Document</title>
</head>
<body>
    <div class="docs-container">
        <?= Template::partial('partials/breadcrumbs', $data) ?>
        <?= Template::display($data) ?>
    </div>
    <?= Modules::run('documentation/_render_theme_color_css', $theme_color) ?>
<script src="js/prism.js"></script>
<script src="js/docs-ahoy.js"></script>
</body>
</html>
