<?php
App::uses('AppController', 'Controller');
/**
 * Species Controller
 *
 * @property Species $Species
 */
class SpeciesController extends AppController {
	public $components = array('RequestHandler');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->set('title_for_layout', 'Species - Index');

		$this->Species->recursive = 0;
		$this->set('species', $this->paginate());

		// Specify the output for the json view.
		$this->set('_serialize', 'species');
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->set('title_for_layout', 'Species - View');

		$this->Species->id = $id;
		if (!$this->Species->exists()) {
			throw new NotFoundException(__('Invalid species'));
		}
		$this->set('species', $this->Species->read(null, $id));

		// Specify the output for the json view.
		$this->set('_serialize', 'species');
	}

/**
 * occurrences method
 *
 * @param string $id
 * @return void
 */
	public function occurrences($id = null) {
		$this->set('title_for_layout', 'Species - Occurrences');

		$this->Species->id = $id;
		if (!$this->Species->exists()) {
			throw new NotFoundException(__('Invalid species'));
		}

		$species = $this->Species->read(null, $id);
		$occurrences = $species['Occurrence'];

		$this->set('species', $species);
		$this->set('occurrences', $occurrences);

		// Specify the output for the json view.
		$this->set('_serialize', 'occurrences');
	}


/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Species->create();
			if ($this->Species->save($this->request->data)) {
				$this->Session->setFlash(__('The species has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The species could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Species->id = $id;
		if (!$this->Species->exists()) {
			throw new NotFoundException(__('Invalid species'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Species->save($this->request->data)) {
				$this->Session->setFlash(__('The species has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The species could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Species->read(null, $id);
		}
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Species->id = $id;
		if (!$this->Species->exists()) {
			throw new NotFoundException(__('Invalid species'));
		}
		if ($this->Species->delete()) {
			$this->Session->setFlash(__('Species deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Species was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
