<?php
include_once __DIR__ . "/../testing.php";
include_once __DIR__ . "/eZakazky.php";

function testEZakazky() {
	$dokumentUrl = 'www.e-zakazky.cz/stazenisouboru/02b281a5-a0ae-48f6-b9f4-fbaf9d368b96';
	$dokument = array('OficialUrl' => $dokumentUrl);
	$result = (object) array(
		// TODO: This field is currently not populated.
		'HtmlUrl' => 'https://www.e-zakazky.cz/profil-zadavatele/cd02694b-87b6-47bc-8f1b-38c30587962c/zakazka/P18V00000003',
		'Dokumenty' => array($dokument),
	);
	eZakazky($result);
	assertThat($result->LhutaDoruceni)->isEqualTo('2018-04-19');
	assertThat($result->OdhadovanaHodnotaBezDPH)->isEqualTo(800000.);
	assertThat($result->OdhadovanaHodnotaMena)->isEqualTo('Kč');
	assertThat($result->Dokumenty[0]['DirectUrl'])->isEqualTo("https://$dokumentUrl");
	assertThat($result->Dokumenty[0]['OficialUrl'])->isEqualTo("https://$dokumentUrl");
}
