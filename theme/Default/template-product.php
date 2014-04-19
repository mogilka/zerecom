<?php
/****************************************************
*
* @File: 			template.php
* @Package:		GetSimple
* @Action:		Default theme for the GetSimple CMS
*
*****************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php get_page_clean_title(); ?> | <?php get_site_name(); ?>, <?php get_i18n_component('tagline'); ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<?php get_header(); ?>
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="<?php get_theme_url(); ?>/default.css" media="all" />

	<script type="text/javascript"><!--
		try {
			document.execCommand("BackgroundImageCache", false, true);
		} catch(err) {}
		/* IE6 flicker hack from http://dean.edwards.name/my/flicker.html */
	--></script>
</head>

<body id="<?php get_page_slug(); ?>" >
<center><div class="top-shadow">&nbsp;</div></center>
<div class="wrapper">
	<?php get_i18n_component('header'); ?>

	<div id="bodycontent">
		<span id="slidebar">
			<?php gst_superslideshow('transport'); ?>
			<?php gst_superslideshow('placement'); ?>
			<?php gst_superslideshow('sanitary'); ?>
		</span><!-- end div#slidebar -->
		<div class="post">
			<h1><?php get_page_title(); ?></h1>
			<div class="postcontent">
				<?php get_page_content(); ?>
			</div>
		</div>
	</div><!-- end div#bodycontent -->
	
	<div class="clear"></div><?php get_i18n_component('footer'); ?>

</div><!-- end div.wrapper -->
<center><div class="bottom-shadow">&nbsp;</div></center>
</body>
</html>
