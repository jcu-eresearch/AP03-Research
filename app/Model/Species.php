<?php

// File: app/Model/Species.rb
// Author: Robert Pyke

class Species extends AppModel {
	public $name = 'Species';

	public $actsAs = array(
		'Geolocations' => array()
	);

	// A Species has many occurrences.
	public $hasMany = array(
		'Occurrence' => array(
			'className' => 'Occurrence',
			'dependent' => true,
		)
	);

	// Specify validation for Species
	// See API: http://book.cakephp.org/2.0/en/models/data-validation.html
	public $validate = array(
		// NOTE:
		// From the Cake PHP Book:
		// When using multiple rules per field the ‘required’ and ‘allowEmpty’ keys need to be used only once in the first rule.
		'name' => array(
			// Can't be empty string
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field must be specified',
				'required' => true,
				'allowEmpty' => false,
			),
			// Can't be duplicate (shouldn't have two species with same name)
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This field must be unique',
			)
		)
	);

	// Return an array of locations
	public function getLocationsArray() {
		return $this->data['Occurrence'];
	}
}
