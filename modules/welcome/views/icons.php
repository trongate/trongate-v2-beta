<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <title>Icon Preview</title>
    <style>
        /* Grid layout for icons */
        .icons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        /* Icon container */
        .icon-item {
            text-align: center;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .icon-item div {
            margin-top: 5px;
            font-size: 0.9em;
        }

        /* Masked icon base class */
        .tg {
            display: inline-block;
            width: 1em;
            height: 1em;
            background-color: currentColor;
            -webkit-mask-size: contain;
            -webkit-mask-repeat: no-repeat;
            -webkit-mask-position: center;
            mask-size: contain;
            mask-repeat: no-repeat;
            mask-position: center;
        }

        /* Size classes */
        .xl { font-size: 2em; }
        .lg { font-size: 1.5em; }
        .sm { font-size: 0.85em; }
        .xs { font-size: 0.7em; }
    </style>

    <?php
    // directory containing your SVG icons
    $icon_dir = APPPATH . '/public/trongate-icons';
    $icons = glob($icon_dir . '/*.svg');

    $css_rules = [];

    foreach ($icons as $icon_path) {
        $icon_file = basename($icon_path);
        $icon_name = pathinfo($icon_file, PATHINFO_FILENAME);
        $icon_url  = BASE_URL . 'public/trongate-icons/' . $icon_file;

        $css_rules[] = ".tg-$icon_name {
            -webkit-mask-image: url('$icon_url');
            mask-image: url('$icon_url');
        }";
    }
    ?>

    <style>
        <?= implode("\n", $css_rules); ?>
    </style>
</head>
<body>

<h1 style="text-align:center; font-family: Arial, sans-serif;">Icon Preview</h1>

<div class="icons-grid">
<?php foreach ($icons as $icon_path): ?>
    <?php
        $icon_file = basename($icon_path);
        $icon_name = pathinfo($icon_file, PATHINFO_FILENAME);
        // Example: Assign a random size class for demonstration
        $sizes = ['xl','lg','sm','xs','']; 
        //$size_class = $sizes[array_rand($sizes)];
        $size_class = 'xl';
    ?>
    <div class="icon-item">
        <i class="tg tg-<?= $icon_name ?> <?= $size_class ?>"></i>
        <div><?= $icon_name ?> <?= $size_class ? "($size_class)" : "" ?></div>
    </div>
<?php endforeach; ?>
</div>

</body>
</html>
