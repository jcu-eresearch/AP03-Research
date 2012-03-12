<?php
// PHP MapScript example.
// Display a map as an inline image or embedded into an HTML page.
$inline = true;

$mapRequest = ms_newOwsRequestObj();
$mapRequest->loadparams();

$map_path = './';
$mapfile = $_GET['MAP'];
$map = ms_newMapObj($map_path . $mapfile);
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
?>
<HTML>
<HEAD>
<TITLE>PHP MapScript example: Display the map</TITLE>
</HEAD>
<BODY>
<P>PHP MapScript example: Display the map</P>
<IMG SRC=<?php echo $image_url; ?> >
</BODY>
</HTML>
