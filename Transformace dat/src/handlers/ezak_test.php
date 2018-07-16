<?php
include_once __DIR__ . '/../testing.php';
include_once __DIR__ . '/ezak.php';

function testEzak() {
	$dokument = array('OficialUrl' => 'https://zakazky.krajbezkorupce.cz/document_download_66097.html');
	$result = (object) array('Dokumenty' => array($dokument));
	ezak($result, new stdClass);
	assertThat($result->RawHtml)->containsMatch('<html');
	assertThat($result->Dokumenty[0]['DirectUrl'])->isEqualTo('https://zakazky.krajbezkorupce.cz/document_66097/oprava-rozvodu-2-pdf');
	assertThat($result->PopisZakazky)->isEqualTo('Oprava a výměna rozvodů ve stolárně v areálu OU a prš');
	assertThat($result->OdhadovanaHodnotaBezDPH)->isEqualTo(150000.);
	assertThat($result->OdhadovanaHodnotaMena)->isEqualTo('Kč');
	assertThat($result->LhutaDoruceni)->isEqualTo('2017-12-06T07:00:00');
}
