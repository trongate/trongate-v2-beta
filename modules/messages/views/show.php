<h1><?= $headline ?> <span class="smaller hide-sm">(Record ID: <?= $update_id ?>)</span></h1>
<?= flashdata() ?>
<div class="card">
    <div class="card-heading">
        Options
    </div>
    <div class="card-body">
        <?php 
        echo anchor('messages/manage', 'View All Messages', array("class" => "button alt"));

        $attr = array( 
            "class" => "button alt",
            "id" => "btn-folder-modal",
            "onclick" => "openModal('folder-modal')"
        );

        echo form_button('messages/create/'.$update_id, 'Send To Another Folder', $attr);
        if ($sent_from>0) {
            echo anchor('messages/create/'.$update_id.'/'.$sent_from, 'Reply', array("class" => "button"));
        }

        $attr_delete = array( 
            "class" => "danger go-right",
            "id" => "btn-delete-modal",
            "onclick" => "openModal('delete-modal')"
        );
        echo form_button('delete', 'Delete', $attr_delete);
        ?>
    </div>
</div>
<div class="two-col">
    <div class="card record-details">
        <div class="card-heading">
            Message Details
        </div>
        <div class="card-body">
            <?= Modules::run('spam_blocker/_draw_modal_btns') ?>
            <div><span>Date Created</span><span><?= date('l jS F Y \a\t H:i', $date_created) ?></span></div>
            <div><span>Sender Email: </span><span id="this-sender-email"><?= $sender_email ?></span></div>
            <div><span>Sent From</span><span id="this-sender-name"><?= $sent_from_name ?></span></div>
            <div><span>Message Subject</span><span><?= $message_subject ?></span></div>
            <div><span>Message Body</span><br><br>
            <span style="display: block; width: 100%;"><?= nl2br($message_body) ?></span>
            <?php
            if (isset($data['reduced_message_body'])) {
                echo '<div id="this-sender-message" style="display: none">'.$data['reduced_message_body'].'</div>';
            }
            ?>
        </div>
        </div>
    </div>
    <div class="card">
        <div class="card-heading">
            Comments
        </div>
        <div class="card-body">
            <div class="text-center">
                <p><button class="alt" onclick="openModal('comment-modal')">Add New Comment</button></p>
                <div id="comments-block"><table></table></div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="comment-modal" style="display: none;">
    <div class="modal-heading"><i class="fa fa-commenting-o"></i> Add New Comment</div>
    <div class="modal-body">
        <p><textarea placeholder="Enter comment here..."></textarea></p>
        <p><?php
            $attr_close = array( 
                "class" => "alt",
                "onclick" => "closeModal()"
            );
            echo form_button('close', 'Cancel', $attr_close);
            echo form_button('submit', 'Submit Comment', array("onclick" => "submitComment()"));
            ?>
        </p>
    </div>
</div>
<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Record</div>
    <div class="modal-body">
        <?= form_open('messages/submit_delete/'.$update_id) ?>
        <p>Are you sure?</p>
        <p>You are about to delete a message record.  This cannot be undone.  Do you really want to do this?</p> 
        <?php 
        echo '<p>'.form_button('close', 'Cancel', $attr_close);
        echo form_submit('submit', 'Yes - Delete Now', array("class" => 'danger')).'</p>';
        echo form_close();
        ?>
    </div>
</div>


<div class="modal" id="folder-modal" style="display: none;">
    <div class="modal-heading"><i class="fa fa-commenting-o"></i> Send To Another Folder</div>
    <div class="modal-body">
        <?php
        $form_location = str_replace('/show/', '/submit_change_folder/', current_url());
        echo form_open($form_location);
        ?>
        <p><?= form_dropdown('new_folder', $message_folders, $message_folders_id) ?></p>
        <p><?php
            $attr_close = array( 
                "class" => "alt",
                "onclick" => "closeModal()"
            );
            echo form_button('close', 'Cancel', $attr_close);
            echo form_submit('submit', 'Submit');
            ?>
        </p>
        <?= form_close(); ?>
    </div>
</div>





<script>
var token = '<?= $token ?>';
var baseUrl = '<?= BASE_URL ?>';
var segment1 = '<?= segment(1) ?>';
var updateId = '<?= $update_id ?>';
var drawComments = true;
</script>