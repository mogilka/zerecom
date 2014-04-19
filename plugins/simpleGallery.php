<?php
/*
Plugin Name: SimpleGallery
Description: Enables a powerfull php gallery, just upload pictures to server using ftp and everything is done automatically.
Version: 0.91
Author: Martin Varga(smooo3), original script creator Daniel Wacker
Author URI: http://www.majstriwebu.sk/
Licence type GNU GPL
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 
	'SimpleGallery', 	
	'0.92', 		
	'Martin Varga',
	'http://www.majstriwebu.com/', 
	'Enables a powerfull php gallery, just upload pictures to server using ftp and everything is done automatically.',
	'plugins',
	'gallery_admin' 
);

# activate filter
add_action('plugins-sidebar', 'createSideMenu', array($thisfile, 'SimpleGallery'));

# definitions
define('GSGALLERYPATH', GSROOTPATH .'gallery/');

# gallery definitions
define('GAL_BACKTO', '"To previous directory" button name in gallery');
define('GAL_MAXPICS', 'Number of pictures per one gallery page');
define('GAL_THUMBSIZE', 'Generated thumbnail size (px)');
define('GAL_LIGHTBOX', 'Enable lightbox - beautify the gallery - recommended');
define('GAL_BROWSESUBDIRS', 'Browse sub-directories');
define('GAL_SHOWTITLE', 'Show title in simpleGallery');
define('GAL_IMGBACKGRND', 'Image background color(color in RGB format without #, letters or numbers only)');
define('GAL_NAME', 'If show title is checked, this will be shown on the top of the gallery');
define('GAL_SAVESETTINGS', 'Save gallery settings');
define('GAL_COLORCHANGE','If you change color, it is neccessary to delete all "thumbs" directories to apply change!');

# functions
function gallery_admin() {
?>
<h3>SimpleGallery</h3>
<?php

$relative = '../';
$file='gallsettings.xml';
$path 		= $relative.'plugins/galleryfiles/';
$data 		= getXML($path . $file);
$err 			= '';

$BACKTO		=$data->BACKTO;
$MAXPICS		=$data->MAXPICS;
$THUMBSIZE	=$data->THUMBSIZE . '';
$IMGBACK		=$data->IMGBACK;
$BEAUTY		=$data->BEAUTY;
$BROWSE		=$data->BROWSE;
$SHOWTTL 	=$data->SHOWTTL;
$GALNAME		=$data->NAME;	

// were changes submitted?
if(isset($_POST['submitted']))
{	
	$nonce = $_POST['nonce'];
	if(!check_nonce($nonce, "save_settings"))
		die("CSRF detected!");	

	if(isset($_POST['backto'])) { 
		$BACKTO = $_POST['backto']; 
	}
	
	if(isset($_POST['maxpics'])) { 
		$MAXPICS = $_POST['maxpics']; 
	}
	
	if(isset($_POST['thumbsize'])) { 
		$THUMBSIZE = $_POST['thumbsize']; 
	} 
	
	if(isset($_POST['imgback'])) { 
		$IMGBACK = $_POST['imgback']; 
	} 
	
	if(isset($_POST['name'])) { 
		$GALNAME= $_POST['name']; 
	} 
	
	$BEAUTY = @$_POST['beauty']; 
	$BROWSE = @$_POST['browse'];
	$SHOWTTL = @$_POST['showttl']; 
	
	// create new site data file
		$ufile = 'gallsettings.xml';
		$xmls = @new SimpleXMLExtended('<item></item>');
		$note = $xmls->addChild('BACKTO');
		$note->addCData($BACKTO);
		$note = $xmls->addChild('MAXPICS');
		$note->addCData(@$MAXPICS);
		$note = $xmls->addChild('THUMBSIZE');
		$note->addCData(@$THUMBSIZE);
		$note = $xmls->addChild('IMGBACK');
		$note->addCData(@$IMGBACK);
		$note = $xmls->addChild('BEAUTY');
		$note->addCData(@$BEAUTY);
		$note = $xmls->addChild('BROWSE');
		$note->addCData(@$BROWSE);
		$note = $xmls->addChild('SHOWTTL');
		$note->addCData(@$SHOWTTL);
		$note = $xmls->addChild('NAME');
		$note->addCData(@$GALNAME);
		exec_action('settings-website');
		XMLsave($xmls, $path . $ufile);
	
		$err = "false";
}

$beautychck = ''; $browsechck = ''; $showttlchck = '';

if ($BEAUTY != '' ) { $beautychck = 'checked'; }
if ($BROWSE != '' ) { $browsechck = 'checked'; }
if ($SHOWTTL != '' ) { $showttlchck = 'checked'; }
	
  if (!file_exists(GSGALLERYPATH)) {
    echo '<p>The directory "<i>GSROOTPATH/gallery</i>" does
    not exist. It is required for this plugin to function properly. Please
    create it manually and make sure it is writable.</p>';
  } else { ?>
  <form class="largeform" action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?id=simpleGallery" method="post" accept-charset="utf-8" >
	<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save_settings"); ?>" />
	<p><b><?php echo GAL_BACKTO; ?>:</b><br /><input class="text" name="backto" type="text" value="<?php if(isset($BACKTO1)) { echo stripslashes($BACKTO1); } else { echo stripslashes($BACKTO); } ?>" /></p>
	<p><b><?php echo GAL_MAXPICS; ?>:</b><br /><input class="text" name="maxpics" type="text" value="<?php if(isset($MAXPICS1)) { echo stripslashes($MAXPICS1); } else { echo stripslashes($MAXPICS); } ?>" /></p>
	<p><b><?php echo GAL_THUMBSIZE; ?>:</b><br /><input class="text" name="thumbsize" type="text" value="<?php if(isset($THUMBSIZE1)) { echo stripslashes($THUMBSIZE1); } else { echo stripslashes($THUMBSIZE); } ?>" /></p>
	<p><input name="beauty" id="beauty" type="checkbox" value="1" <?php echo $beautychck; ?>  /> &nbsp;<label class="clean" for="beauty" ><?php echo GAL_LIGHTBOX; ?></label></p>
	<p><input name="browse" id="browse" type="checkbox" value="1" <?php echo $browsechck; ?> /> &nbsp;<label class="clean" for="browse" ><?php echo GAL_BROWSESUBDIRS;?></label></p>
	<p><input name="showttl" id="showttl" type="checkbox" value="1" <?php echo $showttlchck; ?> /> &nbsp;<label class="clean" for="showttl" ><?php echo GAL_SHOWTITLE;?></label></p>
	<p><b><?php echo GAL_NAME; ?>:</b><br /><input class="text" name="name" type="text" value="<?php if(isset($GALNAME1)) { echo stripslashes($GALNAME1); } else { echo stripslashes($GALNAME); } ?>" /></p>
	<p><b><?php echo GAL_IMGBACKGRND; ?>:</b><br /><input class="text" name="imgback" type="text" value="<?php if(isset($IMGBACK1)) { echo stripslashes($IMGBACK1); } else { echo stripslashes($IMGBACK); } ?>" /></p>
	<p><?php echo GAL_COLORCHANGE; ?></p>
 <p><input class="submit" type="submit" name="submitted" value="<?php echo GAL_SAVESETTINGS; ?>" /></p>
 </form>
<?php  
  }  
}

function gallery() {
// getting settings form xml file
$data=getXML('./plugins/galleryfiles/gallsettings.xml');
$navrat			=$data->BACKTO;
$maxpics 		=$data->MAXPICS;
$thumbsize	=$data->THUMBSIZE . '';
$bg				=$data->IMGBACK;
$galleryname =$data->NAME;
if ($data->BEAUTY != '') {$lightbox= 'true';} else {$lightbox = 'false';}
if ($data->SHOWTTL != '') {$titlex = 'true';} else {$titlex = 'false';}
if ($data->BROWSE != '') {$subdirs = 'true';} else {$subdirs = 'false';}

//main gallery code starts here, the output code is stored in variable $content 

$charset = "UTF-8";
$thumbdir = 'thumbs';
// must stay relative, in other case it allows to browse all directories
$picdir = './gallery'; 

isset($_SERVER) || ($error = error('php'));
function_exists('imagecreate') || ($error = error('gd'));
function_exists('imagejpeg') || ($error = error('jpg'));
if (function_exists('ini_set')) @ini_set('memory_limit', -1);
$fontsize = 2;

$words = array(
'gallery' => 'gallery',
 'error' => 'Error',
 'php_error' => 'PHP >= 4.1 is required.',
 'gd_error' => 'GD Library is required. See http://www.boutell.com/gd/.',
 'jpg_error' => 'JPEG software is required. See ftp://ftp.uu.net/graphics/jpeg/.',
 'mkdir_error' => 'Write permission is required in this folder.',
 'opendir_error' => 'The directory "%1" can not be read.'
);

function word ($word) {
global $words;
return html($words[$word]);
}

function html ($word) {
global $charset;
return htmlentities($word, ENT_COMPAT, $charset);
}

function error ($word, $arg = '') {
global $words;
return html(str_replace('%1', $arg, $words[$word .'_error']));
}
if ($lightbox == 'true') 
{$content='<script type="text/javascript" src="./plugins/galleryfiles/js/jquery.min.js"></script>
<script type="text/javascript" src="./plugins/galleryfiles/js/slimbox2.js"></script>
<style type="text/css">
/* SLIMBOX */
#lbOverlay{position:fixed;z-index:9999;left:0;top:0;width:100%;height:100%;background-color:#000;cursor:pointer;}
#lbCenter,#lbBottomContainer{position:absolute;z-index:9999;overflow:hidden;background-color:#fff;}
.lbLoading{background:#fff url(./plugins/galleryfiles/img/loading.gif) no-repeat center;}
#lbImage{position:absolute;left:0;top:0;border:10px solid #fff;background-repeat:no-repeat;}
#lbPrevLink,#lbNextLink{display:block;position:absolute;top:0;width:50%;outline:none;}
#lbPrevLink{left:0;}
#lbPrevLink:hover{background:transparent url(./plugins/galleryfiles/img/prevlabel.gif) no-repeat 0 15%;}
#lbNextLink{right:0;}
#lbNextLink:hover{background:transparent url(./plugins/galleryfiles/img/nextlabel.gif) no-repeat 100% 15%;}
#lbBottom{font-family:Verdana,Arial,Geneva,Helvetica,sans-serif;font-size:10px;color:#666;line-height:1.4em;text-align:left;border:10px solid #fff;border-top-style:none;}
#lbCloseLink{display:block;float:right;width:66px;height:22px;background:transparent url(./plugins/galleryfiles/img/closelabel.gif) no-repeat center;margin:5px 0;outline:none;}
#lbCaption,#lbNumber{margin-right:71px;}
#lbCaption{font-weight:bold;}
</style>';} 
else {$content='';}

$delim = DIRECTORY_SEPARATOR;
if (array_key_exists('dir', $_REQUEST) && $subdirs) $dir = $_REQUEST['dir'];
else $dir = '';
if (!empty($_SERVER['PATH_TRANSLATED'])) $d = dirname($_SERVER['PATH_TRANSLATED']);
elseif (!empty($_SERVER['SCRIPT_FILENAME'])) $d = dirname($_SERVER['SCRIPT_FILENAME']);
else $d = getcwd();
$delim = (substr($d, 1, 1) == ':') ? '\\' : '/';
$rp = function_exists('realpath');
if ($rp) $root = realpath($d . $delim . $picdir);
else $root = $d . $delim . $picdir;
if ($rp) $realdir = realpath($root . $dir);
else $realdir = $root . $dir;
if (substr($realdir, 0, strlen($root)) != $root) { $realdir = $root; $dir = ''; }
$dirname = substr($realdir, strlen($root));
$dirnamehttp = $picdir . $dir;
if ($delim == '\\') $dirnamehttp = strtr($dirnamehttp, '\\', '/');
if (substr($dirnamehttp, 0, 2) == './') $dirnamehttp = substr($dirnamehttp, 2);
if (empty($dirnamehttp)) $dirnamehttp = '.';
if ($subdirs && !empty($dirname)) {$ti= $galleryname .' - '. substr( $dirname, 1);} else {$ti= $galleryname;}
if (($d = @opendir($realdir)) === false) $error = error('opendir', array($realdir));
if (isset($error)) $content="<p style=\"color: red\">$error</p>"; else {
if ($titlex == 'true') $content.='<h3>' . html($ti) .'</h3>';
$dirs = $pics = array();
while (($filename = readdir($d)) !== false) {
if ($filename == $thumbdir
|| ($filename == '..' && $dirname == '')
|| ($filename != '..' && substr($filename, 0, 1) == '.')) continue;
$file = $realdir . $delim . $filename;
if (is_dir($file)) $dirs[] = $filename;
elseif (strpos( $file,'.jpg')!='' || strpos( $file,'.jpeg')!='' ) $pics[] = $filename;
}
closedir($d);
sort($dirs);
sort($pics);
$urlsuffix = '';
foreach ($_GET as $v => $r) {
if (!in_array($v, array('dir', 'pic', 'offset'))) $urlsuffix .= "&$v=" . urlencode($r);
}
if (sizeof($dirs) > 0 && $subdirs) {
$content.='<p><ul>';
foreach ($dirs as $filename) {
if ($rp) $target = substr(realpath($realdir . $delim . $filename), strlen($root));
else $target = substr($realdir . $delim . $filename, strlen($root));
if ($delim == '\\') $target = strtr($target, '\\', '/');
if ($target == '') {
$url = ereg_replace('^([^?]+).*$', '\1', $_SERVER['REQUEST_URI']);
if (!empty($urlsuffix)) {
if (strstr($url, '?') === false) $url .= '?' . substr($urlsuffix, 1);
else $url .= $urlsuffix;
}
} else $url = '?dir=' . urlencode($target) . $urlsuffix;
if((strlen($filename))=="2") {
$content.='<li><a href="' . html($url) . '">' . $navrat . '</a></li>';} else {
$content .= '<li><a href="' . html($url) . '">' . html($filename) . '</a></li>';
}}
$content.='</ul><hr /></p>';
}
if (($num = sizeof($pics)) > 0) {
if (array_key_exists('offset', $_REQUEST)) $offset = $_REQUEST['offset'];
else $offset = 0;
if ($num > $maxpics) {
$content.='<p id=\"pagenumbers\">';
for ($i = 0; $i < $num; $i += $maxpics) {
$e = $i + $maxpics - 1;
if ($e > $num - 1) $e = $num - 1;
if ($i != $e) $b = ($i + 1) . '-' . ($e + 1);
else $b = $i + 1;
if ($i == $offset) $content.="<b>$b</b>";
else {
$url = ($dirname  == '') ? '?' : '?dir=' . urlencode($dirname) . '&amp;';
$content.="<a href=\"{$url}offset=$i" . html($urlsuffix) . "\">$b</a>";
}
if ($e != $num - 1) $content.=' | ';
$content.='';
}
$content.='</p><hr />';
}
$content.='<p>';
for ($i = $offset; $i < $offset + $maxpics; $i++) {
if ($i >= $num) break;
$filename = $pics[$i];
$file = $realdir . $delim . $filename;
if (!is_readable($file)) continue;
if (!is_dir($realdir . $delim . $thumbdir)) {
$u = umask(0);
if (!@mkdir($realdir . $delim . $thumbdir, 0777)) {
$content.='<span style="color: red; text-align: center">' . word('mkdir_error') . '</span>';
break;
}
umask($u);
}
$thumb = $realdir . $delim . $thumbdir . $delim . $filename . '.thumb.jpg';
if (!is_file($thumb)) {
if ((strpos( $file,'.jpg')!='' || strpos( $file,'.jpeg')!='' ))
$original = @imagecreatefromjpeg($file);
elseif (eregi($gif, $file))
$original = @imagecreatefromgif($file);
elseif (eregi($png, $file))
$original = @imagecreatefrompng($file);
else continue;
if ($original) {
if (function_exists('getimagesize'))
list($width, $height, $type, $attr) = getimagesize($file);
else continue;
if ($width >= $height && $width > $thumbsize) {
$smallwidth = $thumbsize;
$smallheight = floor($height / ($width / $smallwidth));
$ofx = 0; $ofy = floor(($thumbsize - $smallheight) / 2);
} elseif ($width <= $height && $height > $thumbsize) {
$smallheight = $thumbsize;
$smallwidth = floor($width / ($height / $smallheight));
$ofx = floor(($thumbsize - $smallwidth) / 2); $ofy = 0;
} else {
$smallheight = $height;
$smallwidth = $width;
$ofx = floor(($thumbsize - $smallwidth) / 2);
$ofy = floor(($thumbsize - $smallheight) / 2);
}
}
if (function_exists('imagecreatetruecolor'))
$small = imagecreatetruecolor($thumbsize, $thumbsize);
else $small = imagecreate($thumbsize, $thumbsize);
sscanf($bg, "%2x%2x%2x", $red, $green, $blue);
$b = imagecolorallocate($small, $red, $green, $blue);
imagefill($small, 0, 0, $b);
if ($original) {
if (function_exists('imagecopyresampled'))
imagecopyresampled($small, $original, $ofx, $ofy, 0, 0, $smallwidth, $smallheight, $width, $height);
else
imagecopyresized($small, $original, $ofx, $ofy, 0, 0, $smallwidth, $smallheight, $width, $height);
} else {
$black = imagecolorallocate($small, 0, 0, 0);
$fw = imagefontwidth($fontsize);
$fh = imagefontheight($fontsize);
$htw = ($fw * strlen($filename)) / 2;
$hts = $thumbsize / 2;
imagestring($small, $fontsize, $hts - $htw, $hts - ($fh / 2), $filename, $black);
imagerectangle($small, $hts - $htw - $fw - 1, $hts - $fh, $hts + $htw + $fw - 1, $hts + $fh, $black);
}
imagejpeg($small, $thumb);
}

$content.='<a href="' . html("$dirnamehttp/$filename");
$content.='" rel="lightbox-'.$ti.'"><img class="galeria" src="' . html("$dirnamehttp/$thumbdir/$filename.thumb.jpg");
$content.='" alt="' . html($filename) . '" style="';
$content.='width:'.$thumbsize.'px; height: '.$thumbsize.'px />"';
$content.='</a>';
}
$content.='</p>';
}
}
echo $content;
}
?>
