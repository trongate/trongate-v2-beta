<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/trongate.css">
    <link rel="stylesheet" href="css/tg-stealth.css">
    <script src="js/trongate-mx.min.js"></script>
    <title>Trongate - The native PHP framework</title>
</head>
<body>
    <section class="text-center container">
        <img src="images/trongate_logo_black.webp" alt="Trongate PHP framework logo">
        <h1 class="text-center mt-0">The Native PHP Framework</h1>

        <div class="hp-links">
            <div>
                <ul>
                    <?php
                    $attr = [
                      'mx-get' => 'welcome/learn_trongate',
                      'mx-build-modal' => json_encode([
                        'id' => 'add-element-modal',
                        'modalHeading' => 'Learn Trongate',
                        'showCloseButton' => 'true'
                      ])
                    ];
                    ?>
                    <li><?php 
                    //anchor('#', 'Learn Trongate', $attr);
                    echo anchor('documentation', 'Documentation');
                    ?></li>
                    <li><?= anchor('learning-zone', 'The Learning Zone') ?></li>
                    <li><?= anchor('#', 'Discussion Forums') ?></li>
                    <li><?= anchor('#', 'Sponsors') ?></li>
                </ul>
            </div>
            <div>
                <ul>
                    <li><?= anchor('https://github.com/trongate/trongate-framework/', 'Download Trongate', array('target' => '_blank')) ?></li>
                    <li><?= anchor('coming_soon', 'Module Market') ?></li>
                    <li><?= anchor('https://github.com/trongate/trongate-framework', 'The GitHub Repo', array('target' => '_blank')) ?></li>
                    <li><?= anchor('#', 'Get In Touch') ?></li>
                </ul>
            </div>
        </div>

        <style>
            body {
                color: var(--primary-darker);
            }

            h1 {
                font-size: 27px;
            }

            img {
                margin: 5em auto 1em auto;
                max-width: 360px;
            }

            .hp-links {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1em;
                max-width: 520px;
                margin: 3em auto;
                left: 2em;
                position: relative;
                font-size: 1.2em;
            }

            .hp-links ul {
                margin: 0;
                padding: 0;
            }

            .hp-links ul li {
                text-align: left;
                margin-top: 0.7em;
            }
        </style>
    </section>
</body>
</html>
