<!DOCTYPE html>
<html lang="en">
<head>
	<base href="http://localhost/t2/">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/trongate.css">
	<title>Document</title>
</head>
<body>
	<div class="container">
		<h1>Page Headline</h1>
		<p>Lorem ipsum dolor sit amet, consectetur, adipisicing elit. Doloremque aspernatur doloribus excepturi sed facilis! Fuga nobis velit deleniti voluptatem architecto placeat, necessitatibus tenetur aliquid, esse ipsam, deserunt sequi iste. Ut!</p>

		<p>
			<button onclick="openModal('test-modal')">Open Modal</button>
		</p>

		<p>
  			<button onclick="openModal('code_generator', 800, 600)">Open iFrame Modal</button>
		</p>
	</div>

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



	</div>



	<script src="js/app.js"></script>
</body>
</html>