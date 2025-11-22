<?php
class Playground_model extends Model {

public function count() {
    echo "Class exists DB: " . (class_exists('DB') ? 'YES' : 'NO') . "<br>";
    echo "Class exists Db: " . (class_exists('Db') ? 'YES' : 'NO') . "<br>";
    
    $rows = $this->live5->get('id', 'df_found_items_ebay');
    
    echo "Object class: " . get_class($this->live5) . "<br>";
    
    $num_rows = count($rows);
    return $num_rows;
}

}