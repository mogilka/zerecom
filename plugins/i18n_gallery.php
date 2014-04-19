<?php
/*
Plugin Name: I18N Gallery
Description: Display image galleries (I18N enabled)
Version: 1.5
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

define('I18N_GALLERY_DIR', 'i18n_gallery/'); 
define('I18N_GALLERY_DEFAULT_TYPE', 'prettyphoto');
define('I18N_GALLERY_DEFAULT_THUMB_WIDTH', 160);
define('I18N_GALLERY_DEFAULT_THUMB_HEIGHT', 120);

# register plugin
register_plugin(
	$thisfile, 
	'I18N Gallery', 	
	'1.5', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Display image galleries (I18N enabled)',
	'i18n_gallery',
	'i18n_gallery_configure'  
);

# load i18n texts
if (basename($_SERVER['PHP_SELF']) != 'index.php') { // back end only
  i18n_merge('i18n_gallery', substr($LANG,0,2));
  i18n_merge('i18n_gallery', 'en');
}

$i18n_gallery_plugins = null;
$i18n_gallery_on_page = null;
$i18n_gallery_settings = null;
$i18n_gallery_pic_used = false;

# activate filter
add_action('header','i18n_gallery_header');
add_action('nav-tab','i18n_gallery_tab');
add_action('i18n_gallery-sidebar', 'i18n_gallery_sidebar');
add_action('theme-header','i18n_gallery_theme_header');
add_filter('content','i18n_gallery_replace');
add_filter('search-index-page', 'i18n_gallery_index');

//install loadtab.php into /admin if necessary
if(!file_exists(GSADMINPATH.'loadtab.php')) {
	if (copy(GSPLUGINPATH.'i18n_gallery/loadtab.php', GSADMINPATH.'loadtab.php')) { }
}

if (!i18n_gallery_is_frontend() && i18n_gallery_gsversion() == '3.0') {
  // workaround for GetSimple 3.0:
  if (isset($_COOKIE['GS_ADMIN_USERNAME'])) setcookie('GS_ADMIN_USERNAME', $_COOKIE['GS_ADMIN_USERNAME'], 0, '/');
}

function i18n_gallery_is_frontend() {
  return function_exists('get_site_url');
}

function i18n_gallery_gsversion() {
  @include(GSADMININCPATH.'configuration.php');
  return GSVERSION;
}


function i18n_gallery_header() {
?>
<style type="text/css">
  #loadtab .wrapper .nav li a.i18n_gallery_selected {
    -moz-box-shadow: 2px -2px 2px rgba(0, 0, 0, 0.1);
    background: -moz-linear-gradient(center top , #FFFFFF 3%, #F6F6F6 100%) repeat scroll 0 0 transparent;
    color: #182227;
    font-weight: bold !important;
    text-shadow: 1px 1px 0 #FFFFFF;
  }
  span.geo {
    display: none;
    width: 20px;
    height: 35px;
    background: url(../plugins/i18n_gallery/images/gray-dot.png) center no-repeat;
  }
  span.geo.geo-yes {
   background-image: url(../plugins/i18n_gallery/images/red-dot.png);
  }
  span.geo.geo-current {
    background-image: url(../plugins/i18n_gallery/images/blue-dot.png);
  }
  .geo span.geo {
    display: block;
  }
</style>
<?php
}

function i18n_gallery_tab() {
	echo '<li><a href="loadtab.php?id=i18n_gallery&amp;item=i18n_gallery_overview" '.(@$_GET['id'] == 'i18n_gallery' ? 'class="i18n_gallery_selected"' : '').'>';
	echo i18n_r('i18n_gallery/TAB');
	echo '</a></li>';
}

function i18n_gallery_sidebar() {
  $f = 'i18n_gallery_overview';
	echo '<li><a href="loadtab.php?id=i18n_gallery&amp;item='.$f.'" '.(@$_GET['item'] == $f ? 'class="current"' : '').' >'.i18n_r('i18n_gallery/GALLERIES').'</a></li>';
  $f = 'i18n_gallery_create';
	echo '<li><a href="loadtab.php?id=i18n_gallery&amp;item='.$f.'" '.(@$_GET['item'] == $f ? 'class="current"' : '').' >'.i18n_r('i18n_gallery/CREATE_GALLERY').'</a></li>';
  $f = 'i18n_gallery_edit';
  if (@$_GET['item'] == $f) {
	  echo '<li><a href="loadtab.php?id=i18n_gallery&amp;item='.$f.'" class="current">'.i18n_r('i18n_gallery/EDIT_GALLERY').'</a></li>';
  }
  $f = 'i18n_gallery_configure';
	echo '<li><a href="loadtab.php?id=i18n_gallery&amp;item='.$f.'" '.(@$_GET['item'] == $f ? 'class="current"' : '').' >'.i18n_r('i18n_gallery/SETTINGS').'</a></li>';
}

function i18n_gallery_check_prerequisites() {
  $success = true;
  $gdir = GSDATAPATH . I18N_GALLERY_DIR;
  if (!file_exists($gdir)) {
    $success = mkdir(substr($gdir,0,strlen($gdir)-1), 0777) && $success;
    $fp = fopen($gdir . '.htaccess', 'w');
    fputs($fp, 'Deny from all');
    fclose($fp);
  }
  $gdir = GSBACKUPSPATH . I18N_GALLERY_DIR;
  // create directory if necessary
  if (!file_exists($gdir)) {
    $success = @mkdir(substr($gdir,0,strlen($gdir)-1), 0777) && $success;
    $fp = @fopen($gdir . '.htaccess', 'w');
    if ($fp) {
      fputs($fp, 'Deny from all');
      fclose($fp);
    }
  }
  return $success;
}

function i18n_gallery_register($type, $name, $description, $edit_function, $header_function, $content_function) {
  global $i18n_gallery_plugins;
  $i18n_gallery_plugins[$type] = array(
    'type' => $type,
    'name' => $name,
    'description' => $description,
    'edit' => $edit_function,
    'header' => $header_function,
    'content' => $content_function
  );
}

function i18n_gallery_plugins() {
  global $i18n_gallery_plugins;
  if ($i18n_gallery_plugins == null) {
    $i18n_gallery_plugins = array();
	  $dir_handle = @opendir(GSPLUGINPATH.'i18n_gallery');
    while ($filename = readdir($dir_handle)) {
      if (substr($filename,0,7) == 'plugin_' && strrpos($filename,'.php') === strlen($filename)-4) {
        include_once(GSPLUGINPATH.'i18n_gallery/'.$filename);
      }
    }
  }
  return $i18n_gallery_plugins;
}

function i18n_gallery_settings($reload=false) {
  global $i18n_gallery_settings;
  if ($i18n_gallery_settings != null && !$reload) return $i18n_gallery_settings;
  $i18n_gallery_settings = array();
  if (file_exists(GSDATAOTHERPATH.'i18n_gallery_settings.xml')) {
    $data = getXML(GSDATAOTHERPATH.'i18n_gallery_settings.xml');
    if ($data) {
      foreach ($data as $key => $value) $i18n_gallery_settings[$key] = (string) $value;
    }
  }
  return $i18n_gallery_settings;
}

function return_i18n_gallery($name) {
  $gallery = array('items' => array());
  if (!file_exists(GSDATAPATH.'i18n_gallery/'.$name.'.xml')) return $gallery;
  $data = getXML(GSDATAPATH . I18N_GALLERY_DIR . $name . '.xml');
  if (!$data) return $gallery;
  foreach ($data as $key => $value) {
    if ($key != 'item' && $key != 'items') {
      $gallery[$key] = (string) $value;
    } else {
      $item = array();
      foreach ($value as $itemkey => $itemvalue) {
        $item[$itemkey] = (string) $itemvalue;
      }
      $gallery['items'][] = $item;
    }
  }
  return $gallery;
}

function i18n_gallery_overview() {
  include(GSPLUGINPATH.'i18n_gallery/overview.php');
}

function i18n_gallery_create() {
  include(GSPLUGINPATH.'i18n_gallery/edit.php');
}

function i18n_gallery_edit() {
  include(GSPLUGINPATH.'i18n_gallery/edit.php');
}

function i18n_gallery_configure() {
  include(GSPLUGINPATH.'i18n_gallery/configure.php');
}

function i18n_gallery_get_from_params($params, $ignoreQuery=false, $ignoreSettings=false, $lang=null) {
  if (!$ignoreQuery) {
    if (!@$params['name'] && @$_GET['name']) $params['name'] = $_GET['name'];
    if (!@$params['type'] && @$_GET['type']) $params['type'] = $_GET['type'];
    if (!@$params['tags'] && @$_GET['tags']) $params['tags'] = $_GET['tags'];
  }
  if (!@$params['name']) return null;
  $gallery = return_i18n_gallery($params['name']);
  if (!$gallery || !@$gallery['type']) return null;
  foreach ($params as $key => $value) $gallery[$key] = $value;
  if (@$params['tags']) {
    // filter images
    $tags = preg_split('/\s*,\s*/', trim($params['tags']));
    $newitems = array();
    foreach ($gallery['items'] as $item) {
      if (!@$item['tags']) continue;
      $itemtags = preg_split('/\s*,\s*/', trim($item['tags']));
      if (count(array_intersect($tags, $itemtags)) == count($tags)) $newitems[] = $item;
    }
    $gallery['items'] = $newitems;
  }
  if (!$ignoreSettings) {
    // add settings
    $settings = i18n_gallery_settings();
    if (!@$gallery['thumbwidth'] && !@$gallery['thumbheight']) {
      if (intval(@$settings['thumbwidth']) > 0 || intval(@$settings['thumbheight']) > 0) {
        $gallery['thumbwidth'] = intval(@$settings['thumbwidth']) > 0 ? intval($settings['thumbwidth']) : null;
        $gallery['thumbheight'] = intval(@$settings['thumbheight']) > 0 ? intval($settings['thumbheight']) : null;
        $gallery['thumbcrop'] = @$settings['thumbcrop'];
      } else {
        $gallery['thumbwidth'] = I18N_GALLERY_DEFAULT_THUMB_WIDTH;
        $gallery['thumbheight'] = I18N_GALLERY_DEFAULT_THUMB_HEIGHT;
        $gallery['thumbcrop'] = 0;
      }
    }
    if (count($settings) > 0) {
      if (!isset($gallery['jquery']) && isset($settings['jquery'])) $gallery['jquery'] = $settings['jquery'];
      if (!isset($gallery['css']) && isset($settings['css'])) $gallery['css'] = $settings['css'];
      if (!isset($gallery['js']) && isset($settings['js'])) $gallery['js'] = $settings['js'];
      if (!@$gallery['width'] && !@$gallery['height']) {
        if (intval(@$settings['width']) > 0) $gallery['width'] = intval($settings['width']);
        if (intval(@$settings['height']) > 0) $gallery['height'] = intval($settings['height']);
      }
    }
  }
  // get best language texts
  if (function_exists('return_i18n_languages')) {
    global $language;
    //$languages = return_i18n_languages();
    if (!$lang) $lang = $language;
    $deflang = return_i18n_default_language();
    $languages = @$lang && $lang != $deflang ? array($lang, $deflang) : array($deflang);
    foreach ($languages as $lang) {
      $fullkey = 'title' . ($lang == $deflang ? '' : '_' . $lang);
      if (isset($gallery[$fullkey])) { $gallery['_title'] = $gallery[$fullkey]; break; }
    }
    foreach ($gallery['items'] as &$item) {
      foreach ($languages as $lang) {
        $fullkey = 'title' . ($lang == $deflang ? '' : '_' . $lang);
        if (isset($item[$fullkey])) { $item['_title'] = $item[$fullkey]; break; }
      }
      foreach ($languages as $lang) {
        $fullkey = 'description' . ($lang == $deflang ? '' : '_' . $lang);
        if (isset($item[$fullkey])) { $item['_description'] = $item[$fullkey]; break; }
      }
    }
  } else {
    $gallery['_title'] = $gallery['title'];
    foreach ($gallery['items'] as &$item) {
      $item['_title'] = $item['title'];
      $item['_description'] = $item['description'];
    }
  }
  return $gallery;
}

function i18n_gallery_get_from_paramstr($paramstr, $ignoreQuery=false, $ignoreSettings=false, $lang=null) {
  $params = array();
  $paramstr = @$paramstr ? html_entity_decode(trim($paramstr), ENT_QUOTES, 'UTF-8') : '';
  while (preg_match('/^([a-zA-Z][a-zA-Z_-]*)[:=]([^"\'\s]*|"[^"]*"|\'[^\']*\')(?:\s|$)/', $paramstr, $pmatch)) {
    $key = $pmatch[1];
    $value = trim($pmatch[2]);
    if (substr($value,0,1) == '"' || substr($value,0,1) == "'") $value = substr($value,1,strlen($value)-2);
    $params[$key] = $value;
    $paramstr = substr($paramstr, strlen($pmatch[0]));
  }
  return i18n_gallery_get_from_params($params, $ignoreQuery, $ignoreSettings, $lang);
}

function i18n_gallery_theme_header() {
  global $i18n_gallery_on_page, $content;
  include_once(GSPLUGINPATH.'i18n_gallery/helper.php');
	$c = strip_decode($content);
  if (preg_match_all("/(<p>\s*)?\(%\s*(gallery)(\s+(?:%[^%\)]|[^%])+)?\s*%\)(\s*<\/p>)?/", $c, $matches)) {
    $plugins = i18n_gallery_plugins();
    foreach ($matches[3] as $paramstr) {
      $gallery = i18n_gallery_get_from_paramstr($paramstr);
      $plugin = @$plugins[$gallery['type']];
      if ($plugin) call_user_func_array($plugin['header'], array($gallery));
    }
    $i18n_gallery_on_page = true;
  }
}

function i18n_gallery_replace($content) {
  global $i18n_gallery_on_page;
  $content = preg_replace_callback("/\(%\s*(gallerylink)(\s+(?:%[^%\)]|[^%])+)?\s*%\)/", 'i18n_gallery_replace_match_link',$content);
  if (!$i18n_gallery_on_page) return $content;
  return preg_replace_callback("/(<p>\s*)?\(%\s*(gallery)(\s+(?:%[^%\)]|[^%])+)?\s*%\)(\s*<\/p>)?/", 'i18n_gallery_replace_match',$content);
}

function i18n_gallery_replace_match($match) {
  $replacement = '';
  if (@$match[1] && (!isset($match[4]) || !$match[4])) $replacement .= $match[1];
  $gallery = i18n_gallery_get_from_paramstr(@$match[3]);
  ob_start();
  if ($match[2] == 'gallery' && $gallery) {
    i18n_gallery_display($gallery);
  }
  $replacement .= ob_get_contents();
  ob_end_clean();
  if (!@$match[1] && isset($match[4]) && $match[4]) $replacement .= $match[4];
  return $replacement;
}

function i18n_gallery_replace_match_link($match) {
  $replacement = '';
  $gallery = i18n_gallery_get_from_paramstr(@$match[2]);
  ob_start();
  if ($match[1] == 'gallerylink' && $gallery) {
    i18n_gallery_link($gallery);
  }
  $replacement .= ob_get_contents();
  ob_end_clean();
  return $replacement;
}

function i18n_gallery_display($gallery, $ignoreQuery=false) {
  global $LANG, $i18n_gallery_pic_used;
  include_once(GSPLUGINPATH.'i18n_gallery/helper.php');
  if (function_exists('i18n_load_texts')) {
    i18n_load_texts('i18n_gallery');
  } else {  
    i18n_merge('i18n_gallery', substr($LANG,0,2)) || i18n_merge('i18n_gallery', 'en');
  }  
  $pic = @$gallery['pic'];
  if (!$ignoreQuery && isset($_GET['pic']) && !$i18n_gallery_pic_used) {
    if (strpos($_GET['pic'],':') === false) {
      $pic = intval($_GET['pic']);
      $i18n_gallery_pic_used = true;
    } else if (substr($_GET['pic'],0,strrpos($_GET['pic'],':')) == $gallery['name']) {
      $pic = intval(substr($_GET['pic'],strrpos($_GET['pic'],':')+1));
      $i18n_gallery_pic_used = true;
    }
  }
  $plugins = i18n_gallery_plugins();
  $plugin = @$plugins[$gallery['type']];
  if ($plugin) call_user_func_array($plugin['content'], array($gallery, $pic));
}

function i18n_gallery_link($gallery) {
  include_once(GSPLUGINPATH.'i18n_gallery/helper.php');
  $url = @$gallery['url'] ? $gallery['url'] : 'index';
  $parent = @$gallery['parent'] ? $gallery['parent'] : null;
  $thumb = i18n_gallery_thumb($gallery);
  $title = $gallery['title'];
  if (function_exists('return_i18n_languages')) {
    $languages = return_i18n_languages();
    $deflang = return_i18n_default_language();
    foreach ($languages as $language) {
      $fullkey = 'title' . ($language == $deflang ? '' : '_' . $language);
      if (isset($gallery[$fullkey])) { $title = $gallery[$fullkey]; break; }
    }
  }
  echo '<a href="'.find_url($url,$parent).'">';
  if (isset($thumb)) {
    $item = @$gallery['items'][$thumb];
    if (!$item) $item = $gallery['items'][0];
    echo '<img src="';
    i18n_gallery_thumb_link($gallery,$item);
    echo '" alt="'.htmlspecialchars($title).'" title="'.htmlspecialchars($title).'"/>';
  } else {
    echo htmlspecialchars($title);
  }
  echo '</a>';  
}

function get_i18n_gallery_link($name, $params = null) {
  if (is_array($name)) $params = $name; 
  else if (!@$params) $params = array('name' => $name);
  else $params['name'] = $name;
  $gallery = i18n_gallery_get_from_params($params, true);
  i18n_gallery_link($gallery);
}

function get_i18n_gallery_header($name, $params = null) {
  include_once(GSPLUGINPATH.'i18n_gallery/helper.php');
  if (is_array($name)) $params = $name; 
  else if (!@$params) $params = array('name' => $name);
  else $params['name'] = $name;
  $gallery = i18n_gallery_get_from_params($params, true);
  $plugins = i18n_gallery_plugins();
  $plugin = @$plugins[$gallery['type']];
  if ($plugin) call_user_func_array($plugin['header'], array($gallery));
}

function get_i18n_gallery($name, $params = null) {
  if (is_array($name)) $params = $name; 
  else if (!@$params) $params = array('name' => $name);
  else $params['name'] = $name;
  $gallery = i18n_gallery_get_from_params($params);
  if ($gallery) i18n_gallery_display($gallery);
}

// indexing content for I18N Search plugin. $item is of type I18nSearchPageItem.
function i18n_gallery_index($item) {
  $content = stripslashes(htmlspecialchars($item->data->content));
  if (preg_match_all("/\(%\s*(gallery)(\s+(?:%[^%\)]|[^%])+)?\s*%\)/", $content, $matches)) {
    $i = 0;
    foreach ($matches[2] as $params) {
      $gallery = i18n_gallery_get_from_paramstr($params,true,true,$item->language);
      if ($gallery) {
        $text = '';
        foreach ($gallery['items'] as &$galitem) {
          $text .= $galitem['_title'] . ' ' . $galitem['_description'] . ' ';
        }
        $item->addContent('i18n_gallery_'.$i, html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
        $i++;
      }
    }
  }
}
