<?php
global $i18n_gallery_includes;
$i18n_gallery_includes = array();

function i18n_gallery_id($gallery) {
  if (@$gallery['id']) return $gallery['id'];
  $id = $gallery['name'];
  if (@$gallery['tags']) {
    $id .= '-'.preg_replace('/[^a-z0-9-]+/','/-/',strtolower($gallery['tags']));
  }
  return $id;
}

function i18n_gallery_check($gallery, $what, $default=true) {
  if (!isset($gallery[$what])) return $default;
  return $gallery[$what] != 0 && $gallery[$what] !== false && strtolower($gallery[$what]) != 'false';
}

function i18n_gallery_needs_include($include) {
  global $i18n_gallery_includes;
  if (@$i18n_gallery_includes[$include]) return false;
  $i18n_gallery_includes[$include] = true;
  return true;
}

function i18n_gallery_thumb($gallery) {
  if (isset($gallery['thumb']) && $gallery['thumb'] != '') {
    $thumb = $gallery['thumb'];
    if (strtolower($thumb) == 'random') return rand(0, count($gallery['items'])-1);
    return intval($thumb) >= 0 && intval($thumb) < count($gallery['items']) ? intval($thumb) : 0;
  }
}

function i18n_gallery_is_goto_image($pic) {
  return $pic != null && intval($pic) >= 0;
}

function i18n_gallery_is_show_image($pic) {
  return $pic != null && intval($pic) < 0;
}

function i18n_gallery_item($gallery, $pic) {
  if ($pic == null) return null;
  if (intval($pic) >= 0) $item = $gallery['items'][intval($pic)]; else $item = $gallery['items'][-intval($pic)-1];
  return $item ? $item : $gallery['items'][0];
}

function i18n_gallery_site_link() {
  global $SITEURL;
  return (string) $SITEURL;
}

function i18n_gallery_pic_link($gallery, $pic, $echo=true) {
  if ($pic >= 0) $pic = -$pic-1;
  $link = get_page_url(true);
  $link .= strpos($link,'?') === false ? '?' : '&';
  foreach ($_GET as $key => $value) if ($key != 'pic' && $key != 'id') $link .= $key.'='.urlencode($value).'&'; 
  $link .= 'pic='.urlencode($gallery['name']).':'.$pic; 
  if ($echo) echo str_replace('&','&amp;',$link); else return $link;
}

function i18n_gallery_prev_link($gallery, $pic, $echo=true) {
  return i18n_gallery_pic_link($gallery, $pic+1 >= 0 ? -count($gallery['items']) : $pic+1, $echo);
}

function i18n_gallery_next_link($gallery, $pic, $echo=true) {
  return i18n_gallery_pic_link($gallery, $pic-1 < -count($gallery['items']) ? -1 : $pic-1, $echo);
}

function i18n_gallery_back_link($echo=true) {
  $link = get_page_url(true);
  $link .= strpos($link,'?') === false ? '?' : '&';
  foreach ($_GET as $key => $value) if ($key != 'pic' && $key != 'id') $link .= $key.'='.urlencode($value).'&'; 
  if ($echo) echo str_replace('&','&amp;',$link); else return $link;
}

function i18n_gallery_image_link($gallery, $item=null, $echo=true) {
  if (!is_array($item)) $item = @$gallery['items'][-intval($item)-1];
  if (!$item) $item = @$gallery['items'][0];
  $w = @$gallery['width'];
  $h = @$gallery['height'];
  $c = @$gallery['crop'] && $w && $h;
  if ((!$c || $w*$item['height'] == $item['width']*$h) && (!$w || $item['width'] <= $w) && (!$h || $item['height'] <= $h)) {
    $link = i18n_gallery_site_link().'data/uploads/'.$item['filename'];
  } else {
    $link = i18n_gallery_site_link().'plugins/i18n_gallery/browser/pic.php?p='.urlencode($item['filename']).'&w='.$w.'&h='.$h.($c?'&c=1':'');
  }
  if ($echo) echo str_replace('&','&amp;',$link); else return $link;
}

function i18n_gallery_thumb_link($gallery, $item=null, $echo=true) {
  if (!is_array($item)) $item = @$gallery['items'][-intval($item)-1];
  if (!$item) $item = @$gallery['items'][0];
  $tw = @$gallery['thumbwidth'];
  $th = @$gallery['thumbheight'];
  $tc = @$gallery['thumbcrop'] && $tw && $th;
  $link = i18n_gallery_site_link().'plugins/i18n_gallery/browser/pic.php?p='.urlencode($item['filename']).'&w='.$tw.'&h='.$th.($tc?'&c=1':'');
  if ($echo) echo str_replace('&','&amp;',$link); else return $link;
}

function i18n_gallery_replace_nojs_links($gallery, $selvar) {
  for ($i=0; $i<count($gallery['items']); $i++) {
?>
  <?php echo $selvar; ?>.get(<?php echo $i; ?>).href = '<?php echo i18n_gallery_image_link($gallery,$gallery['items'][$i],false); ?>';
<?php 
  } 
}

function i18n_gallery_PREV() {
  i18n('i18n_gallery/PREV');
}

function i18n_gallery_NEXT() {
  i18n('i18n_gallery/NEXT');
}

function i18n_gallery_BACK() {
  i18n('i18n_gallery/BACK');
}
