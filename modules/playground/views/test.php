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
		<h1 class="text-center">Test Form</h1>
		<?php
		$form_attr = [
			'class' => 'highlight-errors',
			'data-error-display' => 'validation_msgs/english'
		];
		echo form_open('playground/submit', $form_attr);
		echo form_label('First Name');
		echo validation_errors('first_name');
		echo form_input('first_name', $first_name, array('autocomplete' => 'off', 'placeholder' => 'Enter first name...'));

		echo form_label('Last Name');
		echo validation_errors('last_name');
		echo form_input('last_name', $last_name, array('autocomplete' => 'off', 'placeholder' => 'Enter last name...'));
		echo form_submit('submit', 'Submit');
		echo form_close();
		?>
	</div>
</body>
</html>