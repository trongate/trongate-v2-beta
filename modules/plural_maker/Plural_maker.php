<?php
class Plural_maker extends Trongate {

    function get_plural() {
        $post = file_get_contents('php://input');
        $params = json_decode($post, true);

        if (!isset($params['moduleNameSingular'])) {
            http_response_code(400);
            die();
        } else {
            $module_name_singular = trim($params['moduleNameSingular']);
            $plural = $this->_get_plural($module_name_singular);

            $third_bit = segment(3);
            if ($third_bit !== '') {
                $data['record_name_singular'] = $module_name_singular;
                $data['record_name_plural'] = $plural;
                $dir_name = url_title(strtolower($data['record_name_plural']));
                $data['module_folder_name'] = str_replace('-', '_', $dir_name);
                http_response_code(200);
                echo json_encode($data);
                die();
            } else {
                http_response_code(200);
                echo $plural;
            }

        }
    }

    function _get_plural($word) {
        $word = strtolower($word);
        $submitted_words = explode(' ', $word);
        $num_words_submitted = count($submitted_words);
        $word = $submitted_words[$num_words_submitted-1];
        $record = $this->db->get_one_where('singular', $word, 'plural_maker');

        if ($record == false) {
            $plural = $word.'s';
            $plural_string = '';

            foreach ($submitted_words as $submitted_word) {
                $plural_string.=$submitted_word.' ';
            }

            $plural = str_replace($word, $plural, $plural_string);

        } elseif ($num_words_submitted>1) {

            $plural = $record->plural;
            $plural_string = '';
            foreach ($submitted_words as $submitted_word) {
                $plural_string.=$submitted_word.' ';
            }

            $plural = str_replace($word, $plural, $plural_string);

        }

        if (!isset($plural)) {
            $plural = $record->plural;
        }

        $plural = trim($plural);
        $plural = ucwords($plural);
        return $plural;
    }

    function _get_singular($str) {
        $record = $this->db->get_one_where('plural', $str, 'plural_maker');

        if ($record == false) {
            $last_char = substr($str, strlen($str)-1, strlen($str));
            if ($last_char == 's') {
                $singular = substr($str, 0, strlen($str)-1);
            } else {
                $singular = $str;
            }
        } else {
            $singular = $record->singular;
        }

        return $singular;
    }


}