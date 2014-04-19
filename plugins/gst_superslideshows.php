<?php
	
/*
Plugin Name: GST SuperSlideshows
Description: enables the usage of Cycle slideshows within GetSimple themes
Version: 1.0
Author: Mattijs Naus / GetSimpleThemes.com
Author URI: http://www.getsimplethemes.com/
*/	

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

$data_path = GSDATAPATH."gst-superslideshows/";

# register plugin
register_plugin(
	$thisfile, 
	'GST SuperSlideshows', 	
	'1.0', 		
	'Mattijs Naus / GetSimpleThemes',
	'http://www.getsimplethemes.com/', 
	'Enables the usage of Cycle slideshows within GetSimple themes',
	'theme',
	'superslideshows'  
);

add_action('theme-sidebar','createSideMenu',array($thisfile,'GST SuperSlideshows'));//puts an extra item in the sidebar
add_action('theme-header', 'getCycle');

function superslideshows() {
	
	global $data_path;
	
	if(!file_exists($data_path)) {
		
		//create direcoty to store slideshows
		mkdir($data_path);	
		
	}
	
	//get all pages
	$data = gst_menu_data();
	
	
	//do we have GET data indicating we need remove a slideshow?
	if(isset($_GET['slideshow']) && $_GET['slideshow'] != '' && file_exists($data_path.$_GET['slideshow'].".xml")) {
		
		if(file_exists($data_path.$_GET['slideshow'].".xml")) { unlink ($data_path.$_GET['slideshow'].".xml"); }
			
			show_message('Your slideshow has been removed.');
		
	}
	
	//do we have POST data for creating a new slideshow?
	if(isset($_POST['superslideshow_name']) && $_POST['superslideshow_name'] != '' && isset($_POST['slideshow_pages']) && !empty($_POST['slideshow_pages'])) {
		
		$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		
		$note = $xml->addChild('slideshow_pages');
		$note->addCData(implode('|', $_POST['slideshow_pages']));
		
		$settings = $xml->addChild('settings');
		
		$settings->addChild('effect', $_POST['settings_effect']);
		$settings->addChild('easing', $_POST['settings_easing']);
		$settings->addChild('pager', $_POST['settings_pager']);
		$settings->addChild('next', $_POST['settings_next']);
		$settings->addChild('previous', $_POST['settings_previous']);
		$settings->addChild('speed', $_POST['settings_speed']);
		$settings->addChild('timeout', $_POST['settings_timeout']);
		
		$filename = str_replace(" ", "_", strtolower($_POST['superslideshow_name']));
		
		fopen($data_path."/".$filename.".xml", "a+");
	
		$xml->asXML($data_path."/".$filename.".xml");
		
		show_message('Your slideshow was created successfully!');
		
	}
	
	
	//do we have POST data for updated an existing slideshow?
	if(isset($_POST['update_slideshow']) && $_POST['update_slideshow'] != '') {
		
		//if all images are unchecked, we'll delete the slideshow all together
		if(empty($_POST['slideshow_pages'])) {
					
			if(file_exists($data_path.$_POST['update_slideshow'].".xml")) { unlink ($data_path.$_POST['update_slideshow'].".xml"); }
			
			show_message('Your slideshow has been removed.');
			
		} else {
			
			//update existing slideshow, start with removing the old version
			
			if(file_exists($data_path.$_POST['update_slideshow'].".xml")) { unlink ($data_path.$_POST['update_slideshow'].".xml"); }
			
			$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		
			$note = $xml->addChild('slideshow_pages');
			$note->addCData(implode('|', $_POST['slideshow_pages']));
			
			$settings = $xml->addChild('settings');
		
			$settings->addChild('effect', $_POST['settings_effect']);
			$settings->addChild('easing', $_POST['settings_easing']);
			$settings->addChild('pager', $_POST['settings_pager']);
			$settings->addChild('next', $_POST['settings_next']);
			$settings->addChild('previous', $_POST['settings_previous']);
			$settings->addChild('speed', $_POST['settings_speed']);
			$settings->addChild('timeout', $_POST['settings_timeout']);
		
			$filename = str_replace(" ", "_", strtolower($_POST['update_slideshow']));
		
			fopen($data_path."/".$filename.".xml", "a+");
	
			$xml->asXML($data_path."/".$filename.".xml");
		
			show_message('Your slideshow was updated successfully!');
			
		}
		
	}
	
	
	
	?>
	
	<style>
	
	.show-others {
		color:#0bc5fa !important;
	}
	
	h2.thehead {
		color:#CF3805;
		font-size:20px;
		font-weight:bold;
	}
	
	h4 {
		font-weight:bold;
		margin-bottom:20px;
	}
	
	input.text {
		margin-bottom:20px;
	}
	
	p input.text {
		margin-bottom:0px;
	}
	
	</style>
	
	<script type="text/javascript">
		
	$(document).ready(function(){
		
		$('a.show-others').toggle(function(){
			
			$(this).parent().next().fadeIn();
			$(this).text('hide');
			
		}, function(){
			
			$(this).parent().next().fadeOut();
			$(this).text('show');
		
		}) 	
		
	})
		
	</script>
	
	<h2 class="thehead">GetSimpleThemes SuperSlideshows Plugin</h2>
	
	<h3>Create a new slideshow...</h3>
	
	<form action="<?php echo $_SERVER ['REQUEST_URI']?>" method="post" style="margin-top:20px; margin-bottom:20px;">
	
		<input type="text" class="text" name="superslideshow_name" value="slideshow" onfocus="if(this.value == 'slideshow'){this.value = ''}" />
		
		<h4>Select the pages to appear in the slideshow</h4>
		
		<table>
		
			<?php foreach($data as $page):?>
			
			<tr>
				<td><?php echo stripslashes($page['slug']);?></td>
				<td style="text-align:right">
					<input type="checkbox" name="slideshow_pages[]" value="<?php echo $page['slug'];?>" />
				</td>
			</tr>
			
			<?php endforeach?>
		
		</table>
		
		<h4>Other settings for your slideshow:</h4>
		
		<p>
		Slide effect:<br />
		<select name="settings_effect">
			<option value="blindX">blindX</option>
			<option value="blindY">blindY</option>
			<option value="blindZ">blindZ</option>
			<option value="cover">cover</option>
			<option value="curtainX">curtainX</option>
			<option value="curtainY">curtainY</option>
			<option value="fade">fade</option>
			<option value="fadeZoom">fadeZoom</option>
			<option value="growX">growX</option>
			<option value="growY">growY</option>
			<option value="none">none</option>
			<option value="scrollUp">scrollUp</option>
			<option value="scrollDown">scrollDown</option>
			<option value="scrollLeft">scrollLeft</option>
			<option value="scrollRight">scrollRight</option>
			<option value="scrollHorz">scrollHorz</option>
			<option value="scrollVert">scrollVert</option>
			<option value="shuffle">shuffle</option>
			<option value="slideX">slideX</option>
			<option value="slideY">slideY</option>
			<option value="toss">toss</option>
			<option value="turnUp">turnUp</option>
			<option value="turnDown">turnDown</option>
			<option value="turnLeft">turnLeft</option>
			<option value="turnRight">turnRight</option>
			<option value="uncover">uncover</option>
			<option value="wipe">wipe</option>
			<option value="zoom">zoom</option>
		</select>
		</p>
		
		<p>
			Easing:<br />
			<select name="settings_easing">
				<option value="easeIn">easeIn</option>
				<option value="easeOut">easeOut</option>
				<option value="easeInOut">easeInOut</option>
				<option value="expoin">expoin</option>
				<option value="expoout">expoout</option>
				<option value="expoinout">expoinout</option>
				<option value="bouncein">bouncein</option>
				<option value="bounceout">bounceout</option>
				<option value="bounceinout">bounceinout</option>
				<option value="elasin">elasin</option>
				<option value="elasout">elasout</option>
				<option value="elasinout">elasinout</option>
				<option value="backin">backin</option>
				<option value="backout">backout</option>
				<option value="backinout">backinout</option>
			</select>
		</p>
		
		<p>
		pager:<br /><input type="text" class="text" name="settings_pager" value="" style="width:200px;" />
		</p>
		
		<p>
		next link:<br /><input type="text" class="text" name="settings_next" value="" style="width:200px;" />
		</p>
		
		<p>
		previous link:<br /><input type="text" class="text" name="settings_previous" value="" style="width:200px;" />
		</p>
		
		<p>
		speed (in milli seconds):<br /> <input type="text" class="text" name="settings_speed" value="1000" style="width:200px;" />
		</p>
		
		<p>
		timeout (in milli seconds):<br /> <input type="text" class="text" name="settings_timeout" value="7000" style="width:200px;" />
		</p>
		
		<input type="submit" value="Create slideshow" />
	
	</form>
	
	<label>Existing slideshows</label><br /><br /><br />
	
	<?php
			
		$slideshows = getFiles($data_path);
			
		$count = 0;
		$size = 0;
		$fileArray = array();
			
		if (count($slideshows) != 0) {
			
			foreach ($slideshows as $file) {
				if ($file == "." || $file == ".." || is_dir(GSDATAUPLOADPATH . $file) || $file == ".htaccess") {
					// not a upload file
				} else {
					$ext = substr($file, strrpos($file, '.') + 1);
					$extention = get_FileType($ext);
					if (strtolower($ext) == 'xml') {
						
						$temp = explode(".", $file);
						
						$fileArray[$count]['name'] = $temp[0];
						$fileArray[$count]['file'] = $file;
						$count++;
					}
				}
			}

			$slideshows = subval_sort($fileArray,'name');
		}
						
	?>
	
	<?php if(count($slideshows) > 0):?>
		<?php foreach($slideshows as $slideshow):?>
			
			<form method="post" action="<?php echo $_SERVER ['REQUEST_URI']?>" style="margin-bottom:40px;" class="existing_slideshow">
			
			<input type="hidden" name="update_slideshow" value="<?php echo $slideshow['name'];?>" />
			
			slideshow: <b style="font-size:15px;"><?php echo $slideshow['name'];?></b>
			
			<code style="float:right; font-size:11px; color:#666666; font-family: Consolas,Monaco,'Courier New',Courier,monospace">&lt;?php gst_superslideshow('<?php echo $slideshow['name'];?>'); ?&gt;</code>
			
			<?php $pages = get_slideshow_pages($slideshow['file']);?>
			
			<table style="margin-top:10px;">
				<?php foreach($pages as $page):?>
				<tr>
					<td>
						<?php echo $page;?>
					</td>
					<td style="text-align:right">
						<input type="checkbox" name="slideshow_pages[]" value="<?php echo $page;?>" checked="checked" />
					</td>
				</tr>
				<?php endforeach;?>
			</table>
			
			<span>Other available pages &nbsp;&nbsp;<a href="" class="show-others">show</a></span>
			
			<table style="margin-top:10px; display:none">
				<?php foreach($data as $page):?>
				<?php if(!in_array($page['slug'], $pages)):?>
				<tr>
					<td><?php echo stripslashes($page['slug']);?></td>
					<td style="text-align:right">
						<input type="checkbox" name="slideshow_pages[]" value="<?php echo $page['slug'];?>" />
					</td>
				</tr>
				<?php endif;?>
				<?php endforeach?>
			</table>
			
			<br /><br />
			
			<span>Slideshow settings  &nbsp;&nbsp;<a href="" class="show-others">show</a></span>
			
			<?php $settings = get_slideshow_settings($slideshow['file']);?>
			
			<div style="margin-top:20px; display:none">
			<p>
				Slide effect:<br />
				<select name="settings_effect">
					<option value="blindX" <?php if($settings->effect == 'blindX'):?>selected="selected"<?php endif;?> >blindX</option>
					<option value="blindY" <?php if($settings->effect == 'blindY'):?>selected="selected"<?php endif;?> >blindY</option>
					<option value="blindZ" <?php if($settings->effect == 'blindZ'):?>selected="selected"<?php endif;?> >blindZ</option>
					<option value="cover" <?php if($settings->effect == 'cover'):?>selected="selected"<?php endif;?> >cover</option>
					<option value="curtainX" <?php if($settings->effect == 'curtainX'):?>selected="selected"<?php endif;?> >curtainX</option>
					<option value="curtainY" <?php if($settings->effect == 'curtainY'):?>selected="selected"<?php endif;?> >curtainY</option>
					<option value="fade" <?php if($settings->effect == 'fade'):?>selected="selected"<?php endif;?> >fade</option>
					<option value="fadeZoom" <?php if($settings->effect == 'fadeZoom'):?>selected="selected"<?php endif;?> >fadeZoom</option>
					<option value="growX" <?php if($settings->effect == 'growX'):?>selected="selected"<?php endif;?> >growX</option>
					<option value="growY" <?php if($settings->effect == 'growY'):?>selected="selected"<?php endif;?> >growY</option>
					<option value="none" <?php if($settings->effect == 'none'):?>selected="selected"<?php endif;?> >none</option>
					<option value="scrollUp" <?php if($settings->effect == 'scrollUp'):?>selected="selected"<?php endif;?> >scrollUp</option>
					<option value="scrollDown" <?php if($settings->effect == 'scrollDown'):?>selected="selected"<?php endif;?> >scrollDown</option>
					<option value="scrollLeft" <?php if($settings->effect == 'scrollLeft'):?>selected="selected"<?php endif;?> >scrollLeft</option>
					<option value="scrollRight" <?php if($settings->effect == 'scrollRight'):?>selected="selected"<?php endif;?> >scrollRight</option>
					<option value="scrollHorz" <?php if($settings->effect == 'scrollHorz'):?>selected="selected"<?php endif;?> >scrollHorz</option>
					<option value="scrollVert" <?php if($settings->effect == 'scrollVert'):?>selected="selected"<?php endif;?> >scrollVert</option>
					<option value="shuffle" <?php if($settings->effect == 'shuffle'):?>selected="selected"<?php endif;?> >shuffle</option>
					<option value="slideX" <?php if($settings->effect == 'slideX'):?>selected="selected"<?php endif;?> >slideX</option>
					<option value="slideY" <?php if($settings->effect == 'slideY'):?>selected="selected"<?php endif;?> >slideY</option>
					<option value="toss" <?php if($settings->effect == 'toss'):?>selected="selected"<?php endif;?> >toss</option>
					<option value="turnUp" <?php if($settings->effect == 'turnUp'):?>selected="selected"<?php endif;?> >turnUp</option>
					<option value="turnDown" <?php if($settings->effect == 'turnDown'):?>selected="selected"<?php endif;?> >turnDown</option>
					<option value="turnLeft" <?php if($settings->effect == 'turnLeft'):?>selected="selected"<?php endif;?> >turnLeft</option>
					<option value="turnRight" <?php if($settings->effect == 'turnRight'):?>selected="selected"<?php endif;?> >turnRight</option>
					<option value="uncover" <?php if($settings->effect == 'uncover'):?>selected="selected"<?php endif;?> >uncover</option>
					<option value="wipe" <?php if($settings->effect == 'wipe'):?>selected="selected"<?php endif;?> >wipe</option>
					<option value="zoom" <?php if($settings->effect == 'zoom'):?>selected="selected"<?php endif;?> >zoom</option>
				</select>
			</p>
			
			<p>
				Easing:<br />
				<select name="settings_easing">
					<option value="easeIn" <?php if($settings->easing == 'easeIn'):?>selected="selected"<?php endif;?> >easeIn</option>
					<option value="easeOut" <?php if($settings->easing == 'easeOut'):?>selected="selected"<?php endif;?> >easeOut</option>
					<option value="easeInOut" <?php if($settings->easing == 'easeInOut'):?>selected="selected"<?php endif;?> >easeInOut</option>
					<option value="expoin" <?php if($settings->easing == 'expoin'):?>selected="selected"<?php endif;?> >expoin</option>
					<option value="expoout" <?php if($settings->easing == 'expoout'):?>selected="selected"<?php endif;?> >expoout</option>
					<option value="expoinout" <?php if($settings->easing == 'expoinout'):?>selected="selected"<?php endif;?> >expoinout</option>
					<option value="bouncein" <?php if($settings->easing == 'bouncein'):?>selected="selected"<?php endif;?> >bouncein</option>
					<option value="bounceout" <?php if($settings->easing == 'bounceout'):?>selected="selected"<?php endif;?> >bounceout</option>
					<option value="bounceinout" <?php if($settings->easing == 'bounceinout'):?>selected="selected"<?php endif;?> >bounceinout</option>
					<option value="elasin" <?php if($settings->easing == 'elasin'):?>selected="selected"<?php endif;?> >elasin</option>
					<option value="elasout" <?php if($settings->easing == 'elasout'):?>selected="selected"<?php endif;?> >elasout</option>
					<option value="elasinout" <?php if($settings->easing == 'elasinout'):?>selected="selected"<?php endif;?> >elasinout</option>
					<option value="backin" <?php if($settings->easing == 'backin'):?>selected="selected"<?php endif;?> >backin</option>
					<option value="backout" <?php if($settings->easing == 'backout'):?>selected="selected"<?php endif;?> >backout</option>
					<option value="backinout" <?php if($settings->easing == 'backinout'):?>selected="selected"<?php endif;?> >backinout</option>
				</select>
			</p>
			
			<p>
				pager:<br /><input type="text" class="text" name="settings_pager" value="<?php echo $settings->pager;?>" style="width:200px;" />
			</p>
			
			<p>
				next link:<br /><input type="text" class="text" name="settings_next" value="<?php echo $settings->next;?>" style="width:200px;" />
			</p>
		
			<p>
				previous link:<br /><input type="text" class="text" name="settings_previous" value="<?php echo $settings->previous;?>" style="width:200px;" />
			</p>
		
			<p>
				speed (in milli seconds):<br /> <input type="text" class="text" name="settings_speed" value="<?php echo $settings->speed;?>" style="width:200px;" />
			</p>
		
			<p>
				timeout (in milli seconds):<br /> <input type="text" class="text" name="settings_timeout" value="<?php echo $settings->timeout;?>" style="width:200px;" />
			</p>
			</div>
			
			<div style="text-align:right; height:30px;">
				 <input type="submit" value="Update slideshow" />&nbsp;&nbsp;or&nbsp;&nbsp;<a href="<?php echo $_SERVER ['REQUEST_URI']?>&slideshow=<?php echo $slideshow['name'];?>" class="cancel">Delete</a>
			</div>
			
			<div class="clear"></div>
			
			</form>
			
		<?php endforeach;?>
		<?php endif;?>
	
	<?php
}

function get_slideshow_pages($file) {
	
	global $data_path;
		
	if (file_exists($data_path.$file)) {
		
		$v = getXML($data_path.$file);
		$pages = explode('|',$v->slideshow_pages);
	
	}
	
	return $pages;
	
}

function get_slideshow_settings($file) {
	
	global $data_path;
		
	if (file_exists($data_path.$file)) {
		
		$v = getXML($data_path.$file);
		$settings = $v->settings;
	
	}
		
	return $settings;
	
}

function gst_superslideshow($slideshow) {
	
	$data_path = GSDATAPATH."gst-superslideshows/";
	
	if (file_exists($data_path.$slideshow.".xml")) {
		
		$v = getXML($data_path.$slideshow.".xml");
		$pages = explode('|',$v->slideshow_pages);
		
		$settings = $v->settings;
		
		asort($pages);
	
		echo "<script type='text/javascript'>
$(document).ready(function(){
$('#".$slideshow."').cycle({ 
fx:     '".$settings->effect."',
speed:	".$settings->speed.",
timeout: ".$settings->timeout.", 
next:   '".$settings->next."', 
prev:   '".$settings->previous."',
pager:  '".$settings->pager."',
easing: '".$settings->easing."',
cleartype: true,
cleartypeNoBg: true
})
});
</script>";

		echo '<ul id="'.$slideshow.'">';
		
		foreach($pages as $page) {
		
			echo "<li>".gst_superslideshows_get_page($page)."</li>";
		
		}
	
		echo "</ul>";
			
	}
	
}

function gst_superslideshows_get_page($page){   
	
	$path = "data/pages";
    $thisfile = @file_get_contents('data/pages/'.$page.'.xml');
    $data = simplexml_load_string($thisfile);
    return stripslashes(htmlspecialchars_decode($data->content, ENT_QUOTES));;
}


function gst_menu_data($id = null,$xml=false) {
        $menu_extract = '';
        global $PRETTYURLS;
        global $SITEURL;
        
        $path = GSDATAPAGESPATH;
        $dir_handle = @opendir($path) or die("Unable to open $path");
        $filenames = array();
        while ($filename = readdir($dir_handle)) {
            $filenames[] = $filename;
        }
        closedir($dir_handle);
        
        $count="0";
        $pagesArray = array();
        if (count($filenames) != 0) {
            foreach ($filenames as $file) {
                if ($file == "." || $file == ".." || is_dir(GSDATAPAGESPATH . $file) || $file == ".htaccess"  ) {
                    // not a page data file
                } else {
										$data = getXML(GSDATAPAGESPATH . $file);
                    if (1) {
                        $pagesArray[$count]['menuStatus'] = $data->menuStatus;
                        $pagesArray[$count]['menuOrder'] = $data->menuOrder;
                        $pagesArray[$count]['menu'] = $data->menu;
                        $pagesArray[$count]['parent'] = $data->parent;
                        $pagesArray[$count]['title'] = $data->title;
                        $pagesArray[$count]['url'] = $data->url;
                        $pagesArray[$count]['private'] = $data->private;
                        $pagesArray[$count]['pubDate'] = $data->pubDate;
                        $count++;
                    }
                }
            }
        }
        
        $pagesSorted = subval_sort($pagesArray,'menuOrder');
        if (count($pagesSorted) != 0) { 
            $count = 0;
            if (!$xml){
            foreach ($pagesSorted as $page) {
                    $text = (string)$page['menu'];
                    $pri = (string)$page['menuOrder'];
                    $parent = (string)$page['parent'];
                    $title = (string)$page['title'];
                    $slug = (string)$page['url'];
                    $menuStatus = (string)$page['menuStatus'];
                    $private = (string)$page['private'];
										$pubDate = (string)$page['pubDate'];
                    
                    $url = find_url($slug,$parent);
                    
                    $specific = array("slug"=>$slug,"url"=>$url,"parent_slug"=>$parent,"title"=>$title,"menu_priority"=>$pri,"menu_text"=>$text,"menu_status"=>$menuStatus,"private"=>$private,"pub_date"=>$pubDate);
                    
                    if ($id == $slug) { 
                        return $specific; 
                        exit; 
                    } else {
                        $menu_extract[] = $specific;
                    }
                
            } 
            return $menu_extract;
            } else {
            $xml = '<?xml version="1.0" encoding="UTF-8"?><channel>';    
            foreach ($pagesSorted as $page) {
                    $text = $page['menu'];
                    $pri = $page['menuOrder'];
                    $parent = $page['parent'];
                    $title = $page['title'];
                    $slug = $page['url'];
                    $pubDate = $page['pubDate'];
                    $menuStatus = $page['menuStatus'];
                    $private = $page['private'];
                   	
                    $url = find_url($slug,$parent);
                    
                    $xml.="<item>";
                    $xml.="<slug><![CDATA[".$slug."]]></slug>";
                    $xml.="<pubDate><![CDATA[".$pubDate."]]></pubDate>";
                    $xml.="<url><![CDATA[".$url."]]></url>";
                    $xml.="<parent><![CDATA[".$parent."]]></parent>";
                    $xml.="<title><![CDATA[".$title."]]></title>";
                    $xml.="<menuOrder><![CDATA[".$pri."]]></menuOrder>";
                    $xml.="<menu><![CDATA[".$text."]]></menu>";
                    $xml.="<menuStatus><![CDATA[".$menuStatus."]]></menuStatus>";
                    $xml.="<private><![CDATA[".$private."]]></private>";
                    $xml.="</item>";
                    
            }
            $xml.="</channel>";
            return $xml;
            }
        }
    }
    
if (!function_exists('show_message')) {

	function show_message($msg) {
	
		echo '<div style="font-size:13px; width:98%; padding:10px; border:1px solid #FFE226; background-color:#FFF7C2; text-align:left; margin-bottom:20px;">'.$msg.'</div>';
	}
}

function getCycle() {
	
	echo '<script src="/plugins/gst_superslideshows/jquery.js" type="text/javascript"></script>';
	echo '<script src="/plugins/gst_superslideshows/jquery.easing.js" type="text/javascript"></script>';
	echo '<script src="/plugins/gst_superslideshows/jquery.easing.names.js" type="text/javascript"></script>';
	echo '<script src="/plugins/gst_superslideshows/jquery.cycle.min.all.js" type="text/javascript"></script>';
	
}
	
?>