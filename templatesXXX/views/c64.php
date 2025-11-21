<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="desktop_app_api_module/css/trongate-code-generator.css">
    <script src="js/trongate-mx.min.js"></script>
    <title>Trongate</title>
</head>
<body>
    <div class="blue-frame">
        <header>*** Trongate ***</header>
        <main><?= Template::display($data) ?></main>
        <footer>
            <div>*</div>
            <div onclick="skipAhead()">Skip</div>

            <div>*</div>
            <div onclick="window.parent.CodeGenerator.reset()">Reset</div>
            <div>*</div>
            <div onclick="skipAhead()">Settings</div>
            <div>*</div>
            <div onclick="TrongateCodeGenerator.initQuit()">Close</div>
            <div>*</div>
        </footer>
    </div>
<script src="desktop_app_api_module/js/trongate-code-generator.js"></script>
<?php
if ($view_file === 'home') {
echo '<script>TrongateCodeGenerator.clearLocalStorage()</script>';
}
?>
</body>
</html>
