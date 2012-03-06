// Author: Robert Pyke
//
// Assumes that the var species_id has already been set.
// Assumes that open layers, jQuery and jQueryUI are all available.

/*
<script type="text/javascript">
            var map, photos;
            OpenLayers.ProxyHost = (window.location.host == "localhost") ?
                "/cgi-bin/proxy.cgi?url=" : "proxy.cgi?url=";
        
            function init() {
                map = new OpenLayers.Map('map', {
                    restrictedExtent: new OpenLayers.Bounds(-180, -90, 180, 90)
                });
                var base = new OpenLayers.Layer.WMS("Imagery", 
                    ["http://t1.hypercube.telascience.org/tiles?",
		     "http://t2.hypercube.telascience.org/tiles?",
		     "http://t3.hypercube.telascience.org/tiles?",
		     "http://t4.hypercube.telascience.org/tiles?"], 
                    {layers: 'landsat7'}
                );

                var style = new OpenLayers.Style({
                    externalGraphic: "${img_url}",
                    pointRadius: 30
                });

                photos = new OpenLayers.Layer.Vector("Photos", {
                    strategies: [new OpenLayers.Strategy.BBOX()],
                    protocol: new OpenLayers.Protocol.HTTP({
                        url: "http://labs.metacarta.com/flickrbrowse/flickr.py/flickr",
                        params: {
                            format: "WFS",
                            sort: "interestingness-desc",
                            service: "WFS",
                            request: "GetFeatures",
                            srs: "EPSG:4326",
                            maxfeatures: 10
                        },
                        format: new OpenLayers.Format.GML()
                    }),
                    styleMap: new OpenLayers.StyleMap(style)
                });

                map.addLayers([base, photos]);
                map.setCenter(new OpenLayers.LonLat(-116.45, 35.42), 5);
            }
            
        </script>
    </head>
*/

var map, select_control;
$(document).ready(function() {
		map = new OpenLayers.Map('map', {
			// Don't let the user move the map outside of the bounds of the earth
			// Some maps support wrap-around, others don't.
			// To make everything simpler (incl. our BBox strategy), just prevent it from happening.
			restrictedExtent: new OpenLayers.Bounds(-180, -90, 180, 90)
		});

		// The standard open layers layer.
		var wms = new OpenLayers.Layer.WMS(
			"OpenLayers WMS",
			"http://vmap0.tiles.osgeo.org/wms/vmap0",
			{'layers':'basic'} 
		);

		// Google Maps Layers
		// These require a google maps API key
		var gphy = new OpenLayers.Layer.Google(
				"Google Physical",
				{type: G_PHYSICAL_MAP}
		);
		var gmap = new OpenLayers.Layer.Google(
				"Google Streets",
				{numZoomLevels: 20}
		);
		var ghyb = new OpenLayers.Layer.Google(
				"Google Hybrid",
				{type: G_HYBRID_MAP, numZoomLevels: 20}
		);
		var gsat = new OpenLayers.Layer.Google(
				"Google Satellite",
				{type: G_SATELLITE_MAP, numZoomLevels: 22}
		);

		var style = new OpenLayers.Style({
				// externalGraphic: "${img_url}",
				pointRadius: "${point_radius}",
				fillColor: "#ee9900",
				fillOpacity: 0.4,
		});

		var occurrences = new OpenLayers.Layer.Vector("Occurrences", {
			// resFactor determines how often to update the map.
			// See: http://dev.openlayers.org/docs/files/OpenLayers/Strategy/BBOX-js.html#OpenLayers.Strategy.BBOX.resFactor
			// A setting of 1 will mean the map is updated every time its zoom/bounds change.
			strategies: [new OpenLayers.Strategy.BBOX({resFactor: 1.0})],
			protocol: new OpenLayers.Protocol.HTTP({
					url: "../geo_json_occurrences/" + species_id + ".json",
					params: {
						// Place addition request params here..
						bound: true,
						clustered: true
					},

					// See: http://geojson.org/geojson-spec.html For the GeoJSON spec.
					format: new OpenLayers.Format.GeoJSON(),
			}),
			styleMap: new OpenLayers.StyleMap({
				"default": style,
				"select": {
					"fillColor": "#83aeef",
					"fillOpacity": 0.8,
				},
			})
		});

		function onPopupClose(evt) {
			// 'this' is the popup.
			select_control.unselect(this.feature);
		}

		function onFeatureSelect(evt) {
			feature = evt.feature;
			popup = new OpenLayers.Popup.FramedCloud("featurePopup",
				feature.geometry.getBounds().getCenterLonLat(),
				new OpenLayers.Size(100,100),
				"<h2>" + feature.attributes.title + "</h2>" +
				feature.attributes.description,
				null, true, onPopupClose);
				feature.popup = popup;
				popup.feature = feature;
				map.addPopup(popup);
		}

		function onFeatureUnselect(evt) {
			feature = evt.feature;
			if (feature.popup) {
				popup.feature = null;
				map.removePopup(feature.popup);
				feature.popup.destroy();
				feature.popup = null;
			}
		}

		occurrences.events.on({
			'featureselected': onFeatureSelect,
			'featureunselected': onFeatureUnselect
		});
		
		var select_control = new OpenLayers.Control.SelectFeature(
			occurrences, {hover: false}
		);

		map.addControl(select_control);
		select_control.activate();
		
		// Let the user change between layers
		// map.addControl(new OpenLayers.Control.LayerSwitcher());

		map.addLayers([wms, occurrences]);
		map.zoomToMaxExtent();
});
