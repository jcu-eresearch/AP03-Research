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
	 *
	 * If clustered is true, then bounds are required.
	 * Clustered will return features in clusters, rather than a single feature per location.
	 */
	public function toGeoJSONArray(Model $Model, $bounds = array(), $clustered=false ) {
		$locations = $Model->getLocationsArray();
		$locationFeatures = array();

		if ( $clustered ) {
			// TODO Convert to constants
			$grid_range_longitude = 80;
			$grid_range_latitude  = 40;
			$min_feature_radius = 3;

			if ( !array_key_exists('min_latitude', $bounds) ) {}
			if ( !array_key_exists('max_latitude', $bounds) ) {}
			if ( !array_key_exists('min_longitude', $bounds) ) {}
			if ( !array_key_exists('max_longitude', $bounds) ) {}

			// Use a grid cluster technique.
			// Cut the map up into a grid of 50 x 50
			// The grid is based on the bounds.
			// Transform all locations to the nearest grid position
			$min_longitude = $bounds['min_longitude'];
			$max_longitude = $bounds['max_longitude'];
			$min_latitude = $bounds['min_latitude'];
			$max_latitude = $bounds['max_latitude'];
			
			// if min_lat 30 and max_lat 90
			// max_lat - min_lat = 60
			// 60 is range.
			// grid is 50.
			// 60/50 is 1.2.
			// Each grid point is 1.2 up/down
			// A occurrence at 30 should be in array location 0
			// An occurrence at 40 should be in array location 8
			// An occurrence at 90 should be in array location 50
			// transform is: ( occur_lat - min_lat ) / transform_lat
			// transform_lat = ( max_lat - min_lat ) * range
			$transform_longitude = ( $max_longitude - $min_longitude ) / $grid_range_longitude;
			$transform_latitude = ( $max_latitude - $min_latitude ) / $grid_range_latitude;

			// Create a 2x2 array. Outside array is longitude. Inner array is latitude.
			$transformed_array = array_fill(
				0,
				$grid_range_longitude, 
				array_fill(
					0, 
					$grid_range_latitude, 
					array()
				)
			);
			
			$max_occurrences_at_approximate_location = 1;
			foreach($locations as &$location) {
				$longitude = $location['longitude'];
				$latitude = $location['latitude'];
				if ( GeolocationsBehavior::withinBounds($longitude, $latitude, $bounds) ) {
					$transformed_longitude = floor( ( $longitude - $min_longitude ) / $transform_longitude );
					$transformed_latitude  = floor( ( $latitude - $min_latitude ) / $transform_latitude );

					array_push($transformed_array[$transformed_longitude][$transformed_latitude], $location['id']);
					$occurrences_at_approximate_location = sizeOf($transformed_array[$transformed_longitude][$transformed_latitude]);
					if ( $max_occurrences_at_approximate_location < $occurrences_at_approximate_location) {
						$max_occurrences_at_approximate_location = $occurrences_at_approximate_location;
					}
				}
			}

			for ($i = 0; $i < sizeOf($transformed_array); $i++) {
				$original_longitude_approximation = ( ( $i * $transform_longitude) + $min_longitude + ( $transform_longitude / 2) );
				for ($j = 0; $j < sizeOf($transformed_array[$i]); $j++) {
					$locations_approximately_here       = $transformed_array[$i][$j];
					$locations_approximately_here_size  = sizeOf($transformed_array[$i][$j]);
					if ($locations_approximately_here_size > 0) {
						$original_latitude_approximation  = ( ( $j * $transform_latitude) + $min_latitude + ( $transform_latitude / 2 ));
						$point_radius = ( ceil(log($locations_approximately_here_size) ) + $min_feature_radius);
						$locationFeatures[] = array(
							"type" => "Feature",
							'properties' => array(
								'point_radius' => $point_radius,
								'title' => "".$locations_approximately_here_size." occurrences",
							),
							'geometry' => array(
								'type' => 'Point',
								'coordinates' => array($original_longitude_approximation, $original_latitude_approximation),
							),
						);
					}
				}
			}

		} else {
			foreach($locations as &$location) {
				$longitude = $location['longitude'];
				$latitude = $location['latitude'];
				if ( GeolocationsBehavior::withinBounds($longitude, $latitude, $bounds) ) {
					$locationFeatures[] = array(
						"type" => "Feature",
						'properties' => array(
							'point_radius' => 4
						),
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array($location['longitude'], $location['latitude']),
						),
					);
				}
			}
		}

		$geoObject = array(
			'type' => 'FeatureCollection',
			'features' => &$locationFeatures
		);
		return $geoObject;
	}


	public static function getLocationsWithinBounds($locations, $bounds = array()) {
		$returnArray = array();
		foreach($locations as &$location) {
			$longitude = $location['longitude'];
			$latitude = $location['latitude'];
			if ( GeolocationsBehavior::withinBounds($longitude, $latitude, $bounds) ) {
				array_push($returnArray,$location);
			}
		}
		return $returnArray;
	}

	/**
	 * Returns true if the locations's latitude and longitude are within bounds.
	 *
	 * Only checks against bounds' existing keys.
	 * bounds' keys are:
	 *  min_latitude
	 *  max_latitude
	 *  min_longitude
	 *  max_longitude
	 *
	 * Bounds can be an empty array.
	 */
	public static function withinBounds($longitude, $latitude, $bounds = array()) {
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
