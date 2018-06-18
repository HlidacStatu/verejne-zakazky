<?php
include_once __DIR__ . '/testing.php';
include_once __DIR__ . '/util.php';
include_once __DIR__ . '/krajbezkorupce.php';

function testKrajbezkorupce() {
	$url = 'https://zakazky.krajbezkorupce.cz/document_download_66097.html';
	unlink(cachePath($url)); // The main purpose of this test is to download a fresh document and check its parsing.
	$dokument = array('OficialUrl' => $url);
	$result = (object) array('Dokumenty' => array($dokument));
	krajbezkorupce($result);
	assertThat($result->Dokumenty[0]['DirectUrl'])->containsMatch('https://zakazky\.krajbezkorupce\.cz/document_66097/\w+-oprava-rozvodu-2-pdf');
}
