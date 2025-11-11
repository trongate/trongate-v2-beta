<?php
class Properties extends Trongate {

    private $module = 'properties';

    function get_address_types() {
        $data[] = 'American';
        $data[] = 'British';
        $data[] = 'Canadian';
        $data[] = 'International';
        echo json_encode($data);
    }

    function get_address_data($input) {
        $post = file_get_contents('php://input');
        $posted_data = json_decode($post, true);
        $address_type = $posted_data['addressType'];
        $view_file = 'address_data_'.strtolower($address_type);
        $data = [];
        $address_str = $this->view($view_file, $data, true);
        echo $address_str;
    }

    function get_fields_info() {
        $sql = 'SELECT
                properties.*,
                properties_suggestions.suggestion
                FROM
                properties_suggestions
                JOIN associated_properties_and_properties_suggestions
                ON properties_suggestions.id = associated_properties_and_properties_suggestions.properties_suggestions_id 
                JOIN properties
                ON associated_properties_and_properties_suggestions.properties_id = properties.id
                ORDER BY properties.priority,properties_suggestions.suggestion';
        $rows = $this->db->query($sql, 'object');
        $suggestions_for_this_property = [];
        $current_property_type = '';

        foreach ($rows as $key => $value) {
            $row_data['property_title'] = $value->property_title;
            $row_data['suggestion'] = $value->suggestion;
            $suggestions_data[] = $row_data;
            $default_max_length[$value->property_title] = $value->default_max_length;
            $default_col_size[$value->property_title] = $value->default_col_size;
            $checkboxes[$value->property_title] = json_decode($value->checkboxes);
            $property_blocks[$value->property_title] = $value->property_title;
            $validation_rules[$value->property_title] = json_decode($value->validation_rules);
        }

        foreach ($property_blocks as $key => $value) {
            $fields['property_name'] = $value;
            $fields['property_label'] = str_replace('_', ' ', $value);
            $fields['validation_rules'] = $validation_rules[$key];
            $property_label = $fields['property_label'];

            unset($suggestions);

            foreach ($suggestions_data as $key => $suggestion_info) {
                if ($suggestion_info['property_title'] == $value) {
                    $suggestions[] = $suggestion_info['suggestion'];
                }
            }
            
            $fields['default_col_size'] = $default_col_size[$value];
            $fields['checkboxes'] = $checkboxes[$value];
            $fields['suggestions'] = $suggestions;
            $fields_data[$property_label] = $fields;
        }

        echo json_encode($fields_data); die();
    }

    function manage() {
        $this->module('trongate_security');
        $this->module('trongate_tokens');
        $this->trongate_security->_make_sure_allowed();

        $user_id = $this->trongate_security->_get_user_id();
        $data['token'] = $this->trongate_tokens->_fetch_token($user_id);
        $data['order_by'] = 'id';

        //format the pagination
        $data['total_rows'] = $this->db->count('properties'); 
        $data['record_name_plural'] = 'properties';

        $data['headline'] = 'Manage Properties';
        $data['view_module'] = 'properties';
        $data['view_file'] = 'manage';

        $this->template('admin', $data);
    }

    function show() {
        $this->module('trongate_security');
        $this->module('trongate_tokens');
        $this->trongate_security->_make_sure_allowed();

        $update_id = segment(3);
    
        if ((!is_numeric($update_id)) && ($update_id != '')) {
            redirect('properties/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $user_id = $this->trongate_security->_get_user_id();
        $data['token'] = $this->trongate_tokens->_fetch_token($user_id);

        if ($data == false) {
            redirect('properties/manage');
        } else {
            $data['form_location'] = BASE_URL.'properties/submit/'.$update_id;
            $data['update_id'] = $update_id;
            $data['headline'] = 'Property Information';
            $data['view_file'] = 'show';
            $this->template('admin', $data);
        }
    }

    function create() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = segment(3);
        $submit = post('submit', true);

        if ((!is_numeric($update_id)) && ($update_id != '')) {
            redirect('properties/manage');
        }

        //fetch the form data
        if (($submit == '') && ($update_id>0)) {
            $data = $this->_get_data_from_db($update_id);
        } else {
            $data = $this->_get_data_from_post();
        }

        $data['headline'] = $this->_get_page_headline($update_id);

        if ($update_id>0) {
            $data['cancel_url'] = BASE_URL.'properties/show/'.$update_id;
            $data['btn_text'] = 'UPDATE PROPERTY DETAILS';
        } else {
            $data['cancel_url'] = BASE_URL.'properties/manage';
            $data['btn_text'] = 'CREATE PROPERTY RECORD';
        }


        $additional_includes_top[] = 'https://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css';
        $additional_includes_top[] = 'https://trentrichardson.com/examples/timepicker/jquery-ui-timepicker-addon.css';
        $additional_includes_top[] = 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"';
        $additional_includes_top[] = 'https://trentrichardson.com/examples/timepicker/jquery-ui-timepicker-addon.js';
        $additional_includes_top[] = BASE_URL.'admin_files/js/i18n/jquery-ui-timepicker-addon-i18n.min.js';
        $additional_includes_top[] = BASE_URL.'admin_files/js/jquery-ui-sliderAccess.js';
        $data['additional_includes_top'] = $additional_includes_top;  

        $data['form_location'] = BASE_URL.'properties/submit/'.$update_id;
        $data['update_id'] = $update_id;
        $data['view_file'] = 'create';
        $this->template('admin', $data);
    }

    function _get_page_headline($update_id) {
        //figure out what the page headline should be (on the properties/create page)
        if (!is_numeric($update_id)) {
            $headline = 'Create New Property Record';
        } else {
            $headline = 'Update Property Details';
        }

        return $headline;
    }

    function submit() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit', true);

        if ($submit == 'Submit') {

            $this->validation->set_rules('property_title', 'Property Title', 'required');
            
            $result = $this->validation->run();

            if ($result == true) {

                $update_id = segment(3);
                $data = $this->_get_data_from_post();
                if (is_numeric($update_id)) {
                    //update an existing record
                    $this->db->update($update_id, $data, 'properties');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->db->insert($data, 'properties');
                    $flash_msg = 'The record was successfully created';
                }
    
                set_flashdata($flash_msg);
                redirect('properties/show/'.$update_id);

            } else {
                //form submission error
                $this->create();
            }

        }

    }

    function submit_delete() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit', true);

        if ($submit == 'Submit') {
            $update_id = segment(3);

            if (!is_numeric($update_id)) {
                die();
            } else {
                $data['update_id'] = $update_id;

                //delete all of the comments associated with this record
                $sql = 'delete from comments where target_table = :module and update_id = :update_id';
                $data['module'] = $this->module;
                $this->db->query_bind($sql, $data);

                //delete the record
                $this->db->delete($update_id, $this->module);

                //set the flashdata
                $flash_msg = 'The record was successfully deleted';
                set_flashdata($flash_msg);

                //redirect to the manage page
                redirect('properties/manage');
            }
        }        
    }

    function _get_data_from_db($update_id) {
        $properties = $this->db->get_where($update_id, 'properties');

        if ($properties == false) {
            $this->template('error_404');
            die();
        } else {
            $data['property_title'] = $properties->property_title;            
            return $data;        
        }
    }

    function _get_data_from_post() {
        $data['property_title'] = post('property_title', true);        
        return $data;
    }

}