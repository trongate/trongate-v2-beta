<!DOCTYPE html>
<html lang="en">
<head>
	<base href="<?= BASE_URL ?>">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/trongate.css">
	<link rel="stylesheet" href="css/trongate-icons.css">
	<title>Trongate Icons</title>
</head>
<body>
	<div class="container">
		<h1 class="text-center">Ahoy!</h1>
		<div class="icons-grid">
			<?php
			$icons = [
				'home',
				'sign-in',
				'sign-out',
				'user',
				'user-circle',
				'users',
				'envelope',
				'envelope-open',
				'star',
				'star-half',
				'shopping-basket',
				'shopping-cart'
			];
			
			foreach ($icons as $icon) {
				echo '<div><i class="fa fa-' . $icon . '"></i> <i class="tg tg-' . $icon . '"></i></div>';
			}
			?>
		</div>
		<div class="massive-icon">
			<?php
			$lastIcon = end($icons);
			?>
			<div><i class="tg tg-<?= $lastIcon ?>"></i></div>
		</div>
	</div>
<style>
.icons-grid {
	max-width: 760px;
	display: grid;
	gap: 1em;
	grid-template-columns: repeat(4, 1fr);
	margin: 0 auto;
}
.icons-grid > div {
	border: 1px silver solid;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 1em;
}
.massive-icon {
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 33em;
}
.massive-icon i {
	border: 1px silver solid;
}
</style>
</body>
</html>