<?php
include_once __DIR__ . '/../testing.php';
include_once __DIR__ . '/vhodneUverejneni.php';

function testVhodneUverejneni() {
	$dokument = array('OficialUrl' => 'https://www.vhodne-uverejneni.cz/index.php?m=xenorders&h=orderdocument&a=detail&document=1674549');
	$result = (object) array('Dokumenty' => array($dokument));
	vhodneUverejneni($result, new stdClass);
	assertThat($result->RawHtml)->containsMatch('<html');
	assertThat($result->Dokumenty[0]['DirectUrl'])->isEqualTo('https://www.vhodne-uverejneni.cz/index.php?m=xenorders&h=orderdocument&a=download&document=1674549');
	assertThat($result->PopisZakazky)->containsMatch('Předmětem veřejné zakázky je dodávka až 8 kusů nových nízkopodlažních autobusů');
	assertThat($result->LhutaDoruceni)->isEqualTo('2018-07-27T13:00:00');
	assertThat($result->OdhadovanaHodnotaBezDPH)->isEqualTo(130000000.);
	assertThat($result->OdhadovanaHodnotaMena)->isEqualTo('Kč');
	assertThat($result->CPV[0])->isEqualTo('Motorová vozidla pro přepravu více než 10 lidí');
}
