<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Panel' ?></title>
    <link rel="stylesheet" href="css/trongate.css">
</head>
<body>
    <header style="background: #333; color: white; padding: 1rem;">
        <h1>Admin Template</h1>
        <nav>
            <a href="<?= BASE_URL ?>" style="color: white; margin-right: 1rem;">Home</a>
            <a href="<?= BASE_URL ?>welcome/hello" style="color: white;">Hello</a>
        </nav>
    </header>
    
    <main style="padding: 2rem;">
        <?= display($data) ?>
    </main>
    
    <footer style="background: #333; color: white; padding: 1rem; margin-top: 2rem;">
        <p>&copy; <?= date('Y') ?> Admin Panel</p>
    </footer>
</body>
</html>