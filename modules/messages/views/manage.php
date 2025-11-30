<h1>Your <?= $folder_name ?></span></h1>
<?php
flashdata();
echo Modules::run('notifications/_display_notifications', $token);
echo '<p>';

$active_folder = segment(2);

if ($active_folder == 'manage') {
    $active_folder = 'inbox';
}

foreach($message_folders as $folder) {
    $folder_url_segment = url_title($folder->folder_title);
    $folder_url_segment = strtolower($folder_url_segment);
    $folder_url = BASE_URL.'messages/'.$folder_url_segment;

    if ($folder_url_segment == $active_folder) {
        $btn_class = 'button';
    } else {
        $btn_class = 'button alt';
    }

    echo anchor($folder_url, $folder->folder_title, array("class" => $btn_class));
}
echo '</p>';
echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="4">
                    <div>
                        <div><?php
                        echo form_open('messages/manage/1/', array("method" => "get"));
                        echo form_input('searchphrase', '', array("placeholder" => "Search records...", "autocomplete" => "off"));
                        echo form_submit('submit', 'Search', array("class" => "alt"));
                        echo form_close();
                        ?></div>
                        <div>Records Per Page: <?php
                        $dropdown_attr['onchange'] = 'setPerPage()';
                        echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr); 
                        ?></div>

                    </div>                    
                </th>
            </tr>
            <tr>
                <th>Date Created</th>
                <th>Sent From</th>
                <th>Message Subject</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { 
                if (isset($all_members[$row->sent_from])) {
                    $sent_from = $all_members[$row->sent_from];
                } else {
                    $sent_from = 'Unknown user';
                }

                settype($row->sent_from, 'int');
                settype($row->sent_to, 'int');
                settype($row->opened, 'int');

                if ($row->opened>0) {
                    $row_icon = '<i class="fa fa-envelope-open-o opened-envelope"></i> ';
                } else {
                    $row_icon = '<i class="fa fa-envelope gold-envelope"></i> ';
                }

                ?>
            <tr>
                <td><?= $row_icon.date('l jS F Y \a\t H:i', $row->date_created) ?></td>
                <td><?= $sent_from ?></td>
                <td><?= $row->message_subject ?></td>
                <td><?= anchor('messages/show/'.$row->id, 'View', $attr) ?></td>        
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php 
    if(count($rows)>9) {
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
    }
}
?>

<style>
.gold-envelope {
    color:  #bdab75;
}
</style>