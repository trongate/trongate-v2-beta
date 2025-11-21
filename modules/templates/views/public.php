<!DOCTYPE html>
<html lang="en">
<head>
	<base href="<?= BASE_URL ?>">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/trongate.css">
	<title>Public Template</title>
</head>
<body>
	<div class="container">
		<?= Modules::run('templates/display', $data) ?>
	</div>
</body>
</html>