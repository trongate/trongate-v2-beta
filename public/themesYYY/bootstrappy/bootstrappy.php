<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/trongate.css">
    <link rel="stylesheet" href="<?= THEME_DIR ?>css/bootstrappy.css">
    <link rel="stylesheet" href="css/trongate-icons.css">
    <?= $additional_includes_top ?? '' ?>
    <title><?= $page_title ?? 'Admin Panel' ?></title>
</head>
<body>
    <header>
        <div class="header-lg">
            <div class="logo"><?= WEBSITE_NAME ?></div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#">Dashboard</a></li>
                    <li><a href="#">Users</a></li>
                    <li><a href="#">Content</a></li>
                    <li><a href="#">Settings</a></li>
                    <li><a href="#">System</a></li>
                    <li><a href="#">Analytics</a></li>
                </ul>
            </nav>
            <div class="sm">&nbsp;</div>
        </div>
    	<div class="header-sm">
    		<div class="hamburger">&#x2630;</div>
    		<div class="logo"><?= WEBSITE_NAME ?></div>
    		<div class="sm">x&nbsp;</div>
    	</div>
    </header>

    <div class="layout">
        <nav class="side-nav">
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Users</a></li>
                <li><a href="#">Content</a></li>
                <li><a href="#">Settings</a></li>
                <li><a href="#">System</a></li>
                <li><a href="#">Analytics</a></li>
            </ul>
        </nav>
        <main>
            <div class="center-stage container">
            <h1>Heading Level One This is a really big headline that occupies more than one line on the page.</h1>
            <h2>Welcome Heading Level Two This is a really big headline that occupies more than one line on the page.</h2>
            <h3>Heading Level Three This is a really big headline that occupies more than one line on the page.</h3>

            <p>
              Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur,
              suscipit. Accusantium recusandae libero, iure dolores vero repellat natus
              tempora pariatur eveniet at maxime amet illum mollitia possimus minima eos.
            </p>

            <p>
              This is a second paragraph to help test spacing, line heights, and font
              choices. Your stylesheet will make this shine — much like your perfectly kept
              hair, DC.
            </p>

            <ul>
              <li>Unordered list item one</li>
              <li>Unordered list item two</li>
              <li>Unordered list item three</li>
            </ul>

            <ol>
              <li>Ordered list item one</li>
              <li>Ordered list item two</li>
              <li>Ordered list item three</li>
            </ol>

            <a href="#">This is a link</a>

            <hr>

            <blockquote>
              “A sample blockquote to help you style citations and emphasised remarks.”
            </blockquote>

            <form>
              <label for="name">Name</label>
              <input id="name" type="text" placeholder="Your name">

              <label for="email">Email</label>
              <input id="email" type="email" placeholder="you@example.com">

              <label for="message">Message</label>
              <textarea id="message" rows="4"></textarea>

              <button type="submit">Submit</button>
            </form>

            <table>
              <thead>
                <tr>
                  <th>Column A</th>
                  <th>Column B</th>
                  <th>Column C</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Value 1</td>
                  <td>Value 2</td>
                  <td>Value 3</td>
                </tr>
                <tr>
                  <td>Value 4</td>
                  <td>Value 5</td>
                  <td>Value 6</td>
                </tr>
              </tbody>
            </table>

            <pre><code>function example() {
              console.log("Code block test.");
            }
            </code></pre>    
        </div>
        </main>
    </div>

<footer>
    <!-- Footer for large screens -->
    <div class="footer-lg">
        <div class="footer-left">
            &copy; 2025 Your Company Name
        </div>
        <div class="footer-center">
            <a href="#">About</a> | <a href="#">Contact</a> | <a href="#">Privacy</a>
        </div>
        <div class="footer-right">
            Follow us on:
            <a href="#">Twitter</a> | <a href="#">LinkedIn</a>
        </div>
    </div>

    <!-- Footer for small screens -->
    <div class="footer-sm">
        <div class="footer-top">
            &copy; <?= date('Y').' '.OUR_NAME ?>
        </div>
        <div class="footer-bottom">
            <a href="#">About</a> | <a href="#">Contact</a> | <a href="#">Privacy</a>
        </div>
    </div>
</footer>

    <?= $additional_includes_btm ?? '' ?>
</body>
</html>