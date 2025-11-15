<?php
class Module_requests extends Trongate {

    private $new_time_gap = 86400*7;

    function index() {
        redirect('module_requests/browse');
    }

    function _draw_admin_top_rhs() {
        $column = 'published';
        $value = 0;
        $target_table = 'module_requests';
        $num_unpublished = $this->db->count_rows($column, $value, $target_table);
        $data['link_text'] = ($num_unpublished>0) ? 'Module Requests ('.$num_unpublished.')' : 'Module Requests';
        $data['link_attr'] = ($num_unpublished>0) ? array('class' => 'highlight') : [];
        $this->view('module_requests_admin_top_rhs', $data);
    }

    function view_request() {
        $code = segment(3);
        $record_obj = $this->db->get_one_where('code', $code);
        $data = (array) $record_obj;

        $params['created_by'] = (int) $data['created_by'];
        $winning_bid_id = (int) $data['winning_bid_id'];

        //fetch the winning bid details
        $winning_bid_obj = $this->db->get_where($winning_bid_id, 'module_request_responses');
        $params['winning_bidder_id'] = (int) $winning_bid_obj->trongate_user_id;

        //make sure user is EITHER the request creator or the winning bidder
        $this->module('trongate_tokens');
        $data['my_trongate_user_id'] = $this->trongate_tokens->_get_user_id();

        if (($data['my_trongate_user_id'] == false) || (!in_array($data['my_trongate_user_id'], $params))) {
            redirect('forbidden');
        }

        $data['offer_value'] = $winning_bid_obj->offer_value;
        $data['offer_currency'] = $winning_bid_obj->offer_currency;
        $data['offer_terms'] = trim($winning_bid_obj->comment);

        //fetch the request creator or the winning bidder details
        $sql = 'SELECT * FROM members WHERE trongate_user_id=:created_by OR trongate_user_id=:winning_bidder_id';
        $member_rows = $this->db->query_bind($sql, $params, 'object');
        $num_member_rows = count($member_rows);

        if ($num_member_rows !== 2) {
            redirect('ouch');
        }

        foreach($member_rows as $member_row) {
            $member_trongate_user_id = (int) $member_row->trongate_user_id;
            if ($member_trongate_user_id == $params['created_by']) {
                $data['creator_username'] = $member_row->username;
                $data['creator_email_address'] = $member_row->email_address;
                $data['creator_join_date'] = date('F Y', $member_row->date_joined);
                $data['creator_profile_url'] = BASE_URL.'members/profile/'.$member_row->code;
            } else {
                $data['winning_bidder_username'] = $member_row->username;
                $data['winning_bidder_email_address'] = $member_row->email_address;
                $data['winning_bidder_join_date'] = date('F Y', $member_row->date_joined);
                $data['winning_bidder_profile_url'] = BASE_URL.'members/profile/'.$member_row->code;
            }
        }

        $additional_includes_top[] = DEFAULT_THEME.'css/info_page.css';
        $additional_includes_top[] = BASE_URL.'module_requests_module/css/module_requests.css';
        $data['additional_includes_top'] = $additional_includes_top;

        $additional_includes_btm[] = BASE_URL.'module_requests_module/js/module_requests.js';
        $data['additional_includes_btm'] = $additional_includes_btm;

        $data['view_file'] = 'deal_details';
        $this->template('public', $data);
    }

    function offer_accepted() {
        $data['view_file'] = 'offer_accepted';
        $this->template('public', $data);
    }

    function submit_accept_offer() {

        $request_code = post('request_code', true);
        $response_code = post('response_code', true);

        //make sure logged in
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        //make sure the code is valid
        $record_obj = $this->db->get_one_where('code', $request_code, 'module_requests');

        //make sure that the module_request belongs to THIS user and is open
        $this->module('trongate_tokens');
        $my_trongate_user_id = (int) $this->trongate_tokens->_get_user_id($token);

        if ($my_trongate_user_id == 0) {
            redirect('members/login');
        }

        $open = (int) $record_obj->open;
        $winning_bid_id = (int) $record_obj->winning_bid_id;
        $created_by = (int) $record_obj->created_by;

        if (($open == 0) || ($winning_bid_id > 0) || ($created_by !== $my_trongate_user_id)) {
            redirect('forbidden');
        }

        if ($record_obj == false) {
            redirect('ouch');
        } else {

            //fetch the response record
            $params['response_type'] = 'Offer';
            $params['module_request_id'] = $record_obj->id;
            $params['response_code'] = $response_code;
            $sql = 'SELECT * FROM module_request_responses 
                    WHERE 
                      response_type=:response_type 
                    AND 
                      module_request_id=:module_request_id 
                    AND response_code=:response_code';

            $rows = $this->db->query_bind($sql, $params, 'object');
            $num_rows = count($rows);

            if ($num_rows == 0) {
                redirect('ouch');
            }

            //set the reponse_type to 'Winning Offer'
            $this->_make_offer_winner($rows[0]->id);

            $update_id = $record_obj->id;
            $data['open'] = 0;
            $data['winning_bid_id'] = $rows[0]->id;
            $this->db->update($update_id, $data, 'module_requests');

            $this->module('module_request_msg_boss');
            $this->module_request_msg_boss->_offer_accepted_after_hook($update_id);

            redirect('module_requests/offer_accepted/'.$record_obj->code);
        }


    }

    function _make_offer_winner($response_id) {
        $data['response_type'] = 'Winning Offer';
        $data['date_accepted'] = time();
        $this->db->update($response_id, $data, 'module_request_responses');
    }

    function goto() {
        $update_id = (int) segment(3);
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $record_obj = $this->db->get_where($update_id, 'module_requests');
        if ($record_obj == false) {
            redirect('module_request_responses/manage');
        } else {
            redirect('module_requests/browse/'.$record_obj->code);
        }
    }

    function _make_sure_request_owner($token, $record_obj) {
        //make sure the record belongs to THIS member
        $created_by = (int) $record_obj->created_by;

        $this->module('members');
        $member_obj = $this->members->_get_member_obj($token);
        $my_trongate_user_id = (int) $member_obj->trongate_user_id;

        if ($created_by !== $my_trongate_user_id) {
            redirect('forbidden');
        }
    }

    function _make_sure_bid_allowed($token, $record_obj) {
        //make sure the record is; published, open and does NOT belong to THIS member
        $open = (int) $record_obj->open;
        $published = (int) $record_obj->published;
        $created_by = (int) $record_obj->created_by;

        $this->module('members');
        $member_obj = $this->members->_get_member_obj($token);
        $my_trongate_user_id = (int) $member_obj->trongate_user_id;

        if (($open == 0) || ($published == 0) || ($created_by == $my_trongate_user_id)) {
            redirect('forbidden');
        }
    }

    function manage() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
        $data['unpublished'] = $this->_fetch_unpublished();
        $data['unpublished_info'] = $this->_build_rows_info(count($data['unpublished']), 'unpublished module request', 'unpublished module requests');
        $data['num_unpublished'] = count($data['unpublished']);
        $data['unpublished'] = $this->_prep_rows($data['unpublished']);

        $data['published'] = $this->_fetch_unpublished(true);
        $data['published_info'] = $this->_build_rows_info(count($data['published']), 'published module request', 'published module requests');
        $data['num_published'] = count($data['published']);
        $data['published'] = $this->_prep_rows($data['published']);
        $data['view_file'] = 'manage_module_requests';
        $this->template('bootstrappy', $data);
    }

    function show() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
        $record_obj = $this->db->get_one_where('code', segment(3), 'module_requests');

        if ($record_obj == false) {
            redirect('module_requests/manage');
        }

        $data = (array) $record_obj;
        $created_by_obj = $this->_fetch_created_by_obj($data['created_by']);

        if ($created_by_obj == false) {
            echo 'Member who created this cannot be found'; die();
        }

        $data['created_by_username'] = $created_by_obj->username;
        $data['create_by_url'] = BASE_URL.'members/show/'.$created_by_obj->id;

        $data['module_request_code'] = $this->_make_module_request_code($data['code']);
        $data['view_file'] = 'show_module_request';
        $this->template('bootstrappy', $data);
    }

    function submit_change_publish_status() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $record_obj = $this->db->get_one_where('code', segment(3));

        if ($record_obj == false) {
            redirect('module_requests/manage');
        }

        $published = (int) $record_obj->published;

        if ($published == 1) {
            $data['published'] = 0;
            $flash_msg = 'The request is no longer published';
        } else {
            $data['published'] = 1;
            $flash_msg = 'The request was successfully published';

            //get a member_obj
            $created_by = (int) $record_obj->created_by;
            $this->module('members');
            $send_data['member_obj'] = $this->db->get_one_where('trongate_user_id', $created_by, 'members');
            
            //get the URL where the module request can be viewed
            $send_data['module_request_url'] = BASE_URL.'module_requests/browse/'.segment(3);

            $this->module('module_request_msg_boss');
            $this->module_request_msg_boss->_send_request_published_msg($send_data);

        }

        $this->db->update($record_obj->id, $data, 'module_requests');

        //LATER -> send internal message to the user who created this

        set_flashdata($flash_msg);
        redirect(previous_url());
    }

    function _fetch_created_by_obj($trongate_user_id) {
        $member_obj = $this->db->get_one_where('trongate_user_id', $trongate_user_id, 'members');
        return $member_obj;
    }

    function _make_module_request_code($code) {
        $first_four = substr($code, 0, 4);
        $module_request_code = strtoupper($first_four);
        return $module_request_code;
    }

    function _prep_rows($rows) {
        foreach($rows as $key => $value) {
            $rows[$key]->date_created = date('l jS F Y', $value->date_created);
            $rows[$key]->published = ($value->published == 1) ? 'published' : 'not published';
            $rows[$key]->created_by = $value->username;
        }
        return $rows;
    }

    function _build_rows_info($num_rows, $singular, $plural) {
        if ($num_rows == 1) {
            $msg = 'There is currently one '.$singular.'.';
        } else {
            $msg = 'There are currently '.$num_rows.' '.$plural.'.';
        }

        return $msg;
    }

    function _fetch_unpublished($invert=null) {

        if (isset($invert)) {
            $published_value = 1;
        } else {
            $published_value = 0;
        }

        $sql = 'SELECT
                    module_requests.*,
                    members.username 
                FROM
                    module_requests
                INNER JOIN
                    members
                ON
                    module_requests.created_by = members.trongate_user_id 
                WHERE 
                    module_requests.published = '.$published_value.' 
                ORDER BY 
                    module_requests.date_created DESC';


        $rows = $this->db->query($sql, 'object');
        return $rows;
    }

    function request_details() {
        $code = segment(3);
        $record_obj = $this->db->get_one_where('code', $code, 'module_requests');
        $data = (array) $record_obj;

        $additional_includes_top[] = DEFAULT_THEME.'css/info_page.css';
        $data['additional_includes_top'] = $additional_includes_top;
        
        $data['view_file'] = 'request_details';
        $this->template('public', $data);
    }

    function login_required() {
        $data['view_file'] = 'login_required';
        if (segment(3) == 'ask') {
            $data['info'] = 'In order to ask a question you have to be logged in.';
        } else {
            $data['info'] = 'In order to submit an offer you have to be logged in.';
        }
        $this->template('public', $data);
    }

    function browse() {

        $sql = 'SELECT
                    module_requests.*,
                    members.username as created_by_username 
                FROM
                    module_requests
                INNER JOIN
                    members
                ON
                    module_requests.created_by = members.trongate_user_id 
                WHERE 
                    published = 1 
                AND 
                    open = 1 
                ORDER BY 
                    date_created DESC';
        $data['published_requests'] = $this->db->query($sql, 'object');
        $data['published_requests'] = $this->_prep_for_public($data['published_requests']);

        if (segment(3) !== '') {
            $data['published_requests'] = $this->_priorities_current_module_request($data['published_requests'], segment(3));
        }

        $num_published = count($data['published_requests']);
        $data['active_request_code'] = ($num_published == 0) ? '' : $data['published_requests'][0]->code;
        $data['created_by'] = ($num_published == 0) ? 0 : (int) $data['published_requests'][0]->created_by;
        $data['is_new'] = ($num_published == 0) ? 0 : (int) $data['published_requests'][0]->new;

        $additional_includes_top[] = DEFAULT_THEME.'css/info_page.css';
        $additional_includes_top[] = BASE_URL.'module_requests_module/css/module_requests.css';
        $data['additional_includes_top'] = $additional_includes_top;

        $additional_includes_btm[] = BASE_URL.'module_requests_module/js/module_requests.js';
        $data['additional_includes_btm'] = $additional_includes_btm;

        $num_published_requests = count($data['published_requests']);

        if ($num_published_requests == 0) {
            $data['view_file'] = 'browse_requests_empty';
        } else {
            $data['view_file'] = 'browse_requests';
        }

        $this->template('public', $data);
    }

    function _priorities_current_module_request($rows, $module_request_code) {
        $index_to_ditch = -1;
        foreach($rows as $key => $value) {
            if ($value->code == $module_request_code) {
                $index_to_ditch = $key;
            }   
        }

        if ($index_to_ditch>=0) {
            $new[] = $rows[$index_to_ditch];
            unset($rows[$index_to_ditch]);
            $rows = array_merge($new, $rows);
        }

        return $rows;
    }

    function _prep_for_public($rows) {
        $rso = $this->_get_response_status_options();

        $ancient_history = time() - $this->new_time_gap;
        foreach($rows as $key => $value) {
            if ($value->date_created > $ancient_history) {
                $rows[$key]->new = true;
            } else {
                $rows[$key]->new = false;
            }

            $rows[$key]->request_details_short = $this->_shorten_words($value->request_details, 70);
            $rows[$key]->request_details = nl2br($value->request_details);
            $got_responses = (int) $value->got_responses;
            $rows[$key]->respond_invite = (isset($rso[$got_responses])) ? $rso[$got_responses] : '';
        }

        return $rows;
    }

    function _shorten_words($str, $max_len) {

        if (strlen($str)>$max_len) {
            $pos=strpos($str, ' ', $max_len);
            $new = substr($str,0,$pos); 
        } else {
            $new = $str;
        }

        $new.= '...';
        return $new;
    }

    function _additional_security($token, $request_code) {
        //make sure the request exists and is owned by THIS user
        $record_obj = $this->db->get_one_where('code', $request_code);

        if ($record_obj == false) {
            redirect('ouch');
        } else {
            $created_by = (int) $record_obj->created_by;
            $this->module('trongate_tokens');
            $my_trongate_user_id = (int) $this->trongate_tokens->_get_user_id($token);
            
            if ($created_by !== $my_trongate_user_id) {
                redirect('forbidden');
            }
        }

        return $record_obj;
    }

    function update_not_allowed() {
        $data['view_file'] = 'update_not_allowed';
        $this->template('public', $data);
    }

    function _make_sure_no_respones($module_request_obj) {
        $module_request_id = (int) $module_request_obj->id;
        $rows = $this->db->get_many_where('module_request_id', $module_request_id, 'module_request_responses');
        $num_rows = count($rows);

        if($num_rows>99990) {
            redirect('module_requests/update_not_allowed/'.$module_request_obj->code);
        }
        
    }

    function create() {

        $submit = post('submit', true);
        $request_code = segment(3);

        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        if ($request_code !== '') {
            $record_obj = $this->_additional_security($token, $request_code);

            //make sure the request has not received any responses
            $this->_make_sure_no_respones($record_obj);
        }

        if (($request_code !== '') && ($submit == '')) {
            $data = (array) $record_obj;
        } else {
            $data = $this->_get_data_from_post();
        }

        $data['page_headline'] = ($request_code == '') ? 'Request A Module' : 'Update Your Request';
        $data['cancel_url'] = ($request_code == '') ? 'module_requests/browse' : 'module_requests/browse/'.$request_code;
        $data['understood'] = (int) post('understood');

        $data['budget_options'] = $this->_get_budget_options($data['budget']);

        $budget = (isset($data['budget'])) ? $data['budget'] : '';
        $selected_budget_index = $budget;
        $budget_strlen = strlen($budget);
        if ($budget_strlen>3) {
            if(in_array($budget, $data['budget_options'])) {
                $selected_budget_index = array_search($budget, $data['budget_options']);
            }
        }

        $data['selected_budget_index'] = $selected_budget_index;
        $data['budget_options'] = $this->_get_budget_options($selected_budget_index);
        $data['form_location'] = str_replace('/create', '/submit', current_url());
        $data['view_file'] = 'request_module';
        $this->template('public', $data);
    }

    function thank_you() {
        $data['view_file'] = 'thank_you';
        $this->template('public', $data);
    }

    function submit_conf_delete() {
        $request_code = segment(3);
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        if ($request_code !== '') {
            $record_obj = $this->_additional_security($token, $request_code);
        }

        $module_request_id = (int) $record_obj->id;

        $sql = 'DELETE from module_request_responses WHERE module_request_id = '.$module_request_id;
        $this->db->query($sql);

        $this->db->delete($module_request_id, 'module_requests');

        $data['view_file'] = 'module_request_deleted';
        $this->template('public', $data);
    }

    function submit() {
        $request_code = segment(3);
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        if ($request_code !== '') {
            $record_obj = $this->_additional_security($token, $request_code);

            //make sure the request has not received any responses
            $this->_make_sure_no_respones($record_obj);
        }

        $this->module('members');
        $member_obj = $this->members->_get_member_obj($token);

        $trongate_user_id = $member_obj->trongate_user_id;

        $this->validation->set_rules('request_title', 'request title', 'required|min_length[7]|max_length[200]');
        $this->validation->set_rules('budget', 'budget', 'callback_budget_check');
        $this->validation->set_rules('request_details', 'request details', 'required|min_length[25]');
        $this->validation->set_rules('understood', 'terms and conditions checkbox', 'required');

        $result = $this->validation->run();

        if ($result == true) {
           
            $data = $this->_get_data_from_post();
            $data['budget'] = $this->_get_budget_description($data['budget']);

            if ($request_code == '') {
                $data['date_created'] = time();
                $data['open'] = 1;
                $data['created_by'] = $trongate_user_id;
                $data['winning_bid_id'] = 0;
                $data['code'] = make_rand_str(16);
                $data['published'] = 0;
                $this->db->insert($data, 'module_requests');
                $finish_url = 'module_requests/thank_you';

                $msg_data['member_obj'] = $member_obj;
                $this->module('module_request_msg_boss');
                $this->module_request_msg_boss->_send_thanks_msg($msg_data);

            } else {
                $update_id = $record_obj->id;
                $this->db->update($update_id, $data);
                $finish_url = BASE_URL.'module_requests/browse/'.$request_code;
                set_flashdata('Your request has been successfully updated');
            }

            redirect($finish_url);

        } else {
            $this->create();
        }
    }

    function test() {
        $member_obj = $this->db->get_where(1, 'members');
        $msg_data['member_obj'] = $member_obj;
        $this->module('module_request_msg_boss');
        $this->module_request_msg_boss->_send_thanks_msg($msg_data);
    }

    function _get_budget_description($budget_key) {
        $budget_options = $this->_get_budget_options('');

        if (isset($budget_options[$budget_key])) {
            $budget_description = $budget_options[$budget_key];
        } else {
            $budget_description = '- not available -';
        }

        return $budget_description;
    }

    function _get_budget_options($selected_option) {

        if ($selected_option == '') {
            $options[''] = 'Select...';
        }
        
        $options['z'] = '0 - I want it built for free';
        $options['x'] = 'I am happy to pay but would like to invite bids';
        $options['a'] = 'I am willing to pay up to 50 dollars (USD)';
        $options['b'] = 'I am willing to pay up to 100 dollars (USD)';
        $options['c'] = 'I am willing to pay up to 200 dollars (USD)';
        $options['d'] = 'I am willing to pay up to 400 dollars (USD)';
        $options['e'] = 'I am willing to pay up to 750 dollars (USD)';
        $options['f'] = 'I am willing to pay up to 1,000 dollars (USD)';
        $options['g'] = 'I am willing to pay up to 1,200 dollars (USD)';
        $options['h'] = 'I am willing to pay up to 1,500 dollars (USD)';
        $options['i'] = 'I am willing to pay up to 2,000 dollars (USD)';
        $options['j'] = 'I am willing to pay over 2,000 dollars (USD)';
        return $options;
    }

    function budget_check($str) {
        $str = trim($str);
        $error_msg = 'You did not select a valid budget '.$str;

        if ($str == '') {
            return $error_msg;
        }

        $budget_options = $this->_get_budget_options('');

        if (isset($budget_options[$str])) {
            return true;
        } else {
            return $error_msg;
        }
    }

    function _register_response($record_obj, $response_type) {
        //get the current got_responses value for this record
        $got_responses = (int) $record_obj->got_responses;
        $rso = $this->_get_response_status_options(true);

        if (isset($rso[$response_type], $rso)) {
            if($got_responses<$rso[$response_type]) {
                $update_id = (int) $record_obj->id;
                $data['got_responses'] = $rso[$response_type];
                $this->db->update($update_id, $data, 'module_requests');
            }
        }
    }

    function _get_response_status_options($keys_as_text=null) {
        if (!isset($keys_as_text)) {
            $options[0] = 'Be the first to respond!';
            $options[1] = 'Be the first to make an offer!'; //somebody asked a ? but no offer yet
        } else {
            $options['question'] = 1;
            $options['offer'] = 2;            
        }

        return $options;
    }

    function _get_data_from_post() {
        $data['request_title'] = post('request_title', true);
        $data['budget'] = post('budget', true);
        $data['request_details'] = post('request_details', true);
        return $data;
    }

}