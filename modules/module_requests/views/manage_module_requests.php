<h1>Manage Module Requests</h1>
<p><?= $unpublished_info ?></p>
<p><?php
$btn1_text = '<i class=\'fa fa-tag\'></i> Status Options';
$btn2_text = '<i class=\'fa fa-comments-o\'></i> Request Responses';
echo anchor('module_request_status/manage', $btn1_text, array('class' => 'button alt'));
echo anchor('module_request_responses/manage', $btn2_text, array('class' => 'button alt'));
?>
</p>
<?php
if ($num_unpublished>0) {
    ?>

    <table class="dark-tbl">
      <thead>
        <tr>
          <th>Title</th>
          <th>Date Created</th>
          <th>Created By</th>
          <th class="text-center">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach($unpublished as $record_obj) { ?>
        <tr>
          <td><b><?= anchor('module_requests/show/'.$record_obj->code, $record_obj->request_title) ?></b></td>
          <td><?= $record_obj->date_created ?></td>
          <td><?= $record_obj->created_by ?></td>
          <td><?= $record_obj->published ?></td>
        </tr>
        <?php
        }
        ?>       
      </tbody>
    </table>

    <?php
}
?>
<hr>
<h2>Published Module Requests</h2>
<p><?= $published_info ?></p>
<?php
if ($num_published>0) {
    ?>

    <table class="dark-tbl">
      <thead>
        <tr>
          <th>Title</th>
          <th>Date Created</th>
          <th>Created By</th>
          <th class="text-center">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach($published as $record_obj) { ?>
        <tr>
          <td><b><?= anchor('module_requests/show/'.$record_obj->code, $record_obj->request_title) ?></b></td>
          <td><?= $record_obj->date_created ?></td>
          <td><?= $record_obj->created_by ?></td>
          <td><?= $record_obj->published ?></td>
        </tr>
        <?php
        }
        ?>       
      </tbody>
    </table>

    <?php
}
?>


<style>
.dark-tbl th {
    background-color: #333;
    color: #fff;
}
</style>