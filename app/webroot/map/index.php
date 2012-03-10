<?php
// PHP MapScript example.
// Display a map as an inline image or embedded into an HTML page.
$inline = true;
$map_path = './';
$mapfile = 'tiff.map';
$map = ms_newMapObj($map_path . $mapfile);

$species_id = $_GET['SPECIESID'];
$bbox = $_GET['BBOX'];
$bbox_exploded = explode(',', $bbox);
$map->setExtent($bbox_exploded[0], $bbox_exploded[1], $bbox_exploded[2], $bbox_exploded[3]);
$map->set('height', 256);
$map->set('width', 256);

$layer = $map->getLayerByName('TEMPDATA');
$layer->set('data', $species_id.'.tiff');

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
<TITLE>PHP MapScript example: Display the map <?php print_r($bbox)?></TITLE>
</HEAD>
<BODY>
<P>PHP MapScript example: Display the map <?php print_r($bbox_exploded)?></P>
<IMG SRC=<?php echo $image_url; ?> >
</BODY>
</HTML>
