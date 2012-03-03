<!-- File: /app/View/Species/view.ctp -->

<h1><?php echo $species['Species']['name']; ?></h1>

<p>Created: <?php echo $species['Species']['created']; ?></p>
<p>Modified: <?php echo $species['Species']['modified']; ?></p>

<h2>Occurrences</h2>
<pre>
	<?php print_r($species['Occurrence']); ?></p>
</pre>
