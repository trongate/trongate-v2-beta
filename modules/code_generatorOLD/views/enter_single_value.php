<p><?= $input_instructions ?></p>

<?php
$form_attr = [
	'mx-post' => 'code_generator/submit_'.$input_code,
	'mx-target' => '#center-stage',
	'mx-target-loading' => '#loading-message'
];

echo form_open('#', $form_attr);
echo form_input($input_code, '', array('class' => 'value-input', 'autocomplete' => 'off'));
echo form_submit('submit', 'Submit');
$from_url = str_replace(BASE_URL.'code_generator/', '', current_url());
echo form_hidden('from_url', $from_url);
echo form_close();
?>



<div id="loading-message" style="display: none;">
	<div class="spinner"></div>
</div>