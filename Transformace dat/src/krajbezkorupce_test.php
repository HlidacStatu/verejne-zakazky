<?php
include_once __DIR__ . '/testing.php';
include_once __DIR__ . '/util.php';
include_once __DIR__ . '/krajbezkorupce.php';

function testKrajbezkorupce() {
	$url = 'https://zakazky.krajbezkorupce.cz/document_download_66097.html';
	unlink(cachePath($url)); // The main purpose of this test is to download a fresh document and check its parsing.
	unlink(cachePath('https://zakazky.krajbezkorupce.cz/contract_display_13335.html'));
	$dokument = array('OficialUrl' => $url);
	$result = (object) array('Dokumenty' => array($dokument));
	krajbezkorupce($result);
	assertThat($result->RawHtml)->containsMatch('<html');
	assertThat($result->Dokumenty[0]['DirectUrl'])->isEqualTo('https://zakazky.krajbezkorupce.cz/document_66097/oprava-rozvodu-2-pdf');
	assertThat($result->PopisZakazky)->isEqualTo('Oprava a výměna rozvodů ve stolárně v areálu OU a prš');
	assertThat($result->OdhadovanaHodnotaBezDPH)->isEqualTo(150000);
	assertThat($result->OdhadovanaHodnotaMena)->isEqualTo('Kč');
}
