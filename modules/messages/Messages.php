<?php
class Messages extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100); 

    function inbox() {
        redirect('messages/manage');
    }   

    function _draw_admin_top_rhs($data) {

        if ((segment(1) == 'messages') && (segment(2) == 'manage')) {
            $num_unopened = 0;
            $message_rows = $data['rows'];
            foreach($message_rows as $row) {
                $message_folders_id = (int) $row->message_folders_id;
                if ($message_folders_id == 1) {
                    $opened = (int) $row->opened;
                    if ($opened == 0) {
                        $num_unopened++;
                    }
                }
            }

        } else {
            $num_unopened = $this->_count_num_opened_for_admin();
        }

        $data['view_module'] = 'messages';
        $data['num_unopened'] = $num_unopened;
        $data['link_text'] = ($num_unopened>0) ? 'Enquiries ('.$num_unopened.')' : 'Enquiries';
        $data['link_attr'] = ($num_unopened>0) ? array('class' => 'highlight') : [];
        $this->view('msgs_admin_top_rhs', $data);
    }

    function _count_num_opened_for_admin() {
        $params['opened'] = 0;
        $params['from_admin'] = 0;
        $sql = 'SELECT id FROM messages WHERE opened=:opened AND from_admin=:from_admin';
        $rows = $this->db->query_bind($sql, $params, 'object');
        $num_unopened = count($rows);
        return $num_unopened;
    }

    function create() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = segment(3);
        $sent_to = segment(4);

        if ($update_id == '') {
            set_flashdata('<p style="color: red;">Please Select A Member</p>');
            redirect('messages/manage');
        }

        if ($update_id == '0') {
            $update_id = '';
        }

        $submit = post('submit');

        if (($submit == '') && (is_numeric($update_id))) {
            $data = $this->_get_data_from_db($update_id);

            if ($update_id>0) {
                $this->module('your_messages');
                //get the username of the person who sent the message
                $data['username'] = $this->_get_sender_username($data['sent_from']);
                $data['message_body'] = $this->your_messages->_build_modified_msg($data);
            }

        } else {
            $data = $this->_get_data_from_post();
        }

        if ((segment(5) !== '') && ($submit == '')) {
            $item_code = str_replace('ITEM_CODE_', '', segment(5));
            $starter_data = $this->_get_starter_data($item_code);

            if ($starter_data !== false) {
                $data['message_subject'] = $starter_data['message_subject'];
                $data['message_body'] = $starter_data['message_body'];
            }
        }

        $data['message_folders_options'] = $this->_get_message_folders_options($data['message_folders_id']);

        if (is_numeric($update_id)) {
            $data['headline'] = 'Reply To Message';
            $data['cancel_url'] = BASE_URL.'messages/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Message';
            $data['cancel_url'] = BASE_URL.'messages/manage';
        }

        if (isset($data['message_subject'])) {
            $data['message_subject'] = $this->correct_msg_subject($data['message_subject']);
        }
        $data['target_member'] = $this->_get_target_member_username($sent_to);
        $data['form_location'] = str_replace('/create/', '/submit/', current_url());
        $data['view_file'] = 'create';
        $this->template('admin', $data);
    }

    private function correct_msg_subject($message_subject) {
        $update_id = (int) segment(3);

        if ($update_id > 0) {
            $first_three = substr($message_subject, 0, 3);
            if ($first_three !== 'Re: ') {
                $message_subject = 'Re: '.$message_subject;
            }
        }

        return $message_subject;
    }

    function _get_starter_data($item_code) {
        $item_obj = $this->db->get_one_where('code', $item_code, 'module_market_items');
        if ($item_obj == false) {
            $starter_data = false;
        } else {

            $approved = $item_obj->approved;
            $live = $item_obj->live;

            $headline = $this->_get_headline($approved, $live);
            $body = $this->_get_body($item_obj, $approved, $live);

            $starter_data['message_subject'] = $headline;
            $starter_data['message_body'] = $body;
        }

        return $starter_data;
    }

    function _get_headline($approved, $live) {

        $headline = 'Item Status Update';

        if (($approved == 1) && ($live == 1)) {
            $headline = 'Congratulations - your item has just gone live';
        }

        if (($approved == 1) && ($live == 0)) {
            $headline = 'Your item has been approved';
        }

        if ($approved == 0) {
            $headline = 'Item Status Update';
        }

        return $headline;
    }

    function _get_body($item_obj, $approved, $live) {

        $msg_body = 'Your item, [item_title] has been checked out.  ';
        $msg_body.= 'You can manage your item via the following URL: <br><br>[manage_item_page]';

        if (($approved == 1) && ($live == 1)) {
            $msg_body = 'Congratulations. Your item, [item_title] has been approved and has just gone live.  ';
            $msg_body.= 'Your item is now on display in '.APP_STORE_NAME_FULL.' at the following URL:<br><br>[item_url].';
            $msg_body.= '<br><br>You can manage your item via the following URL: [manage_item_page]';
            $msg_body.= '<br><br>Best of luck!';
            $msg_body.= '<br><br>DC';
        }

        if (($approved == 1) && ($live == 0)) {
            $msg_body = 'Your item, [item_title] has been approved for the '.APP_STORE_NAME_FULL.'.  ';
            $msg_body.= 'However, your item is not currently live.  In other words, it is not yet on display in '.APP_STORE_NAME.'.  To publish your item you have to go to ';
            $msg_body.= 'the <a href="[basic_details_form_url]"> Update Basic Details</a> page and check  the checkbox that says `Go Live`.';

            $msg_body.= '<br><br>Regards,';
            $msg_body.= '<br><br>DC';

            $basic_details_form_url = BASE_URL.'module_market-submit_mod/basic_form/'.$item_obj->code;
            $msg_body = str_replace('[basic_details_form_url]', $basic_details_form_url, $msg_body);
        }

        if ($approved == 0) {
            $msg_body = 'Your item, [item_title] has just been checked out.';
            $msg_body.= '<br><br>At the moment, your item is not approved and not live.';
            $msg_body.= '<br><br>Regards,<br><br>DC';
        }

        $msg_body = str_replace('[item_title]', '<b>'.$item_obj->title.'</b>', $msg_body);
        $manage_item_page = BASE_URL.'manage_item_page/update/'.$item_obj->code;

        $manage_item_page_link = '<a href="'.$manage_item_page.'">'.$manage_item_page.'</a>';
        $msg_body = str_replace('[manage_item_page]', $manage_item_page_link, $msg_body);

        $item_url =  BASE_URL.'module_market/display/'.$item_obj->code;
        $item_url_link = '<a href="'.$item_url.'">'.$item_url.'</a>';
        $msg_body = str_replace('[item_url]', $item_url_link, $msg_body);
        return $msg_body;
    }

    function _get_target_member_username($trongate_user_id) {
        $sql = 'SELECT
                    members.username 
                FROM
                    members
                INNER JOIN
                    trongate_users
                ON
                    members.trongate_user_id = trongate_users.id 
                WHERE trongate_users.id = :trongate_user_id';

        $params['trongate_user_id'] = $trongate_user_id;
        $rows = $this->db->query_bind($sql, $params, 'object');
        $target_row = $rows[0];
        $member_username = $target_row->username;
        return $member_username;
    }

    function junk() {
        $this->manage();
    }

    function archives() {
        $this->manage();
    }

    function important() {
        $this->manage();
    }

    function _get_sender_username($trongate_user_id) {
        $member_obj = $this->db->get_one_where('trongate_user_id', $trongate_user_id, 'members');

        if (gettype($member_obj) == 'object') {
            $username = $member_obj->username;
        } else {
            $username = 'Unknown';
        }

        return $username;
    }

    function manage() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();

        //fetch_all of the message folders
        $data['message_folders'] =  $this->db->get('id', 'message_folders');
        $message_folder = ucfirst(segment(2));
        $data['folder_name'] = 'Inbox'; //default
        $message_folders_id = 1; //default

        //figure out the current folder ID
        foreach($data['message_folders'] as $msg_folder) {
            if ($msg_folder->folder_title == $message_folder) {
                $data['folder_name'] = $message_folder;
                $message_folders_id = (int) $msg_folder->id;
            }
        }

        $params['message_folders_id'] = $message_folders_id;
        $params['from_admin'] = 0;
        //get ALL of the messages in this folder that are FOR admin
        $sql = 'select * from messages where from_admin = :from_admin and message_folders_id = :message_folders_id order by date_created desc';
        $all_rows = $this->db->query_bind($sql, $params, 'object');

        $data['all_members'] = $this->_get_all_members(true);
        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'messages/manage';
        $pagination_data['record_name_plural'] = 'messages';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['token'] = $token;
        $data['rows'] = $this->_reduce_rows($all_rows);

        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'messages';
        $data['view_file'] = 'manage';
        $this->template('bootstrappy', $data);
    }

    function manageOLD2() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['message_subject'] = '%'.$searchphrase.'%';
            $params['code'] = '%'.$searchphrase.'%';
            $sql = 'select * from messages
            WHERE message_subject LIKE :message_subject
            OR code LIKE :code
            ORDER BY date_created desc';
            $all_rows = $this->db->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Messages';

            $this_folder = segment(2);
            switch($this_folder) {
                case 'inbox':
                    $message_folders_id = 1;
                    break;
                case 'manage':
                    $message_folders_id = 1;
                    break;
                case 'junk':
                    $message_folders_id = 2;
                    break;
                case 'archives':
                    $message_folders_id = 3;
                    break;
                case 'default':
                    $message_folders_id = 1;
            }

            $params['target_folder'] = $message_folders_id;
            $params['from_admin'] = 0;
            $data['folder_name'] = ucfirst(segment(2));

            if ($data['folder_name'] == 'Manage') {
                $data['folder_name'] = 'Inbox';
            }

/*
            $sql = 'select * from messages where from_admin = :from_admin 
                    and message_folders_id = :target_folder 
                    order by date_created desc';
*/

            $sql = 'SELECT
                        messages.id,
                        messages.date_created,
                        messages.message_subject,
                        messages.message_body,
                        messages.opened,
                        messages.code,
                        members.username 
                    FROM
                        messages
                    INNER JOIN
                        trongate_users
                    ON
                        messages.sent_from = trongate_users.id
                    INNER JOIN
                        members
                    ON
                        trongate_users.id = members.trongate_user_id 
                    WHERE messages.message_folders_id = :target_folder 
                    AND messages.from_admin = :from_admin 
                    ORDER BY messages.date_created desc';

            // $sql = 'select * from messages where from_admin = 0 order by date_created desc';
            // $all_rows = $this->db->query($sql, 'object');

            $all_rows = $this->db->query_bind($sql, $params, 'object');
            echo count($all_rows); die();
        }



        $data['all_members'] = $this->_get_all_members(true);
        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'messages/manage';
        $pagination_data['record_name_plural'] = 'messages';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        //fetch_all of the message folders
        $data['message_folders'] =  $this->db->get('id', 'message_folders');
        $data['token'] = $token;
        $data['rows'] = $this->_reduce_rows($all_rows);

        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'messages';
        $data['view_file'] = 'manage';
        $this->template('admin', $data);
    }

    function _get_all_members($return_trongate_id=null) {
        $all_members = [];
        $members = $this->db->get('id', 'members');
        foreach($members as $key => $value) {
            if (isset($return_trongate_id)) {
                 $all_members[$value->trongate_user_id] = $value->username;     
            } else {
                 $all_members[$value->id] = $value->username;
            }
        }
        return $all_members;
    }

    function manageOLD() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['message_subject'] = '%'.$searchphrase.'%';
            $params['code'] = '%'.$searchphrase.'%';
            $sql = 'select * from messages
            WHERE message_subject LIKE :message_subject
            OR code LIKE :code
            ORDER BY date_created desc';
            $all_rows = $this->db->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Messages';

            $this_folder = segment(2);
            switch($this_folder) {
                case 'inbox':
                    $message_folders_id = 1;
                    break;
                case 'manage':
                    $message_folders_id = 1;
                    break;
                case 'junk':
                    $message_folders_id = 2;
                    break;
                case 'archives':
                    $message_folders_id = 3;
                    break;
                case 'default':
                    $message_folders_id = 1;
            }

            $params['target_folder'] = $message_folders_id;
            $params['from_admin'] = 0;

            $data['folder_name'] = ucfirst(segment(2));

            if ($data['folder_name'] == 'Manage') {
                $data['folder_name'] = 'Inbox';
            }

            $sql = 'select * from messages where from_admin = :from_admin 
                    and message_folders_id = :target_folder 
                    order by date_created desc';
            $all_rows = $this->db->query_bind($sql, $params, 'object');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'messages/manage';
        $pagination_data['record_name_plural'] = 'messages';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        //fetch_all of the message folders
        $data['message_folders'] =  $this->db->get('id', 'message_folders');
        $data['token'] = $token;
        $data['all_usernames'] = $this->_get_all_usernames($token);
        $data['rows'] = $this->_reduce_rows($all_rows);
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'messages';
        $data['view_file'] = 'manage';
        $this->template('admin', $data);
    }

    function _get_all_usernames($token) {

        //get my username (as an admin)
        $this->module('trongate_security');
        $my_user_id= $this->trongate_security->_get_user_id();

        $result = $this->db->get_one_where('trongate_user_id', $my_user_id, 'trongate_administrators');

        if (gettype($result) == false) {
            redirect('members/logout');
        }

        $all_usernames[$my_user_id] = $result->username;

        $sql = 'SELECT
                    members.username,
                    members.trongate_user_id 
                FROM
                    members
                INNER JOIN
                    trongate_users
                ON
                    members.trongate_user_id = trongate_users.id';

        $rows = $this->db->query($sql, 'object');
        foreach($rows as $row) {
            $all_usernames[$row->trongate_user_id] = $row->username;
        }

        return $all_usernames;
    }

    function _get_sent_from_name($sent_from, $target_table) {
        $params['sent_from'] = $sent_from;
        $sql = 'SELECT
                    target_table.username,
                    members.trongate_user_id 
                FROM
                    target_table
                INNER JOIN
                    trongate_users
                ON
                    target_table.trongate_user_id = trongate_users.id 
                WHERE trongate_users.id = :sent_from';

        $sql = str_replace('target_table', $target_table, $sql);
        $rows = $this->db->query_bind($sql, $params, 'object');

        if (count($rows)>0) {
            $sent_from_name = $rows[0]->username;
        } else {
            $sent_from_name = 'Unknown';
        }

        return $sent_from_name;
        
    }

    function submit_change_folder() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = segment(3);

        if ((!is_numeric($update_id)) && ($update_id != '')) {
            redirect('messages/manage');
        }

        $data['message_folders_id'] = post('new_folder');
        $this->db->update($update_id, $data);

        $result = $this->db->get_where($data['message_folders_id'], 'message_folders');
        $new_folder_title = $result->folder_title;

        set_flashdata('The message was successfully sent to the '.$new_folder_title.' folder');
        redirect(previous_url());        
    }

    function show() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = segment(3);

        if ((!is_numeric($update_id)) && ($update_id != '')) {
            redirect('messages/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['token'] = $token;

        $this->module('messages_boss');
        $this->messages_boss->_set_message_to_opened($data);

        $data['message_folders'] =  $this->_get_message_folders($data['message_folders_id']);
        $data['sent_from_name'] = $this->_get_sent_from_name($data['sent_from'], 'members');

        if (($data['sent_from'] == 0) && ($data['sent_from_name'] == 'Unknown')) {
            $data['sent_from_name'] = $this->_guess_name($data['message_body']);
            $data['sender_email'] = $this->_guess_sender_email($data['message_body']);
            $data['reduced_message_body'] = $this->_reduce_message_body($data['message_body']);
        } else {
            $data['sender_email'] = 'Unknown';
            $data['reduced_message_body'] = $data['message_body'];
        }

        if ($data == false) {
            redirect('messages/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Message Information';
            $data['view_file'] = 'show';
            $this->template('admin', $data);
        }
    }

    function _reduce_message_body($message_body) {
        $start = '<strong>Message: ';
        $rand_str = '*65~~£$,';
        $message_body.= $rand_str;
        $end = $rand_str;
        $reduced_message_body = $this->_extract_content($message_body, $start, $end);
        $reduced_message_body = strip_tags(trim($reduced_message_body));
        $reduced_message_body = str_replace($rand_str, '', $reduced_message_body);
        return $reduced_message_body;
    }

    function _guess_sender_email($message_body) {
        $start = '<strong>Email Address: ';
        $end = '</p>';
        $sender_email = $this->_extract_content($message_body, $start, $end);

        $email_len = strlen($sender_email);
        if (($email_len<2) || ($email_len > 35)) {
            $sender_email = 'Unknown';
        }

        $sender_email = strip_tags(trim($sender_email));
        return $sender_email;
    }

    function _guess_name($message_body) {
        $start = '<strong>Name: </strong>';
        $end = '</p>';
        $name = $this->_extract_content($message_body, $start, $end);

        $name_len = strlen($name);
        if (($name_len<2) || ($name_len > 35)) {
            $name = 'Unknown';
        }

        $name = strip_tags(trim($name));
        return $name;
    }

    function _extract_content($string, $start, $end) {
        $pos = stripos($string, $start);
        $str = substr($string, $pos);
        $str_two = substr($str, strlen($start));
        $second_pos = stripos($str_two, $end);
        $str_three = substr($str_two, 0, $second_pos);
        $content = trim($str_three); // remove whitespaces
        return $content;
    }

    function _get_message_folders($message_folders_id=null) {
        $do_not_include = [];
        $message_folders = [];

        if (isset($message_folders_id)) {
            $do_not_include[] = $message_folders_id;
            $message_folders[''] = 'Select folder...';
        }

        $all_folders = $this->db->get('id', 'message_folders');


        foreach($all_folders as $value) {
            if (!in_array($value->id, $do_not_include)) {
                $message_folders[$value->id] = $value->folder_title;
            }
            
        }

        return $message_folders;
    }
    
    function _reduce_rows($all_rows) {
        $rows = [];
        $start_index = $this->_get_offset();
        $limit = $this->_get_limit();
        $end_index = $start_index + $limit;

        $count = -1;
        foreach ($all_rows as $row) {
            $count++;
            if (($count>=$start_index) && ($count<$end_index)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    function submit() {
        $this->module('trongate_security');
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();

        $submit = post('submit', true);


        if ($submit == 'Submit') {

            $this->validation->set_rules('message_subject', 'Message Subject', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('message_body', 'Message Body', 'required|min_length[2]');

            $result = $this->validation->run();

            if ($result == true) {

                $update_id = segment(3);
                $data = $this->_get_data_from_post();
                $data['message_folders_id'] = (is_numeric($data['message_folders_id']) ? $data['message_folders_id'] : 0);


                //insert the new record
                $data['from_admin'] = 1;
                $flash_msg = 'The message was successfully sent';
                $data['sent_from'] = $this->trongate_security->_get_user_id();
                $data['sent_to'] = segment(4);
                $this->module('messages_boss');
                $update_id = $this->messages_boss->_send_msg($data);
                
                set_flashdata('<span class="success_text">'.$flash_msg.'</span>');
                redirect('messages/show/'.$update_id);

            } else {
                //form submission error
                $this->create();
            }

        }

    }

    function submit_delete() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = segment(3);

        if (($submit == 'Yes - Delete Now') && (is_numeric($params['update_id']))) {
            //delete all of the comments associated with this record
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'messages';
            $this->db->query_bind($sql, $params);

            //delete the record
            $this->db->delete($params['update_id'], 'messages');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata('<span class="success_text">'.$flash_msg.'</span>');

            //redirect to the manage page
            redirect('messages/manage');
        }
    }

    function _get_limit() {
        if (isset($_SESSION['selected_per_page'])) {
            $limit = $this->per_page_options[$_SESSION['selected_per_page']];
        } else {
            $limit = $this->default_limit;
        }

        return $limit;
    }

    function _get_offset() {
        $page_num = segment(3);

        if (!is_numeric($page_num)) {
            $page_num = 0;
        }

        if ($page_num>1) {
            $offset = ($page_num-1)*$this->_get_limit();
        } else {
            $offset = 0;
        }

        return $offset;
    }

    function _get_selected_per_page() {
        if (!isset($_SESSION['selected_per_page'])) {
            $selected_per_page = $this->per_page_options[1];
        } else {
            $selected_per_page = $_SESSION['selected_per_page'];
        }

        return $selected_per_page;
    }

    function set_per_page($selected_index) {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('messages/manage');
    }

    function _get_data_from_db($update_id) {
        $record_obj = $this->db->get_where($update_id, 'messages');

        if ($record_obj == false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    function _get_data_from_post() {
        $data['message_subject'] = post('message_subject', true);
        $data['message_body'] = post('message_body');        
        $data['message_folders_id'] = post('message_folders_id');
        return $data;
    }

    function _get_message_folders_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'messages', 'message_folders');
        return $options;
    }

    function _attempt_get_upgrade_alerts($member_id, $member_obj=null) {
        $upgrade_alerts = [];

        if (!isset($member_obj)) {
            $member_obj = $this->db->get_where($member_id, 'members');
        }

        if (gettype($member_obj) == 'object') {
            $this->module('your_account');
            $member_downloads = $this->your_account->_get_member_downloads($member_obj);

            foreach ($member_downloads as $key => $value) {

                if (isset($value->current_version_number)) {
                    $members_version_number = $value->version_number;
                    $current_version_number = $value->current_version_number;
                    if ($members_version_number !== $current_version_number) {
                        $row_data['item_title'] = $value->item_title;
                        $row_data['module_code'] = $value->module_code;
                        $row_data['current_version_number'] = $value->current_version_number;
                        $upgrade_alerts[] = $row_data;
                    }
                }

            }
        }

        return $upgrade_alerts;
    }

    function _get_num_unopened($trongate_user_id, $all_rows=null) {
        $params['message_folders_id'] = 1; //inbox
        $params['trongate_user_id'] = $trongate_user_id;
        $params['opened'] = 0;

        if (!isset($all_rows)) {
            $sql = 'select id from messages where sent_to = :trongate_user_id and message_folders_id = :message_folders_id and opened = :opened';
            $rows = $this->db->query_bind($sql, $params, 'object');
            $num_unopened = count($rows);
        } else {
            $num_unopened = 0;
            foreach($all_rows as $row) {
                if ($row->opened == 0) {
                    $num_unopened++;
                }
            }            
        }
        return $num_unopened;
    }

    function _send_congratulations_msg($module_obj) {
        //this gets called by module_uploader when a file has been uploaded
        //for the 1st time, for a particular module

        $item_type =  strtolower($module_obj->type_title);

        $subject = 'Thank you for uploading a module';
        $msg = 'Many thanks for submitting a new '.$item_type.' for the '.APP_STORE_NAME.'.  ';
        $msg.= 'Your '.$item_type.' details have been successfully submitted, along ';
        $msg.= 'with the file that you have uploaded.<br><br>';
        $msg.= 'Your submission will be getting reviewed shortly and if all is well, it will ';
        $msg.= 'be added to the '.APP_STORE_NAME.' soon.  However, if there is an issue then ';
        $msg.= 'you will be contacted. Either way, you can expect to be contacted soon and ';
        $msg.= 'thank you once again for your submission.<br><br>';
        $msg.= 'Regards,<br><br>';
        $msg.= 'ASLAN <i>(<b>A</b> <b>S</b>ystem for <b>L</b>eaving <b>A</b>utomated <b>N</b>otices)</i>';

        $data['sent_from'] = 1;
        $data['sent_to'] = $module_obj->trongate_user_id;
        $data['message_subject'] = $subject;
        $data['message_body'] = $msg;
        $data['from_admin'] = 1;
        $data['message_folders_id'] = 1;

        $this->module('messages_boss');
        $this->messages_boss->_send_msg($data);

        $this->module('notifications');
        $this->notifications->_notify_item_uploaded($module_obj->id, 0);  
    }

    function _send_item_uploaded_msg($module_obj) {
        //this gets called by module_uploader when a file has been uploaded
        //for the 1st time, for a particular module

        $item_type =  strtolower($module_obj->type_title);

        $subject = 'New '.$item_type.' uploaded';
        $msg = 'You have uploaded a new file for your '.$item_type.' entitled, "<b>'.$module_obj->title.'</b>".  ';
        $msg.= 'Your code will be getting reviewed shortly and if all is well, your item will go live soon.';  
        $msg.= '  However, if there is an issue then ';
        $msg.= 'you will be contacted. Either way, you can expect to be contacted soon and ';
        $msg.= 'thank you, as always, for being on board with us.<br><br>';
        $msg.= 'Regards,<br><br>';
        $msg.= 'ASLAN <i>(<b>A</b> <b>S</b>ystem for <b>L</b>eaving <b>A</b>utomated <b>N</b>otices)</i>';

        $data['sent_from'] = 1;
        $data['sent_to'] = $module_obj->trongate_user_id;
        $data['message_subject'] = $subject;
        $data['message_body'] = $msg;
        $data['from_admin'] = 1;
        $data['message_folders_id'] = 1;

        $this->module('messages_boss');
        $this->messages_boss->_send_msg($data);  

        $this->module('notifications');
        $this->notifications->_notify_item_uploaded($module_obj->id, 1);      
    }

    function _draw_upgrade_alert($upgrade_alerts, $alert_type, $member_id=null) {
        //alert type can be 'short' or 'full' (maybe something else later)
        $num_required_upgrades = count($upgrade_alerts);

        if ($alert_type == 'full') {
            //build a full upgrade alert and return the message
            $target_subjects = [];
            $sent_subjects = [];

            $this->module('members');
            $this->module('messages_boss');
            $trongate_user_id = $this->members->_get_trongate_user_id($member_id);
            $params['sent_to'] = $trongate_user_id; //turn this into a trongate ID!
            $count = 0;
            foreach($upgrade_alerts as $upgrade_alert) {
                $count++;
                $item_title = $upgrade_alert['item_title'];
                $current_version_number = $upgrade_alert['current_version_number'];
                $target_subject = $item_title.' '.$current_version_number.' Available';
                $target_subjects[] = $target_subject;
                $target_param_property = 'subject_'.$count;
                $params[$target_param_property] = $target_subject;
            }

            $sql = 'SELECT message_subject from messages where sent_to = :sent_to and ';
            $count = 0;
            foreach($target_subjects as $target_subject) {
                $count++;
                $sql.= 'message_subject = :subject_'.$count;
            }

            if ($count>0) {
                //attempt fetch the messages that have already been sent, relating to these alerts
                $message_rows = $this->db->query_bind($sql, $params, 'object');
                foreach($message_rows as $message_row) {
                    $sent_subjects[] = $message_row->message_subject;
                }
            }

            //we now know what emails have been sent AND what alerts are due.  So...
            foreach($upgrade_alerts as $upgrade_alert) {
                $item_title = $upgrade_alert['item_title'];
                $current_version_number = $upgrade_alert['current_version_number'];
                $target_subject = $item_title.' '.$current_version_number.' Available';

                if (!in_array($target_subject, $sent_subjects)) {
                    //a message needs to be sent for this alert!
                    $data['sent_from'] = 1;
                    $data['sent_to'] = $params['sent_to'];
                    $data['message_subject'] = $target_subject;
                    $data['message_body'] = $item_title.' '.$current_version_number.' has been released. ';
                    $data['message_body'].= 'Please visit [link] for more information.';

                    $download_page_url = BASE_URL.'your_account/download_history';
                    $download_text = 'your downloads page';
                    $download_link = '<a href="'.$download_page_url.'">'.$download_text.'</a>';
                    $data['message_body'] = str_replace('[link]', $download_link, $data['message_body']);
                    $data['from_admin'] = 1;
                    $this->messages_boss->_send_msg($data);
                }
            }


        } else {

            //normal upgrade alert
            if ($num_required_upgrades == 1) {
                $msg = 'A new version is available for one of your previously downloaded items.';
            } else {
                $msg = 'You have new versions available for '.$num_required_upgrades.' of your previously downloaded items.';
            }

            $data['msg'] = $msg;
            $view_file = 'upgrade_alert_'.$alert_type;
            $this->view($view_file, $data);

        }

    }
}