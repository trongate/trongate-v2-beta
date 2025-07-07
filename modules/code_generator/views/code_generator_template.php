<!DOCTYPE html>
<html lang="en">
<head>
	<base href="<?= $api_base_url ?>">
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>code_generator_module/css/code_generator.css">
	<script src="js/trongate-mx.min.js"></script>
	<title>Document</title>
</head>
<body>
	<div class="spinner"></div>
	<script>
	const localBaseUrl = '<?= BASE_URL ?>';
	const apiBaseUrl = '<?= $api_base_url ?>';
	const cssPath = '<?= $api_base_url ?>t2_api-code_generator_module/css/code_generator.css';
	const jsPath = '<?= $api_base_url ?>t2_api-code_generator_module/js/code_generator.js';
	</script>
	<script src="<?= BASE_URL ?>code_generator_module/js/code_generator.js"></script>
</body>
</html>