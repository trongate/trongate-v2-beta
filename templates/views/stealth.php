<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/trongate.css">
    <link rel="stylesheet" href="css/tg-stealth.css">
    <script src="js/trongate-mx.min.js"></script>
    <?= $additional_includes_top ?>
    <title>Trongate - The native PHP framework</title>
</head>
<body>
    <?php if (isset($breadcrumbs) && !empty($breadcrumbs)) : ?>
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <?php $total = count($breadcrumbs); ?>
                    <?php foreach ($breadcrumbs as $index => $crumb) : ?>
                        <?php $is_last = ($index === $total - 1); ?>
                        <li <?= $is_last ? 'aria-current="page"' : '' ?>>
                            <?php if ($is_last): ?>
                                <?= htmlspecialchars($crumb['title']) ?>
                            <?php else: ?>
                                <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['title']) ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
    
    <?= Template::display($data) ?>
</body>
</html>