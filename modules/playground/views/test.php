<!DOCTYPE html>
<html lang="en">
<head>
	<base href="<?= BASE_URL ?>">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/trongate.css">
	<title>Document</title>
</head>
<body>
	<div class="container">
		<h1 class="text-center">Headline</h1>
		<?= Modules::run('pagination/display', $pagination_data) ?>
		<?= json($rows[0]) ?>
	</div>
</body>
</html>