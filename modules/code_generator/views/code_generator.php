<!DOCTYPE html>
<html lang="en">
<head>
	<base href="<?= BASE_URL ?>">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="code_generator_module/css/code_generator.css">
	<script src="js/trongate-mx.min.js"></script>
	<title>Code Generator</title>
</head>
<body>
	<div class="modal-container">
	    <header>*** Trongate ***</header>
	    <main>
	        <div class="loader mx-indicator" style="display: none;"><img src="code_generator_module/images/loader.svg"></div>
	        <div id="center-stage"><?= Template::display($data) ?></div>
	    </main>
	    <footer>
	    	<div>*</div>
	    	<div>hide</div>
	    	<div>*</div>
	    	<div onclick="renderLoader()">reset</div>
	    	<div>*</div>
	    	<div onclick="parent.closeModal()">quit</div>
	    	<div>*</div>
	    </footer>		
	</div>
<script src="code_generator_module/js/code_generator.js"></script>
</body>
</html>