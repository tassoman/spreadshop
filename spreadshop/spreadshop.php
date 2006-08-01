<?php
/*
Plugin Name: Spreadshop
Plugin URI: http://blog.tassoman.com/spreadshirt-wordpress-plugin
Description: Insert your personal SpreadShirt shop (spreadshop) wrapped into Wordpress without any popup or iframe.
Version: 1.3
Author: Tassoman
Author URI: http://blog.tassoman.com
*/

/*  Copyright 2006  Tassoman  (email: tassoman@gmail.com)

	Translations:
	Italian:	Tassoman		(email: tassoman@gmail.com)
	German:		Frank Bueltge	(email: frank@bueltge.de)

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
					<div class="error"><p>I can't connect to Spreadshirt website!</p></div>
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
	$spread_default["name"] = 'Default shop';
	$spread_default["descr"] = 'This is the default shop at Spreadshirts';
	$spread_default["size"] = 'big';
	$spread_default["order"] = 'Newer first';
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
			<div class="updated"><p>Your Spreadshop was updated!</p></div>
			<?php
		}
		else { ?>
			<div class="error"><p>You've entered wrong data. Please repeat.</p></div>
		<?php }
	}
	else {
		add_option("spreadshop",$spread_default);
	}
	$spread_form = get_option("spreadshop");
	?>
	<div class="wrap">
		<h2>Spreadshop for WordPress</h2>
		<p>This plugin lets you add a Spreadshop made on Spreadshirts either accounted in <a href="http://www.spreadshirts.com" title="Spreadshirts in US">US</a> or <a href="http://www.spreadshirts.net" title="Spreadshirts in Europe">EU</a></p>
		<p>Please fill that module to add or update your shop.</p>
		<p>Actually the only way to get the entire shop imported into your wordpress blog, is creating a page with <span style="font-family: Courier, monospace;">&lt;!--spreadshop--&gt;</span> as content.</p>
		<p>You could use the css class <em>.spreadarticle</em> to manage divs containing articles.</p>
		<p>Use the identificator <em>#spreadshop</em> to manage graphics for entire wrapper.</p>
		<p><strong>Remember</strong> to re-submit this form once you've added new articles on theire website</p>
		<fieldset class="options">
			<legend>Spreadshop settings</legend>
			<form action="" method="GET">
				<input type="hidden" name="page" value="<?=basename(__FILE__);?>" />
				<input type="hidden" name="spread_update" value="1" />
				<ol>
					<li><label>Which continent?</label>
						<select name="spread_site">
							<option selected="selected"><?=$spread_form["site"];?></option>
							<option value=".net">Europe (.net)</option>
							<option value=".com">United States (.com)</option>
						</select>
					<li><label>Shop ID:</label> <input type="text" name="spread_id" value="<?=$spread_form["id"];?>" /></li>
					<li><label>Shop Name:</label> <input type="text" name="spread_name" value="<?=$spread_form["name"];?>" /></li>
					<li><label>Shop description: (html is welcome)</label> <textarea name="spread_descr" style="width:100%; height:5em;" ><?=$spread_form["descr"];?></textarea></li>
					<li><label>Image size:</label>
						<select name="spread_size">
							<option selected="selected"><?=$spread_form["size"];?></option>
							<option>big</option
							<option>small</option
						</select>
					</li>
					<li><label>Articles order:</label>
						<select name="spread_order">
							<option selected="selected"><?=$spread_form["order"];?></option>
							<option>Newer first</option
							<option>Older first</option
						</select>
					<li><label>Edit CSS:</label> <textarea name="spread_css" style="width:100%; height:5em;"><?=$spread_form["css"];?></textarea></li>
				</ol>
				<div class="submit"><input type="submit" value="Update your Spreadshop" /></div>
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
		echo '<style type="text/css" media="screen">',$spreadshop["css"],'</style>',"\n";
		echo '<div id="spreadshop">',"\n";
		echo "<h2>",$spreadshop["name"],"</h2>\n";
		echo "<p>",$spreadshop["descr"],"</p>\n";
		if($spreadshop["order"] != 'Newer first')
			$spreadshop["articles"] = array_reverse($spreadshop["articles"]);
		foreach($spreadshop["articles"] AS $article ) {
			$article =get_object_vars($article);
?>
<div class="spreadarticle">
	<a href="http://www.spreadshirt<?=$spreadshop['site'];?>/shop.php?sid=<?=$spreadshop["id"];?>&amp;article_id=<?=$article["id"];?>" title="<?=$article["name"];?>" target="_blank" rel="no-follow"><img src="http://www.spreadshirt<?=$spreadshop['site'];?>/<?=$article["picurl"];?>" alt="<?=$article["name"];?>" /></a>
	<h3><?=$article["name"];?></h3>
	<p><?=$article["description"];?></p>
	<p>&euro;. <?=$article["price"];?></p>
</div>
<?php
		}
		echo '<div style="clear:both"></div>',"\n";
		echo '<p><a href="http://blog.tassoman.com/wordpress-plugins/spreadshop" title="Spreadshop plugin Open Source">Spreadshop by Tassoman</a></p>';
		echo '</div>',"\n";
		$content = ob_get_contents();
		ob_end_clean();
		$data = substr_replace($data, $content, $start, strlen(SPREAD_PAGE));
	}
	return $data;
}

add_filter('the_content', 'spread_shop_filter');

?>