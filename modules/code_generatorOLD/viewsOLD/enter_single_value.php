<p><?= $input_instructions ?></p>

<?php
$form_attr = [
	'mx-post' => 'code_generator/submit_value',
	'mx-target' => '#center-stage'
];

echo form_open('#', $form_attr);
echo form_input($input_code, '', array('class' => 'value-input', 'autocomplete' => 'off'));
echo form_submit('submit', 'Submit');
$from_url = str_replace(BASE_URL.'code_generator/', '', current_url());
echo form_hidden('from_url', $from_url);
echo form_close();
?>