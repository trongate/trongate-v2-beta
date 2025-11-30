<h1>Module Request <?= $module_request_code ?></h1>
<?= flashdata() ?>
<p><?= anchor('module_requests/manage', '<i class="fa fa-arrow-left"></i> Go Back', array('class' => 'button')) ?></p>

<table class="dark-tbl">
    <thead>
        <tr>
            <th colspan="2">Request Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Date Created</td>
            <td><?= date('l jS F Y', $date_created) ?></td>
        </tr>
        <tr>
            <td style="width:200px">Request Title</td>
            <td><?= $request_title ?></td>
        </tr>
        <tr>
            <td>Created By</td>
            <td><?= anchor($create_by_url, $created_by_username) ?></td>
        </tr>
        <tr>
            <td>Budget</td>
            <td><?= $budget ?></td>
        </tr>
        <tr>
            <td>Published</td>
            <td><?= ($published == 1) ? 'yes' : 'no' ?> <button class="alt sm" onclick="openModal('publish-modal')"><?php
            if ($published == 1) {
                echo 'Unpublish';
            } else {
                echo 'Publish';
            }
        ?></button></td>
        </tr>
        <tr>
            <td>Open</td>
            <td><?= ($open == 1) ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <td style="vertical-align:top">Description</td>
            <td><?= nl2br($request_details) ?></td>
        </tr>
    </tbody>
</table>


<div class="modal" style="display:none" id="publish-modal">
    <div class="modal-heading">Change Publish Status</div>
    <div class="modal-body">
        <p><?php
        if ($published == 1) {
            echo 'The request is currently published.';
            $btn_text = 'Unpublish Request';
        } else {
            echo 'The request is not currently published';
            $btn_text = 'Publish Request';
        }
        ?></p>
        <p class="text-center"><?php
           echo form_open('module_requests/submit_change_publish_status/'.segment(3));
           echo '<p class="text-center">';
           echo form_submit('submit', $btn_text);
           echo form_button('cancel', 'Cancel', array('class' => 'button alt', 'onclick' => 'closeModal()'));
           echo '</p>';
           echo form_close();
    ?></p>
    </div>
</div>


<style>
.dark-tbl th {
    background-color: #333;
    color: #fff;
}

.dark-tbl {
    max-width: 760px;
}

.dark-tbl tr > td:nth-child(1) {
    font-weight: bold;
}

.dark-tbl tr > td:nth-child(2) {
    text-align: left;
}
</style>