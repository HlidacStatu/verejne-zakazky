<?php
include_once __DIR__ . "/../testing.php";
include_once __DIR__ . "/eZakazky.php";

function testEZakazky() {
	$url = 'www.e-zakazky.cz/stazenisouboru/02b281a5-a0ae-48f6-b9f4-fbaf9d368b96';
	$dokument = array('OficialUrl' => $url);
	$result = (object) array('Dokumenty' => array($dokument));
	eZakazky($result);
	assertThat($result->Dokumenty[0]['DirectUrl'])->isEqualTo("https://$url");
	assertThat($result->Dokumenty[0]['OficialUrl'])->isEqualTo("https://$url");
}
