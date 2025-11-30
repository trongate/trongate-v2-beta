<!DOCTYPE html>
<html lang="en">
<head>
  <base href="<?= BASE_URL ?>">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/trongate_lean.css">
  <link rel="stylesheet" href="css/milligram.min.css">
  <title>Welcome to Trongate</title>

</head>
<body>
  <main>
    <div class="container">
    <h1>Welcome to Trongate</h1>
    <h2>You’re all set — let’s build something extraordinary</h2>

    <p style="display: none;">
      <button onclick="openModal('test-modal')">Open Modal</button>
    </p>

    <p>
        <button onclick="openModal('code_generator', 800, 600)">Code Generator</button>
        <button onclick="openModal('code_generator', 800, 600)">Query Builder</button>
        <button onclick="openModal('code_generator', 800, 600)">Module Market</button>
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