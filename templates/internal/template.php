<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
    <meta charset="utf-8">
    <title>Wella's Stage | Trend Vision Awards 2012</title>
	<?php require_once('cocoasHead.js'); ?>
	<?php require_once('cocoasScripts.js'); ?>
	<?php require_once('templates/head.php'); ?>
</head>
<body>

    <div id="background_popups"></div>

<!-- start header -->
	<?php
	require_once($root.'header.php'); ?>
<!-- end header -->
<!-- start content -->
<?php
	require_once($view);
?>
<!-- end content -->
<!-- start footer -->
	<?php require_once($root.'footer.php'); ?>
<!-- end footer -->
</body>
</html>
