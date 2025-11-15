<section class="force_tall">
    <div class="container container-xxs">
        <h1>Not Allowed</h1>
        <p class="text-center">Since you have already received at least one response, modifying of your original request is not permitted. If you want, you can delete your request.</p>

        <p class="text-center"><button class="danger button-danger" onclick="openModal('delete-request-modal')"><i class="fa fa-trash"></i> Delete Your Request</button></p>

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



        <p class="text-center neon_glow"><?= anchor('module_requests/browse/'.segment(3), 'Return To Your Module Request', array('class' => 'button alt')) ?></p>
    </div>
</section>