<section class="force_tall">
    <div class="container container-xs">
        <h1><?= $page_headline ?></h1>
        <h3 class="text-center">Got an idea for a module?  Would you like somebody else to build it for you? Let's make it happen!</h3>
        <?php

        if (segment(3) !== '') {
            echo '<p class="text-right">';
            $delete_attr['type'] = 'button';
            $delete_attr['class'] = 'button-danger';
            $delete_attr['onclick'] = 'openModal(\'delete-request-modal\')';
            echo form_button('delete', '<i class=\'fa fa-trash\'></i>', $delete_attr);
            echo '</p>';
        }

        echo form_open($form_location, array('class' => 'highlight-errors'));
        echo form_label('<b>Request Title:</b>');
        echo validation_errors('request_title');
        echo '<div class="extra-info">';
        echo 'Give your module request a meaningful title, e.g., \'Shopping Cart Wanted\'.';
        echo '</div>';
        echo form_input('request_title', $request_title, array('placeholder' => 'Add a title here...', 'autocomplete' => 'off'));

        echo form_label('<b>What\'s Your Budget:</b>');
        echo validation_errors('budget');
        echo '<div class="extra-info">';
        echo 'It\'s okay to request a free build, however, if you offer to pay then you will receive more responses.';
        echo '</div>';

        echo form_dropdown('budget', $budget_options, $selected_budget_index);

        echo form_label('<b>What Would You To Have Built?:</b>');
        echo validation_errors('request_details');
        echo '<div class="extra-info">';
        echo 'There is no need to go into precise details.  For now, a general description will be fine. ';
        echo 'If interested developers have questions or comments, relating to your post, then you\'ll be able to exchange messages after your post goes live.';
        echo '</div>';
        echo form_textarea('request_details', $request_details, array('placeholder' => 'Add a title here...', 'rows' => 5, 'placeholder' => 'Use this space to describe what you would like to have built...'));

        echo validation_errors('understood');
        echo '<div>';
        echo form_checkbox('understood', 1, $understood);
        echo ' If I get ripped off then I won\'t blame the makers of Trongate.';
        echo '</div>';

        echo form_submit('submit', 'Submit', array('class' => 'button'));
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));

        echo form_close();
        ?>
    </div>
</section>



<div class="modal" id="delete-request-modal" style="display: none">
    <div class="modal-heading"><i class="fa fa-trash"></i> Delete Module Request</div>
    <div class="modal-body">
        <h3 class="text-center">Are You Sure?</h3>
        <p>You are about to delete your module request.  This cannot be undone.</p>
        <?php
        echo form_open('module_requests/submit_conf_delete/'.segment(3));
        echo '<p class="text-center">';
        echo form_submit('submit', '<i class=\'fa fa-trash\'></i> Delete Record Now', array('class' => 'button-danger'));
        echo form_button('cancel', 'Cancel', array('onclick' => 'closeModal()', 'class' => 'alt'));
        echo '</p>';
        echo form_close();
        ?></p>
    </div>
</div>


<style>
.extra-info {
    bxackground-color: #dde4aa;
    cxolor: #2e3f4d;
    color: #dde4aa;
    margin: 4px 0;
    font-size: 15px;
}
</style>