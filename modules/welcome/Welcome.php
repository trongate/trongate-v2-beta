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
=======
    /**
     * Renders the (default) homepage for public access.
     *
     * @return void
     */
    public function index(): void {
        $this->view('stealth_homepage');
    }
>>>>>>> cp3

}
