<?php
/*
Plugin Name: Spreadshop
Plugin URI: http://blog.tassoman.com/spreadshirt-wordpress-plugin
Description: Insert your personal SpreadShirt's shop (spreadshop) wrapped into Wordpress without any popup or iframe.
Version: 1.6
Author: Tassoman
Author URI: http://blog.tassoman.com
*/

/*  Copyright 2006  Tassoman  (tassoman@gmail.com)

	Translations:
	Italian:	Tassoman		(tassoman@gmail.com)
	German:		Frank Bueltge	(frank@bueltge.de)
	French:		David Allard	(guerdal@free.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('SPREAD_VER', 1.6);

if(function_exists('load_plugin_textdomain'))
	load_plugin_textdomain('spreadshop','wp-content/plugins/spreadshop');

/**
 * Let's add the menu links
 * */
function add_spread_menu() {
	if(function_exists('add_submenu_page'))
		add_submenu_page('edit.php', __('Spreadshop plugin','spreadshop'), 'Spreadshop', 8, basename(__FILE__), 'spread_sub_page');
}

/**
 * Let's create the admin page
 *
 * */
function spread_sub_page() {
	if(!class_exists('spread_article')) {
		class spread_article {
			var $id;
			var $name;
			var $description;
			var $picurl;
			var $price;
			var $producttypename;
				function spread_article($aa) {
					foreach ($aa as $key=>$value) {
						$this->$key = utf8_decode($aa[$key]);
					}
					return $this;
				}
			}
	}
	if(!function_exists('get_spread_articles')) {

		/**
		 * Let's fetch the xml source
		 * */
		function get_spread_articles($id, $size, $site) {
			if (ini_get("allow_url_fopen")) {
				$handle = @fopen('http://www.spreadshirt' . $site . '/articlefeed.php?sid=' . $id . '&picsize=' . $size, 'r');
				if($handle) {
					while (!feof($handle)) {
						$buffer[] = fgets($handle, 4096);
					}
					$data = implode('', $buffer);
					fclose ($handle);
				}
				else {
?>
					<div class="error"><p><?php _e("I can't connect to Spreadshirt website!",'spreadshop');?></p></div>
<?php			}
			}
			else {
				$data = "";
   				$handle = fsockopen('www.spreadshirt'.$site, 80);
   				$filesrc = '/articlefeed.php?sid=' . $id . '&picsize=' . $size;
				$rqst = "GET $filesrc HTTP/1.1\r\nAccept: */*\r\nAccept-Language: en\r\nAccept-Encoding: gzip, deflate\r\nUser-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)\r\nHost: www.spreadshirt$site:80\r\nConnection: Keep-Alive\r\n\r\n";
       			fputs($handle, $rqst);
       			while (!feof($handle)) {
					$data .= fread($fp, 4096);
       			}
       			fclose($handle);
       			$data = substr($data, strpos($data, "\r\n\r\n") + 4);
   			}

			// parse xml data into array
			$parser = xml_parser_create();
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parse_into_struct($parser, $data, $values, $tags);
			xml_parser_free($parser);

			foreach ($tags as $key=>$val) {
				if ($key == "shop") {
					$attributes = $val;
					// each contiguous pair of array entries are the
					// lower and upper range for each article definition
					for ($i=0; $i < (count($attributes)-2); $i++) {
						$offset = $attributes[$i] + 1;
						$len = $attributes[$i + 1] - $offset;
						$shoparticles[] = spread_sortattributes(array_slice($values, $offset, $len));
					}
				}
				else continue;
			}

			// write article datas
			return $shoparticles;
		}
	}

// order values to keys
function spread_sortattributes($values) {
	for ($i=0; $i < count($values); $i++) {
		$attr[$values[$i]["tag"]] = $values[$i]["value"];
  	}
   	return new spread_article($attr);
}
	$spread_default['site'] = '.net';
	$spread_default["id"] = 92393;
	$spread_default["name"] = __('Default shop name','spreadshop');
	$spread_default["descr"] = __('This is the default shop description','spreadshop');
	$spread_default["size"] = 'big';
	$spread_default["order"] = 'desc';
	$spread_default["css"] = "
.spreadarticle {
clear: left;
border-bottom: 1px solid #666;
margin-bottom: 2em;
}
.spreadarticle img {
float: left;
}
.spreadarticle h3, .spreadarticle p {
margin-left: 150px;
}
";
	$spread_default["articles"] = get_spread_articles($spread_default["id"],$spread_default["size"],$spread_default['site']);

	if($_GET["spread_update"]) {
		if(ereg("[0-9]{3,6}",$_GET["spread_id"])) {
			$spreadshop['site'] = $_GET['spread_site'];
			$spreadshop["id"] = $_GET["spread_id"];
			$spreadshop["name"] = $_GET["spread_name"];
			$spreadshop["descr"] = $_GET["spread_descr"];
			$spreadshop["size"] = $_GET["spread_size"];
			$spreadshop["order"] = $_GET["spread_order"];
			$spreadshop["css"] = $_GET["spread_css"];
			$spreadshop["articles"] = get_spread_articles($_GET["spread_id"],$_GET["spread_size"], $_GET["spread_site"]);
			update_option("spreadshop", $spreadshop);
			?>
			<div class="updated"><p><?php _e('Your Spreadshop was updated!','spreadshop');?></p></div>
			<?php
		}
		else { ?>
			<div class="error"><p><?php _e("You've entered wrong data. Please repeat.",'spreadshop');?></p></div>
		<?php }
	}
	else {
		add_option("spreadshop",$spread_default);
	}
	$spread_form = get_option("spreadshop");
	?>
	<div class="wrap">
		<h2><?php _e('Spreadshop plugin for WordPress','spreadshop');?></h2>
		<p><?php _e('This plugin lets you add a Spreadshop registered on Spreadshirt website either accounted','spreadshop');?> <a href="http://www.spreadshirts.com" title="Spreadshirt <?php _e('in USA','spreadshop');?>"><?php _e('in USA','spreadshop');?></a> <?php _e('or','spreadshop');?> <a href="http://www.spreadshirts.net" title="Spreadshirt <?php _e('in Europe','spreadshop');?>"><?php _e('in Europe','spreadshop');?></a></p>
		<p><?php _e('Please fill the following form to update your Spreadshop.','spreadshop');?></p>
		<p><?php _e('For now, the only way to get the entire shop imported into your wordpress blog, is creating a page with','spreadshop');?> <span style="font-family: Courier, monospace;">&lt;!--spreadshop--&gt;</span> <?php _e('as content of post.','spreadshop');?></p>
		<p><?php _e('If you want to manage CSS style for each article, you must edit this CSS class:','spreadshop');?> <em>.spreadarticle</em></p>
		<p><?php _e('To edit the Spreadshop wrapper, use this CSS identificator:','spreadshop');?> <em>#spreadshop</em></p>
		<p><strong><?php _e('Warning:','spreadshop');?></strong> <?php _e('You must update this options every time you update the shop on Spreadshirt','spreadshop');?></p>
		<fieldset class="options">
			<legend><?php _e('Spreadshop settings','spreadshop');?></legend>
			<form action="" method="GET">
				<input type="hidden" name="page" value="<?=basename(__FILE__);?>" />
				<input type="hidden" name="spread_update" value="1" />
				<ol>
					<li><label><?php _e('Which continent?','spreadshop');?></label>
						<select name="spread_site">
							<option selected="selected"><?=$spread_form["site"];?></option>
							<option value=".net"><?php _e('in Europe','spreadshop');?> (.net)</option>
							<option value=".com"><?php _e('in USA','spreadshop');?> (.com)</option>
						</select>
					<li><label><?php _e('Shop ID:','spreadshop');?></label> <input type="text" name="spread_id" value="<?=$spread_form["id"];?>" /></li>
					<li><label><?php _e('Shop Name:','spreadshop');?></label> <input type="text" name="spread_name" value="<?=$spread_form["name"];?>" /></li>
					<li><label><?php _e('Shop description:','spreadshop');?> <?php _e('(html is welcome)','spreadshop');?></label> <textarea name="spread_descr" style="width:100%; height:5em;" ><?=$spread_form["descr"];?></textarea></li>
					<li><label><?php _e('Image size:','spreadshop');?></label>
						<select name="spread_size">
							<option selected="selected"><?=$spread_form["size"];?></option>
							<option value="small"><?php _e('small (42x42)','spreadshop');?></option>
							<option value="medium"><?php _e('medium (130x130)','spreadshop');?></option>
							<option value="big"><?php _e('big (190x190)','spreadshop');?></option>
							<option value="huge"><?php _e('huge (280x280)','spreadshop');?></option>
						</select>
					</li>
					<li><label><?php _e('Order articles by:','spreadshop');?></label>
						<select name="spread_order">
							<option selected="selected"><?=$spread_form["order"];?></option>
							<option value="desc"><?php _e('Last item, first object','spreadshop');?></option>
							<option value="asc"><?php _e('First item, first object','spreadshop');?></option>
						</select>
					<li><label><?php _e('CSS editing:','spreadshop');?></label>
						<textarea name="spread_css" style="width:100%; height:5em;"><?=$spread_form["css"];?></textarea></li>
				</ol>
				<div class="submit"><input type="submit" value="<?php _e('Update Spreadshop options','spreadshop');?>" /></div>
			</form>
		</fieldset>
		<fieldset class="options">
<?php
if(in_array(WPLANG, array('en_US', 'it_IT','nl_NL','de_DE','fr_FR','zh_CN','es_ES'))) $langpal = WPLANG;
else $langpal = 'en_US';

if(ini_get('allow_url_fopen')) {
	$stableversion = (ini_get('allow_url_fopen')) ? file('http://spreadshop.googlecode.com/svn/trunk/spreadshop/stable.ver') : array(99.9) ;
	$spreadstatus = ( SPREAD_VER < $stableversion[0] ) ? '<span style="color:red;">' . __('must be upgraded','spreadshop') . '</span>' : __('is lastest','spreadshop') ;
}
else $spreadstatus = '<span style="color:orange;">' . __('can\'t be detected','spreadshop') . '</span> ' . __('remember to check for updates often','spreadshop');
?>
			<h3><?php _e('Your version of','spreadshop');?> <?php _e('Spreadshop plugin','spreadshop');?> <?php echo $spreadstatus; ?></h3>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="image" src="https://www.paypal.com/<?=$langpal;?>/i/btn/x-click-but11.gif" border="0" name="submit" alt="Make a donation">
				<img alt="" border="0" src="https://www.paypal.com/it_IT/i/scr/pixel.gif" width="1" height="1">
				<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
			</form>
		</fieldset>
	</div>
	<?php
}

if(function_exists('add_action'))
	add_action('admin_menu','add_spread_menu');

define('SPREAD_PAGE', '<!--spreadshop-->');

function spread_shop_filter($data) {
	$start = strpos($data, SPREAD_PAGE);
	if($start !== false) {
		ob_start();
		$spreadshop = get_option('spreadshop');
		echo '<style type="text/css">',$spreadshop["css"],'</style>',"\n";
		echo '<div id="spreadshop">',"\n";
		echo "<h2>",$spreadshop["name"],"</h2>\n";
		echo "<p>",$spreadshop["descr"],"</p>\n";
		if($spreadshop["order"] != 'desc')
			$spreadshop["articles"] = array_reverse($spreadshop["articles"]);
		foreach($spreadshop["articles"] AS $article ) {
			$article =get_object_vars($article);
?>
<div class="spreadarticle">
	<a href="http://www.spreadshirt<?=$spreadshop['site'];?>/shop.php?sid=<?=$spreadshop["id"];?>&amp;article_id=<?=$article["id"];?>" title="<?=$article["name"];?>" target="_blank" rel="nofollow"><img src="http://www.spreadshirt<?=$spreadshop['site'];?>/<?=$article["picurl"];?>" alt="<?=$article["name"];?>" /></a>
	<h3><?=$article["name"];?></h3>
	<p><?=$article["description"];?></p>
	<p><?=$article["price"];?> <?php echo ($spreadshop['site'] == '.net') ? 'EUR' : 'USD';?></p>
</div>
<?php
		}
		echo '<div style="clear:both"></div>',"\n";
		echo '<p style="visibility: hidden !important;"><a href="http://blog.tassoman.com/wordpress-plugins/spreadshop" title="Spreadshop plugin Open Source">Spreadshop by Tassoman</a></p>';
		echo '</div>',"\n";
		$content = ob_get_contents();
		ob_end_clean();
		$data = substr_replace($data, $content, $start, strlen(SPREAD_PAGE));
	}
	return $data;
}

function randomSpreadArticle($size = 'small') {
	$spreadshop = get_option('spreadshop');
	$randart = array_rand($spreadshop['articles']);
	$article = get_object_vars($spreadshop['articles'][$randart]);
	$article['picurl'] = ereg_replace('^(.*)(small|medium|big|huge)(.*)$','\\1'.$size.'\\3', $article['picurl']);
?>
<a href="http://www.spreadshirt<?=$spreadshop['site'];?>/shop.php?sid=<?=$spreadshop["id"];?>&amp;article_id=<?=$article["id"];?>" title="<?=$article["name"];?>" target="_blank" rel="nofollow"><img src="http://www.spreadshirt<?=$spreadshop['site'];?>/<?=$article["picurl"];?>" alt="<?=$article["name"];?>" /></a>
<h4><?=$article["name"];?></h4>
<p><?=$article["description"];?> &raquo; <?=$article["price"];?> <?php echo ($spreadshop['site'] == '.net') ? 'EUR' : 'USD';?></p>
</div>
<?php
}

add_filter('the_content', 'spread_shop_filter');

?>
