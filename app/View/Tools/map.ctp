<?php

// Display a map as an inline image
$inline = true;

$mapRequest = ms_newOwsRequestObj();
$mapRequest->loadparams();

$map_path = realpath(APP.'/Lib/map');
$mapfile = null;
if (array_key_exists('MAP', $this->request->query)) {
	$mapfile = $this->request->query['MAP'];
} else {
	throw new BadRequestException(__('Invalid MAP query parameter'));
}
$map = ms_newMapObj(realpath($map_path.'/'.$mapfile));
$map->loadOWSParameters($mapRequest);

$layer = $map->getLayerByName('TEMPDATA');
$data = $_GET['DATA'];
$layer->set('data', $data);

$map_image = $map->draw();
if ($inline) {
    header('Content-Type: image/png');
    $map_image->saveImage('');
    exit;
}
$image_url = $map_image->saveWebImage();
