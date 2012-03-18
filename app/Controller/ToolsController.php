<?php
App::uses('AppController', 'Controller');
App::uses('map', 'Lib');

/**
 * Tools Controller
 */
class ToolsController extends AppController {
	public $components = array('RequestHandler');
	public $uses = null;

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		// Pass through to view
	}

	/**
	 * map method
	 *
	 * @return void
	 */
	public function map() {
		// Pass through to view
		$this->layout = false;
	}

}
