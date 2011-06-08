<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php __('OW.GS'); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		//echo $this->Html->css('cake.generic');
		echo $this->Html->css('style');

		echo $scripts_for_layout;
	?>
</head>
<body>
	<div id="content">

		<?php echo $this->Session->flash(); ?>

		<?php 
			echo $content_for_layout; 
			echo $this->Session->flash('auth');
	    ?>

	</div>
	<div id="footer"></div>
</body>
</html>