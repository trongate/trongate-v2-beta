<?php
class Welcome extends Trongate {

<<<<<<< HEAD
	/**
	 * Display the default welcome page.
	 *
	 * @return void
	 */
	public function indexX(): void {
	    $data = [
	    	'view_module' => 'welcome',
	    	'view_file' => 'homepage_content'
	    ];
	    $this->templates->public($data);
	}
=======
    public function ahoy() {
        $this->view('ahoy');
    }

    public function icons() {

        $icon_dir = APPPATH . '/public/trongate-icons';
        $data['icons'] = glob($icon_dir . '/*.svg');
        $this->view('icons', $data);


    }

    public function test() {
        $rows = $this->db->get('chapter_number', 'documentation_chapters');
        $counter = 0;
        foreach($rows as $row) {
            
            if ($row->book_id === 1) {
                $counter++;
                echo $counter.': '.$row->chapter_title.',<br>';
            }
            
        }
    }
>>>>>>> 1812b796f7c3086fff759fc5fd48c63bc5af4250

	public function index() {
	    $data = [
	    	'view_module' => 'welcome',
	    	'view_file' => 'default_homepage'
	    ];
		$this->templates->public($data);
	}

	/**
	 * Display the optional database setup instructions page.
	 *
	 * @return void
	 */
	public function database_setup(): void {
	    $this->view('database_setup');
	}

}
