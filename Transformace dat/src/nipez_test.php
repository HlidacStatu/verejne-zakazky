<?php
include_once __DIR__ . '/testing.php';
include_once __DIR__ . '/nipez.php';

function testNipez() {
	$url = 'https://nen.nipez.cz/Soubor.aspx?id=135232650&typ=.rar&velikost=662379B';
	$dokument = array('OficialUrl' => $url);
	$result = (object) array('Dokumenty' => array($dokument));
	nipez($result);
	assertThat($result->Dokumenty[0]['DirectUrl'])->isEqualTo($url);
}
