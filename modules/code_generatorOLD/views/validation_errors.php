<?php
echo validation_errors('<div class="validation-error">', '</div>');

$input_attr = [
    'mx-get' => 'code_generator/'.$from_url,
    'mx-target' => '#center-stage'
];

echo form_button('try_again', 'Try Again', $input_attr);