<?php
$my_trongate_user_id = ($member_obj == false) ? 0 : (int) $member_obj->trongate_user_id;
?>
<section class="force_tall">
  <div class="container">
    <h1>Module Requests</h1>
    <?= flashdata() ?>
    <div id="module-requests-grid">
      <div id="jobs-feed">
        <?php
          $counter = 0;
          foreach($published_requests as $published_request) {
            $counter++;
        ?>
        <div class="card job-card">
          <div class="card-body">
            <?php
            if ($published_request->new == true) {
              echo '<div class="newjob">new</div>';
            }
            ?>
            <h2 onclick="loadRecord(<?= $counter ?>)"><?= $published_request->request_title ?></h2>
            <p class="bigger"><?= $published_request->created_by_username ?></p>
            <p><b><i class="fa fa-money"></i> <?= $published_request->budget ?></b></p>

            <?php
            if ($published_request->respond_invite !== '') {
              echo '<p class="job-ad-notification"><i class="fa fa-star"></i> ';
              echo $published_request->respond_invite;
              echo '</p>';
            }
            ?>
            
            <p class="job-desc-short"><?= nl2br($published_request->request_details_short) ?></p>
            <p class="smaller">Posted <?= date('l jS F Y', $published_request->date_created) ?></b></p>
          </div>
        </div>
        <?php
          }
        ?>
      </div>
      <?php
      //clarify the details of request to be showcased
      $showcase_record = $published_requests[0];
      ?>
      <div id="job-details">
        <div class="newjob" style="display: none">new</div>
        <h2><?= $showcase_record->request_title ?></h2>
        <p class="bigger"><?= $showcase_record->created_by_username ?></p>
        <p><b><i class="fa fa-money"></i> <span id="ask-amount"><?= $showcase_record->budget ?></span></b></p>
        <p class="job-ad-notification" id="special-info"></p>
        <?php
        if ($member_obj == false) {
        //draw fake buttons
        ?>
        <p><?php
           echo anchor('module_requests/login_required/offer', 'Make An Offer', array('class' => 'button'));
           echo anchor('module_requests/login_required/ask', 'Ask A Question', array('class' => 'button alt'));
          ?></p>
        <?php
        //draw REAL buttons
        } else {
        ?>
        <p id="action-btns" style="display: none"><?php
          echo form_button('Make An Offer', 'Make An Offer', array('class' => 'button', 'onclick' => 'initMakeOffer()'));
          echo form_button('Ask A Question', 'Ask A Question', array('class' => 'button alt', 'onclick' => 'initAskQuestion()'));
        ?></p>
        <?php
        }
        ?>
        <hr>
        <h2>Description</h2>
        <p><?= $showcase_record->request_details ?></p>
        <hr>
        <h2>Responses</h2>
        <div id="record-responses">
          <div class="spinner top_margin"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<div class="modal" id="accept-modal" style="display:none">
  <div class="modal-heading">Accept Offer Confirmation</div>
  <div class="modal-body">
    <h3 class="text-center">Are Are Sure?</h3>
    <p class="text-left" style="text-align: left;"><b>Here's What Happens After You Accept An Offer:</b></p>
    <ol class="sm">
      <li>Your module request gets automatically closed.  This means that nobody will be able to post additional offers or questions.</li>
      <li>You will be able to view the email address of the person who posted the winning offer.</li>
      <li><b>Your email address will be made available to the person who posted the winning offer.</b></li>
      <li>It's then for you and the person who posted the winning offer to get in touch with each other and figure out how you'd like to move forward.</li>
    </ol>
    <p><b>As a reminder, the makers of Trongate are not involved in this deal in any way.  We don't take commission. If something goes wrong, the makers of Trongate cannot accept responsibility.</b></p>
    <?= form_open('module_requests/submit_accept_offer') ?>
    <p>
      <button type="submit" name="submit" class="green-btn">I Understand And Accept The Offer</button>
      <button type="button" class="alt" onclick="closeModal()">Cancel</button>
      <?php 
        echo form_hidden('request_code', '', array('id' => 'active_request_code'));
        echo form_hidden('response_code', '', array('id' => 'accepted_response_code'));
        ?>
    </p>
    <?= form_close() ?>
  </div>
</div>
<script>
const myTrongateUserId = <?= $my_trongate_user_id ?>;
const rows = <?= json_encode($published_requests) ?>;
const baseUrl = '<?= BASE_URL ?>';
let activeRequestCode = '<?= $active_request_code ?>';
let createdBy = <?= $created_by ?>;
let isNew = '<?= $is_new ?>'; //is the first record new?

window.addEventListener('load', () => {
  initDrawActionBtns();
  attemptDrawIsNew();
  fetchResponseFeed();
});
</script>