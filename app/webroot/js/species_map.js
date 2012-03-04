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

var map;
$(document).ready(function() {
		map = new OpenLayers.Map('map', {
			// Don't let the user move the map outside of the bounds of the earth
			// Some maps support wrap-around, others don't.
			// To make everything simpler (incl. our BBox strategy), just prevent it from happening.
			restrictedExtent: new OpenLayers.Bounds(-180, -90, 180, 90)
		});

		// Let the user change between layers
		map.addControl(new OpenLayers.Control.LayerSwitcher());

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
				externalGraphic: "${img_url}",
				pointRadius: 30
		});

		var occurrences = new OpenLayers.Layer.Vector("Photos", {
				strategies: [new OpenLayers.Strategy.BBOX()],
				protocol: new OpenLayers.Protocol.HTTP({
						url: "http://localhost",
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

		map.addLayers([wms, gphy, gmap, ghyb, gsat, occurrences]);

		map.zoomToMaxExtent();
});
