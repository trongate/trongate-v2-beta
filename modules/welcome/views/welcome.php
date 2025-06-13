<!DOCTYPE html>
<html lang="en">
<head>
  <base href="<?= BASE_URL ?>">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome to Trongate</title>
  <style>
    :root {
      --brand: #000080;
      --text: #1a1a1a;
      --muted: #666;
      --bg: #fff;
      --button-bg: #000080;
      --button-alt: #333;
      --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: var(--font);
      background: var(--bg);
      color: var(--text);
      padding: 2em;
      line-height: 1.6;
    }

    main {
      max-width: 720px;
      margin: auto;
      text-align: center;
    }

    h1 {
      font-size: 2.5em;
      color: var(--brand);
      margin-bottom: 0.25em;
    }

    h2 {
      font-size: 1.2em;
      font-weight: normal;
      color: var(--muted);
      margin-bottom: 1.5em;
    }

    .datetime {
      font-size: 0.85em;
      color: var(--muted);
      margin-bottom: 2em;
      text-align: center;
    }

    p {
      text-align: left;
      margin-bottom: 1.5em;
    }

    strong {
      color: var(--brand);
    }

    .cta-buttons {
      margin: 2em 0;
    }

    button {
      font-size: 1em;
      padding: 0.75em 1.5em;
      border: none;
      border-radius: 4px;
      margin: 0.5em;
      cursor: pointer;
      background: var(--button-bg);
      color: white;
      transition: background 0.3s ease;
    }

    button.alt {
      background: var(--button-alt);
    }

    button:hover {
      background: #222266;
    }

    ul {
      list-style: none;
      padding-left: 0;
      text-align: left;
      display: inline-block;
      margin-top: 1em;
      margin-bottom: 2em;
    }

    ul li {
      margin-bottom: 0.75em;
    }

    footer {
      font-size: 0.9em;
      color: var(--muted);
      margin-top: 3em;
    }

    .text-center {
      text-align: center;
    }

    a {
      color: var(--brand);
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    .github-link {
      display: inline-block;
      margin-top: 2em;
      font-weight: bold;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
  </style>
</head>
<body>
  <main>
    <h1>Welcome to Trongate</h1>
    <h2>You’re all set — let’s build something extraordinary</h2>

    <p class="datetime"><?= $formatted_datetime ?? $datetime ?></p>

    <p>
      You’ve just installed <strong>Trongate</strong>, a modern PHP framework engineered for performance, simplicity, and zero fluff. It’s fast, lean, and completely free from the bloat of over-engineered code or needless dependencies.
    </p>

    <p>
      This page is yours to change. You can modify it, delete it, or make it the foundation of your next big idea.  Don't forget to check out the documentation at <a href="https://trongate.io/documentation">https://trongate.io/documentation</a>
    </p>

    <div class="cta-buttons">
      <button onclick="window.location='tg-admin';">Admin Panel</button>
      <button class="alt" onclick="window.location='https://trongate.io/documentation';">Documentation</button>
    </div>

    <p>
      To begin, log into your <a href="tg-admin">admin panel</a>. Default credentials:
    </p>

    <ul>
      <li><strong>Username:</strong> admin</li>
      <li><strong>Password:</strong> admin</li>
    </ul>

    <footer>
      <p class="text-center"><a class="github-link" href="https://github.com/trongate/trongate-framework" target="_blank">⭐ Please Give Trongate A Star on GitHub</a></p>
    </footer>
  </main>
</body>
</html>