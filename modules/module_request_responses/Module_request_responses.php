<?php
class Module_request_responses extends Trongate {

    function _draw_admin_top_rhs() {
        $column = 'status';
        $value = 0;
        $target_table = 'module_request_responses';
        $num_unpublished = $this->db->count_rows($column, $value, $target_table);
        $data['view_module'] = 'module_request_responses';
        $data['num_unpublished'] = $num_unpublished;
        $data['link_text'] = ($num_unpublished>0) ? 'Module Request Responses ('.$num_unpublished.')' : 'Module Request Responses';
        $data['link_attr'] = ($num_unpublished>0) ? array('class' => 'highlight') : [];
        $this->view('module_requests_responses_admin_top_rhs', $data);
    }

    function submit_approve() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $response_id = (int) post('response_id', true);
        $approve = post('approve', true);

        if (($response_id>0) && ($approve == 'Approve')) {
            $data['status'] = 2; //response record is now approved
            $this->db->update($response_id, $data, 'module_request_responses');

            //we need; the record obj for the module_request PLUS the type of response 
            $sql = 'SELECT
                        module_requests.*,
                        module_request_responses.response_type 
                    FROM
                        module_request_responses
                    INNER JOIN
                        module_requests
                    ON
                        module_request_responses.module_request_id = module_requests.id 
                    WHERE 
                        module_request_responses.id = '.$response_id;
            $rows = $this->db->query($sql, 'object');
            if (isset($rows[0])) {
                $module_request_obj = $rows[0];
                $response_type = strtolower($rows[0]->response_type);
                $this->module('module_requests');
                $this->module_requests->_register_response($module_request_obj, $response_type);

                //SEND A MESSAGE, ACKNOWLEDGING THAT A QUESTION/OFFER WAS APPROVED *** START ***

                //figure out who posted the response
                $response_obj = $this->db->get_where($response_id, 'module_request_responses');
                $trongate_user_id = (int) $response_obj->trongate_user_id;
                $member_obj = $this->db->get_one_where('trongate_user_id', $trongate_user_id, 'members');

                //figure out what the module request URL is
                $module_request_code = $module_request_obj->code;

                $send_data['member_obj'] = $member_obj;
                $send_data['module_request_url'] = BASE_URL.'module_requests/browse/'.$module_request_code;
                $this->module('module_request_msg_boss');

                if ($response_type == 'question') {
                    $this->module_request_msg_boss->_send_thanks_question_approved_msg($send_data);
                } elseif($response_type == 'offer') {
                    $this->module_request_msg_boss->_send_thanks_offer_approved_msg($send_data);
                }

                //SEND A MESSAGE, ACKNOWLEDGING THAT A QUESTION/OFFER WAS APPROVED *** END ***

            }

            set_flashdata('The record has been successfully approved.');
        }

        redirect('module_request_responses/manage');
    }

    function submit_conf_delete() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $response_id = (int) post('response_id', true);
        $delete = post('delete', true);

        if (($response_id>0) && ($delete == 'Delete')) {
            $this->db->delete($response_id, 'module_request_responses');
            set_flashdata('The record has been successfully deleted.');
        }

        redirect('module_request_responses/manage');
    }

    function fetch_feed_data() {
        $code = segment(3);
        $params['code'] = $code;
        $sql = 'SELECT
                    module_requests.request_title,
                    module_requests.date_created,
                    module_requests.open,
                    module_requests.created_by,
                    module_requests.published,
                    module_request_responses.*,
                    members.username,
                    members.code as member_code 
                FROM
                    module_requests
                INNER JOIN
                    module_request_responses
                ON
                    module_requests.id = module_request_responses.module_request_id
                INNER JOIN
                    members
                ON
                    module_request_responses.trongate_user_id = members.trongate_user_id 
                WHERE 
                    module_requests.code = :code 
                AND 
                    module_request_responses.status>1 
                ORDER BY 
                    module_request_responses.date_created';
        $rows = $this->db->query_bind($sql, $params, 'object');
        $rows = $this->_prep_feed_data($rows);
        http_response_code(200);
        echo json_encode($rows);
    }

    function _prep_feed_data($rows) {
        foreach($rows as $key => $value) {
            $rows[$key]->date_created = date('jS M Y \a\t H:i', $value->date_created);
            $rows[$key]->comment = nl2br($value->comment);
            $rows[$key]->class = strtolower(url_title($value->response_type));

            $date_accepted = (int) $value->date_accepted;
            if ($date_accepted>0) {
                $rows[$key]->date_accepted_desc = date('F jS Y \a\t H:i \G\M\T', $date_accepted);
            }
        }
        return $rows;
    }

    function manage() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $sql = 'SELECT
                    module_request_responses.*,
                    members.username 
                FROM
                    module_request_responses
                INNER JOIN
                    members
                ON
                    module_request_responses.trongate_user_id = members.trongate_user_id 
                WHERE 
                    module_request_responses.status = 1 
                ORDER BY 
                    module_request_responses.id DESC';

        $this->module('module_request_status');
        $data['all_status_options'] = $this->module_request_status->_get_all_status_options();
        $data['rows'] = $this->db->query($sql, 'object');  
        $data['rows'] = $this->_prep_for_admin($data['rows'], $data['all_status_options']);     
        $data['view_file'] = 'manage';
        $this->template('bootstrappy', $data);
    }

    function _prep_for_admin($rows, $all_status_options) {
        foreach($rows as $key => $value) {
            $module_request_url = BASE_URL.'module_requests/goto/'.$value->module_request_id;
            $status_title = (isset($all_status_options[$value->status])) ? $all_status_options[$value->status] : 'unknown';
            $rows[$key]->module_request_url = $module_request_url;
            $rows[$key]->status_title = $status_title;
        }
        return $rows;
    }

    function submit_ask_question() {
        $code = segment(3);

        //make sure logged in
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        //make sure the code is valid
        $record_obj = $this->db->get_one_where('code', $code, 'module_requests');

        if ($record_obj == false) {
            redirect('ouch');
        }

        //make sure the record is; published, open and does NOT belong to THIS member
        $this->module('module_requests');
        $this->module_requests->_make_sure_bid_allowed($token, $record_obj);

        $this->validation->set_rules('comment', 'question', 'required|min_length[4]');
        $this->validation->set_rules('understood', 'terms and conditions checkbox', 'required');
        
        $result = $this->validation->run();
        if ($result == true) {

            $this->module('members');
            $member_obj = $this->members->_get_member_obj($token);
            $data['comment'] = post('comment', true);
            $data['offer_value'] = 0;
            $data['offer_currency'] = '';
            $data['response_type'] = 'Question';
            $data['trongate_user_id'] = $member_obj->trongate_user_id;
            $data['status'] = 1; // submitted
            $data['date_created'] = time();
            $data['module_request_id'] = $record_obj->id;
            $data['response_code'] = make_rand_str(16);
            $data['answer_for'] = '';
            $this->db->insert($data, 'module_request_responses');

            $send_data['member_obj'] = $member_obj;
            $send_data['module_request_url'] = BASE_URL.'module_requests/browse/'.segment(3);
            $this->module('module_request_msg_boss');
            $this->module_request_msg_boss->_send_thanks_question_msg($send_data);

            $finish_url = BASE_URL.'module_request_responses/thankyou';
            redirect($finish_url);
        } else {
            $this->ask_question();
        }

    }

    function submit_compose_answer() {
        $code = segment(3);
        $question_code = segment(4);

        //make sure logged in
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        //make sure the code is valid
        $record_obj = $this->db->get_one_where('code', $code, 'module_requests');

        if ($record_obj == false) {
            redirect('ouch');
        } else {
            $this->_make_sure_valid_question_code($question_code, $record_obj);
        }

        //make sure the record belongs to THIS member
        $this->module('module_requests');
        $this->module_requests->_make_sure_request_owner($token, $record_obj);

        $this->validation->set_rules('comment', 'answer', 'required|min_length[4]');
        $this->validation->set_rules('understood', 'terms and conditions checkbox', 'required');
        
        $result = $this->validation->run();
        if ($result == true) {

            $this->module('members');
            $member_obj = $this->members->_get_member_obj($token);
            $data['comment'] = post('comment', true);
            $data['offer_value'] = 0;
            $data['offer_currency'] = '';
            $data['response_type'] = 'Answer';
            $data['trongate_user_id'] = $member_obj->trongate_user_id;
            $data['status'] = 2; // published
            $data['date_created'] = time();
            $data['module_request_id'] = $record_obj->id;
            $data['response_code'] = make_rand_str(16);
            $data['answer_for'] = segment(4);
            $this->db->insert($data, 'module_request_responses');

            $finish_url = BASE_URL.'module_requests/browse/'.$record_obj->code;
            redirect($finish_url);
        } else {
            $this->compose_answer();
        }

    }

    function submit_make_offer() {
        $code = segment(3);

        //make sure logged in
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        //make sure the code is valid
        $record_obj = $this->db->get_one_where('code', $code, 'module_requests');

        if ($record_obj == false) {
            redirect('ouch');
        }

        //make sure the record is; published, open and does NOT belong to THIS member
        $this->module('module_requests');
        $this->module_requests->_make_sure_bid_allowed($token, $record_obj);

        $this->validation->set_rules('offer_value', 'offer value', 'numeric|greater_than[0]');
        $this->validation->set_rules('offer_currency', 'currency', 'required|max_length[55]');
        $this->validation->set_rules('understood', 'terms and conditions checkbox', 'required');

        $result = $this->validation->run();
        if ($result == true) {

            $this->module('members');
            $member_obj = $this->members->_get_member_obj($token);
            $data = $this->_get_data_from_post();
            $data['response_type'] = 'Offer';
            $data['trongate_user_id'] = $member_obj->trongate_user_id;
            $data['status'] = 1; // submitted
            $data['date_created'] = time();
            $data['module_request_id'] = $record_obj->id;
            $data['response_code'] = make_rand_str(16);
            $data['answer_for'] = '';
            $this->db->insert($data, 'module_request_responses');

            //send internal messages
            $send_data['member_obj'] = $member_obj;
            $send_data['module_request_url'] = BASE_URL.'module_requests/browse/'.segment(3);
            $this->module('module_request_msg_boss');
            $this->module_request_msg_boss->_send_thanks_offer_msg($send_data);

            $finish_url = BASE_URL.'module_request_responses/thankyou';
            redirect($finish_url);
        } else {
            $this->make_offer();
        }

    }

    function thankyou() {
        $data['view_file'] = 'thankyou';
        $this->template('public', $data);
    }

    function ask_question() {
        $code = segment(3);
        $update_id = (int) segment(4);

        //make sure logged in
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        //make sure the code is valid
        $record_obj = $this->db->get_one_where('code', $code, 'module_requests');

        if ($record_obj == false) {
            redirect('ouch');
        }

        $submit = post('submit');

        if (($submit == '') && ($update_id>0)) {
            $data = $this->_get_data_from_db($update_id);
        } else {
            $data['comment'] = post('comment', true);
        }

        //make sure the record is; published, open and does NOT belong to THIS member
        $this->module('module_requests');
        $this->module_requests->_make_sure_bid_allowed($token, $record_obj);

        $data['cancel_url'] = BASE_URL.'module_requests/browse/'.segment(3);
        $data['understood'] = (int) post('understood');
        $data['form_location'] = str_replace('/ask_question/', '/submit_ask_question/', current_url());
        $data['view_file'] = 'ask_question';
        $this->template('public', $data);
    }

    function _make_sure_valid_question_code($question_code, $record_obj) {
        $params['module_request_id'] = (int) $record_obj->id;
        $params['response_code'] = $question_code;
        $sql = 'SELECT * FROM module_request_responses WHERE module_request_id = :module_request_id 
                AND response_code = :response_code';

        $rows = $this->db->query_bind($sql, $params, 'object');
        $num_rows = count($rows);

        if ($num_rows>0) {
            return true;
        } else {
            redirect('forbidden');
        }
    }

    function compose_answer() {
        $code = segment(3);
        $question_code = segment(4);

        //make sure logged in
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        //make sure the code is valid
        $record_obj = $this->db->get_one_where('code', $code, 'module_requests');

        if ($record_obj == false) {
            redirect('ouch');
        } else {
            $this->_make_sure_valid_question_code($question_code, $record_obj);
        }

        $submit = post('submit');
        $data['comment'] = post('comment', true);
        
        //make sure the record belongs to THIS member
        $this->module('module_requests');
        $this->module_requests->_make_sure_request_owner($token, $record_obj);

        $data['cancel_url'] = BASE_URL.'module_requests/browse/'.segment(3);
        $data['understood'] = (int) post('understood');
        $data['form_location'] = str_replace('/compose_answer/', '/submit_compose_answer/', current_url());
        $data['view_file'] = 'compose_answer';
        $this->template('public', $data);
    }

    function make_offer() {
        $code = segment(3);
        $update_id = (int) segment(4);

        //make sure logged in
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed('any member level');

        //make sure the code is valid
        $record_obj = $this->db->get_one_where('code', $code, 'module_requests');

        if ($record_obj == false) {
            redirect('ouch');
        }

        $submit = post('submit');

        if (($submit == '') && ($update_id>0)) {
            $data = $this->_get_data_from_db($update_id);
        } else {
            $data = $this->_get_data_from_post();
        }

        //make sure the record is; published, open and does NOT belong to THIS member
        $this->module('module_requests');
        $this->module_requests->_make_sure_bid_allowed($token, $record_obj);

        $data['cancel_url'] = BASE_URL.'module_requests/browse/'.segment(3);
        $data['understood'] = (int) post('understood');
        $data['form_location'] = str_replace('/make_offer/', '/submit_make_offer/', current_url());
        $data['view_file'] = 'make_offer';
        $this->template('public', $data);
    }

    function _get_data_from_db($update_id) {
        $record_obj = $this->db->get_where($update_id, 'module_request_responses');
        if ($record_obj == false) {
            return false;
        } else {
            $data = (array) $record_obj;
            return $data;
        }
    }

    function _get_data_from_post() {
        $data['offer_value'] = post('offer_value', true);
        $data['offer_currency'] = post('offer_currency', true);
        $data['comment'] = post('comment', true);
        return $data;
    }

}