<!DOCTYPE html>
<html lang="en">
<head>
  <base href="<?= BASE_URL ?>">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/trongate.css">
  <title>Welcome to Trongate</title>

</head>
<body>
  <main>
    <div class="container">
    <h1>Welcome to Trongate</h1>
    <h2>You’re all set — let’s build something extraordinary</h2>

    <p>
      <button onclick="openModal('test-modal')">Open Modal</button>
    </p>

    <p>
        <button onclick="openModal('code_generator', 800, 600)">Open iFrame Modal</button>
    </p>

<div class="modal" id="test-modal" style="display: none">
    <div class="modal-heading">Hello</div>
    <div class="modal-body">
        <p>Goodbye</p>
    </div>
    <div class="modal-footer">
        <button class="alt" onclick="closeModal()">Cancel</button>
        <button onclick="closeModal()">OK</button>
    </div>
</div>

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
<?php
/*
<script>
window.addEventListener('load', (ev) => {
   const button = document.querySelector('button');
   button.click();
});
</script>
*/
?>      
    </div>




    <footer>
      <p class="text-center"><a class="github-link" href="https://github.com/trongate/trongate-framework" target="_blank">⭐ Please Give Trongate A Star on GitHub</a></p>
    </footer>
  </main>
  <script src="js/admin.js"></script>
</body>
</html>