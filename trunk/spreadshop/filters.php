<?php
/*
 * filters.php was created on Sun Nov 25 23:41:01 GMT+01:00 2007 at 23:41:01 by tassoman
 *
 * Copyright (C) 2007 - tassoman
 *
 * http://www.softwarelibero.it/gnudoc/gpl.it.txt
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License Version 2.x
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

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

function showArticle($id, $size = 'small') {
	$spreadshop = get_option('spreadshop');
	foreach($spreadshop["articles"] AS $article ) {
		$article = get_object_vars($article);
		if($article["id"] == $id) {
			$article['picurl'] = ereg_replace('^(.*)(small|medium|big|huge)(.*)$','\\1'.$size.'\\3', $article['picurl']);
?>
<a href="http://www.spreadshirt<?=$spreadshop['site'];?>/shop.php?sid=<?=$spreadshop["id"];?>&amp;article_id=<?=$article["id"];?>" title="<?=$article["name"];?>" target="_blank" rel="nofollow"><img src="http://www.spreadshirt<?=$spreadshop['site'];?>/<?=$article["picurl"];?>" alt="<?=$article["name"];?>" /></a>
<h4><?=$article["name"];?></h4>
<p><?=$article["description"];?> &raquo; <?=$article["price"];?> <?php echo ($spreadshop['site'] == '.net') ? 'EUR' : 'USD';?></p>
<?php
		}
	}
}

function spread_article_filter($data) {
	$pattern = '/\<\!\-\-spreadarticle\=(\d+)(:(small|medium|big|huge))?\-\-\>/';
	while(preg_match($pattern, $data, $matches)) {
		ob_start();
		if($matches[3])
			showArticle($matches[1],$matches[3]);
		else
			showArticle($matches[1]);
		$content = ob_get_contents();
		ob_end_clean();
		$replace_pattern = '/\<\!\-\-spreadarticle\='.$matches[1].'(:'.$matches[3].')?\-\-\>/';
		$data = preg_replace($replace_pattern, $content, $data);
	}
	return $data;
}

function spread_random_article_filter($size = 'small') {
	$spreadshop = get_option('spreadshop');
	$randart = array_rand($spreadshop['articles']);
	$article = get_object_vars($spreadshop['articles'][$randart]);
	$article['picurl'] = ereg_replace('^(.*)(small|medium|big|huge)(.*)$','\\1'.$size.'\\3', $article['picurl']);
?>
<a href="http://www.spreadshirt<?=$spreadshop['site'];?>/shop.php?sid=<?=$spreadshop["id"];?>&amp;article_id=<?=$article["id"];?>" title="<?=$article["name"];?>" target="_blank" rel="nofollow"><img src="http://www.spreadshirt<?=$spreadshop['site'];?>/<?=$article["picurl"];?>" alt="<?=$article["name"];?>" /></a>
<h4><?=$article["name"];?></h4>
<p><?=$article["description"];?> &raquo; <?=$article["price"];?> <?php echo ($spreadshop['site'] == '.net') ? 'EUR' : 'USD';?></p>
<?php
}

?>