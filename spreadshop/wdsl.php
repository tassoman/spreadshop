<?php
/* http://developer.spreadshirt.net/book/export/html/7 */

define('WPLANG', 'it_IT');
define('SHOPID', 92393);
#define('SHOPID', 260828);

function spread_get_country() {
	if( strlen(WPLANG) == 5 ) {
		return substr(WPLANG, -2);
	}
	return 'IT';
}

function spread_get_language() {
	if( strlen(WPLANG) == 5 ) {
		return substr(WPLANG, 0, 2);
	}
	return 'it';
}

function spread_get_domain($country) {
	if($country == 'US' || $country == 'CA') {
		return 'com';
	}
	return 'net';
}

$wsdl = sprintf('http://www.spreadshirt.%s/services.php?wsdl', spread_get_domain( spread_get_country() ) );

echo $wsdl;

$client = new SoapClient($wsdl, array('exceptions' => 0, 'trace' => 1));

$session_token = $client->initialize_session(SHOPID);

$s = microtime(true);
$articles = $client->get_articles($session_token, SHOPID);
$e = microtime(true);

$d = $e-$s;

print "Durata: $d<br />";

var_dump($articles);
?>