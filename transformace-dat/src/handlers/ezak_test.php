<?php
include_once __DIR__ . '/../testing.php';
include_once __DIR__ . '/ezak.php';

function testEzak() {
	$dokument = array('OficialUrl' => 'https://zakazky.cenakhk.cz/document_download_31255.html');
	$result = (object) array('EvidencniCisloZakazky' => 'P18V00000537', 'Dokumenty' => array($dokument));
	$profile = (object) array('url' => 'https://zakazky.cenakhk.cz/profile_display_2.html');
	ezak($result, $profile);
	assertThat($result->RawHtml)->containsMatch('<html');
	assertThat($result->Dokumenty[0]['DirectUrl'])->isEqualTo('https://zakazky.cenakhk.cz/document_31255/vidkon_zadavaci_podminky-pdf');
	assertThat($result->PopisZakazky)->isEqualTo('Předmětem veřejné zakázky je dodávka videokonferenčního zařízení do prostor simulačního polygonu a sanitního vozidla včetně souvisejících dodávek a služeb.');
	assertThat($result->OdhadovanaHodnotaBezDPH)->isEqualTo(990000.);
	assertThat($result->OdhadovanaHodnotaMena)->isEqualTo('Kč');
	assertThat($result->LhutaDoruceni)->isEqualTo('2018-08-13T11:00:00');
}
