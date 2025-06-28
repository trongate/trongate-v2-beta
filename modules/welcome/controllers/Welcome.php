<?php
class Welcome extends Trongate {

	/**
	 * Renders the (default) homepage for public access.
	 *
	 * @return void
	 */
	public function index(): void {
		$data['datetime'] = date('l jS \of F Y \a\t H:i');
		$this->view('welcome', $data);
	}

	public function test() {
		$this->view('test');
	}

}