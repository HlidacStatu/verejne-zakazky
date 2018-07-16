<?php
include_once __DIR__ . '/../util.php';

function vhodneUverejneni(stdClass $result, stdClass $profile) {
	if (!$result->Dokumenty) {
		return;
	}
	$dokument = $result->Dokumenty[0];
	$dom = downloadHtml($dokument['OficialUrl']);
	// Get VerejnaZakazka URL from Document URL because that's all we have.
	$url = (new DOMXPath($dom))->evaluate("string(//a[starts-with(@href, 'https://www.vhodne-uverejneni.cz/zakazka/')]/@href)");
	
	$html = download($url);
	$result->RawHtml = "$html\n<!-- Downloaded from $url -->";
	$dom = parseHtml($html);
	$xpath = new DOMXPath($dom);
	
	$result->PopisZakazky = $xpath->evaluate("string(//tr[th='Popis']/td)");
	
	$lhutaDoruceni = $xpath->evaluate("string(//tr[th='Datum ukončení příjmu nabídek']/td)");
	if ($lhutaDoruceni) {
		$result->LhutaDoruceni = isoDate($lhutaDoruceni);
	}
	
	setOdhadovanaHodnota($xpath->evaluate("string(//tr[th='Předpokládaná hodnota v Kč bez DPH']/td)"), $result);
	
	$result->CPV = array();
	foreach ($xpath->evaluate("//tr[th='Předmět CPV']/td/ul/li") as $li) {
		$result->CPV[] = $li->textContent; // TODO: Translate to code.
	}
	
	foreach ($result->Dokumenty as &$dokument) {
		$dokument['DirectUrl'] = preg_replace('~&a=detail&~', '&a=download&', $dokument['OficialUrl']);
	}
	unset($dokument);
}
