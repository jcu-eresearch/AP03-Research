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
		$this->layout = false;

		$map_request = ms_newOwsRequestObj();
		$map_request->loadparams();

		$map_path = realpath(APP.'/Lib/map');
		$map_file = null;
		if (array_key_exists('MAP', $this->request->query)) {
			$map_file = $this->request->query['MAP'];
		} else {
			throw new BadRequestException(__('Invalid query. Missing MAP query parameter'));
		}
		
		$map = ms_newMapObj(realpath($map_path.'/'.$map_file));
		$map->loadOWSParameters($map_request);

		$data = null;
		if (array_key_exists('DATA', $this->request->query)) {
			$data = $this->request->query['DATA'];
		} else {
			throw new BadRequestException(__('Invalid query. Missing DATA query parameter'));
		}

		$layer = $map->getLayerByName('TEMPDATA');
		$layer->set('data', $data);

		$map_image = $map->draw();

		// Pass the map image through to view
		$this->set('map_image', $map_image);

	}

}
