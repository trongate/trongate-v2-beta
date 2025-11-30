<h1>Unpublished Request Responses</h1>

<?php
echo flashdata();
$num_rows = count($rows);
if ($num_rows>0) { ?>
    <table class="dark-tbl">
      <thead>
        <tr>
          <th>Response Type</th>
          <th>Date Created</th>
          <th>Created By</th>
          <th>Status</th>
          <th class="text-center">Details</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $index = -1;
        foreach($rows as $record_obj) { 
           $index++;
        ?>
        <tr>
          <td><?= $record_obj->response_type ?></td>
          <td><?= date('l jS F Y', $record_obj->date_created) ?></td>
          <td><?= $record_obj->username ?></td>
          <td><?php
          if (isset($all_status_options[$record_obj->status])) {
            echo $all_status_options[$record_obj->status];
          } else {
            echo 'Unknown';
          }
          ?></td>
          <td><button onclick="openDetails('<?= $index ?>')">View</button></td>
        </tr>
        <?php
        }
        ?>       
      </tbody>
    </table>
<?php
}
?>

<div class="modal" id="response-details-modal" style="display:none">
    <div class="modal-heading">Response Details</div>
    <div class="modal-body">
        <div class="response-details-grid">
            <div class="row">
                <div>Status</div>
                <div id="record_status_title"></div>
            </div>
            <div class="row">
                <div>Username</div>
                <div id="record_username"></div>
            </div>
            <div class="row">
                <div>Response Type</div>
                <div id="record_response_type"></div>
            </div>
            <div class="row">
                <div>Offer Value</div>
                <div id="record_offer_value"></div>
            </div>
            <div class="row">
                <div>Offer Currency</div>
                <div id="record_offer_currency"></div>
            </div>
            <div class="row">
                <div>Comment</div>
                <div></div>
            </div>
            <div id="record_comment"></div>
        </div>
    </div>
    <p class="text-center">
        <button onclick="initViewRequest()">View Request</button>
        <button onclick="initApprove()" class="success">Approve</button>
        <button onclick="initDelete()" class="danger">Delete</button>
        <button class="alt" onclick="closeModal()">Close Window</button>
    </p>
    <div>&nbsp;</div>
</div>

<style>
.dark-tbl th {
    background-color: #333;
    color: #fff;
}

.response-details-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    grid-gap: 1em;
}

.response-details-grid > div {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
}

.response-details-grid > div > div:nth-child(odd) {
    font-weight: bold;
}
</style>

<script>
let currentIndex = 0;
const allRows = <?= json_encode($rows) ?>

function openDetails(i) {
    currentIndex = i;
    openModal('response-details-modal');
    populateDetails(i);
}

function populateDetails(i) {
    const currentRecord = allRows[i];
    record_status_title.innerHTML = allRows[i]['status_title'];
    record_username.innerHTML = allRows[i]['username'];
    record_response_type.innerHTML = allRows[i]['response_type'];
    record_offer_value.innerHTML = allRows[i]['offer_value'];
    record_offer_currency.innerHTML = allRows[i]['offer_currency'];
    record_comment.innerHTML = allRows[i]['comment'];
}

function initViewRequest() {
    const currentRecord = allRows[currentIndex];
    const moduleRequestUrl = currentRecord['module_request_url'];
    window.location.href = moduleRequestUrl;
}

function initApprove() {
    const currentRecord = allRows[currentIndex];

    const form = document.createElement('form');
    form.style.display = 'none';
    form.setAttribute('method', 'post');
    form.setAttribute('action', '<?= BASE_URL ?>module_request_responses/submit_approve');
    const body = document.getElementsByTagName('body')[0];
    body.appendChild(form);

    const formInput = document.createElement('input');
    formInput.setAttribute('name', 'response_id');
    formInput.setAttribute('value', currentRecord['id']);
    form.appendChild(formInput);

    const formSubmit = document.createElement('button');
    formSubmit.setAttribute('type', 'submit');
    formSubmit.setAttribute('name', 'approve');
    formSubmit.setAttribute('value', 'Approve');
    formSubmit.innerHTML = 'Submit';
    form.appendChild(formSubmit);

    formSubmit.click();
}

function initDelete() {
    const currentRecord = allRows[currentIndex];

    const form = document.createElement('form');
    form.style.display = 'none';
    form.setAttribute('method', 'post');
    form.setAttribute('action', '<?= BASE_URL ?>module_request_responses/submit_conf_delete');
    const body = document.getElementsByTagName('body')[0];
    body.appendChild(form);

    const formInput = document.createElement('input');
    formInput.setAttribute('name', 'response_id');
    formInput.setAttribute('value', currentRecord['id']);
    form.appendChild(formInput);

    const formSubmit = document.createElement('button');
    formSubmit.setAttribute('type', 'submit');
    formSubmit.setAttribute('name', 'delete');
    formSubmit.setAttribute('value', 'Delete');
    formSubmit.innerHTML = 'Submit';
    form.appendChild(formSubmit);

    formSubmit.click();
}
</script>