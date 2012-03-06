<?php
/**
 * GeolocationsBehavior provides geo location functions for models that have a series of locations associated with them.
 *
 * Expects Model's that use this behaviour to have a toLocationsArray method.
 * Each location must have at least a latitude and longitude.
 */
class GeolocationsBehavior extends ModelBehavior {

	// Store the settings for this model.
	public function setup($Model, $config = array()) {
		$settings = $config;
		$this->settings[$Model->alias] = $settings;
	}

	/**
	 * Dump the object to a geoJSONArray
	 *
	 * If bounds are provided, only show locations within the provided bounds 
	 * bounds is an array of min_latitude, max_latitude, min_longitude, max_longitude
	 */
	public function toGeoJSONArray(Model $Model, $bounds = array() ) {
		$locations = $Model->getLocationsArray();
		$locationFeatures = array();

		foreach($locations as &$location) {
			if ( GeolocationsBehavior::withinBounds($location, $bounds) ) {
				array_push($locationFeatures,array(
					"type" => "Feature",
					'properties' => array(
						'point_radius' => 4
					),
					'geometry' => array(
						'type' => 'Point',
						'coordinates' => array($location['longitude'], $location['latitude']),
					),
				));
			}
		}

		$geoObject = array(
			'type' => 'FeatureCollection',
			'features' => $locationFeatures
		);
		return $geoObject;
	}

	/**
	 * Returns true if the locations's latitude and longitude are within bounds.
	 * Only checks against bounds' keys provided.
	 * bounds keys are:
	 *  min_latitude
	 *  max_latitude
	 *  min_longitude
	 *  max_longitude
	 * Assumes it is fine to not specify a bounds.
	 * i.e. if bounds is an empty array, assumes location is valid.
	 */
	public static function withinBounds($location, $bounds) {
		// > max lat
		if ( array_key_exists('max_latitude', $bounds) ) {
			if ( $latitude > $bounds['max_latitude'] ) {
				return false;
			}
		}

		// < min lat
		if ( array_key_exists('min_latitude', $bounds) ) {
			if ( $latitude < $bounds['min_latitude'] ) {
				return false;
			}
		}

		// > max lon
		if ( array_key_exists('max_longitude', $bounds) ) {
			if ( $longitude > $bounds['max_longitude'] ) {
				return false;
			}
		}

		// < max lon
		if ( array_key_exists('min_longitude', $bounds) ) {
			if ( $longitude < $bounds['min_longitude'] ) {
				return false;
			}
		}

		return true;

	}

}
