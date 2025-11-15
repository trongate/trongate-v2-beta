<section class="force_tall">
  <div class="container">
    <h1>Module Request Details</h1>
    <p class="sm">REMINDER: The makers of Trongate are not involved in the agreement below. It's for the request creator and winning bidder to make contact and hopefully move forward with a successful project.  The makers of Trongate cannot assume any responsibility for the conduct, abilities or integrity of either the request creator or the winning bidder.  Having said that, we wish you both the best of luck!</p>
    <?= flashdata() ?>
    <div id="module-requests-grid">
      <div id="jobs-feed">
        <div class="card job-card">
          <div class="card-body">
            <h2>Deal Details</h2>

            <p style="margin-top:12px; text-transform: uppercase;"><b>Request Creator</b></p>
            <div class="two-col lg">
                <div><span class="sm">username: </span><b><?= $creator_username ?></b></div>
                <div><?= anchor($creator_profile_url, '<i class=\'fa fa-user-circle\'></i> View Profile', array('class' => 'button alt sm')) ?></div>
            </div>
            <div><p>Joined on <?= $creator_join_date ?></p></div>
            <div class="two-col lg" id="creator_email_address_row">
                <div id="creator_email_address">*********</div>
                <div><button class="sm" onclick="displayCreatorEmail()">Reveal Email Address</button></div>
            </div>
            <div id="creator_email_display" style="display: none"><span class="sm">email: </span><b><?= $creator_email_address ?></b></div>
            <hr>

            <p style="margin-top:12px; text-transform: uppercase;"><b>Winning Bidder</b></p>
            <div class="two-col lg">
                <div><span class="sm">username: </span><b><?= $winning_bidder_username ?></b></div>
                <div><?= anchor($winning_bidder_profile_url, '<i class=\'fa fa-user-circle\'></i> View Profile', array('class' => 'button alt sm')) ?></div>
            </div>
            <div><p>Joined on <?= $creator_join_date ?></p></div>
            <div class="two-col lg" id="winning_bidder_email_address_row">
                <div id="winning_bidder_email_address">*********</div>
                <div><button class="sm" onclick="displayBidderEmail()">Reveal Email Address</button></div>
            </div>
            <div id="winning_bidder_display" style="display: none"><span class="sm">email: </span><b><?= $winning_bidder_email_address ?></b></div>
            <hr>

            <p style="margin-top:12px; text-transform: uppercase;"><b>Agreed Payment Amount</b></p>
            <div style="margin-top:12px"><span class="sm">amount quoted: </span><b><?= $offer_value ?></b> <?= $offer_currency ?></div>
            <div><span class="sm">winning bidder's special terms: </span></div>
            <?php
            if ($offer_terms !== '') { ?>
            <div class="sm" style="margin-top: 12px"><p><i>&#8220;<?= nl2br($offer_terms) ?>&#8221;</i></p></div>
            <?php
            }
            ?>


          </div>
        </div>
      </div>
      <?php
      //display the original request details
      ?>
      <div id="job-details">
        <div class="newjob" style="display: none">new</div>
        <h2><?= $request_title ?></h2>
        <p class="bigger"><?= $creator_username ?></p>
        <p><b><i class="fa fa-money"></i> <span id="ask-amount"><?= $budget ?></span></b></p>
        <hr>
        <h2>Description</h2>
        <p><?= $request_details ?></p>
        <hr>
        <h2>Responses</h2>
        <div id="record-responses">
          <div class="spinner top_margin"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
const activeRequestCode = '<?= segment(3) ?>';
const baseUrl = '<?= BASE_URL ?>';

window.addEventListener('load', () => {
  fetchResponseFeedAlt();
});

function displayCreatorEmail() {
    const creatorEmailAddressRow = document.getElementById('creator_email_address_row');
    creatorEmailAddressRow.remove();
    const creatorEmailDisplay = document.getElementById('creator_email_display');
    creatorEmailDisplay.style.display = 'block';
}

function displayBidderEmail() {
    const bidderEmailAddressRow = document.getElementById('winning_bidder_email_address_row');
    bidderEmailAddressRow.remove();
    const bidderEmailDisplay = document.getElementById('winning_bidder_display');
    bidderEmailDisplay.style.display = 'block';
}
</script>