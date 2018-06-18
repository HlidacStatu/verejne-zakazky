<?php
include_once __DIR__ . '/../util.php';

function vhodneUverejneni(stdClass $result) {
	if (!$result->Dokumenty) {
		return;
	}
	$dokument = $result->Dokumenty[0];
	$dom = downloadHtml($dokument['OficialUrl']);
	// Get VerejnaZakazka URL from Document URL because that's all we have.
	$url = (new DOMXPath($dom))->evaluate("//a[starts-with(@href, 'https://www.vhodne-uverejneni.cz/zakazka/')]")->item(0)->getAttribute('href');
	
	$html = download($url);
	$result->RawHtml = "$html\n<!-- Downloaded from $url -->";
	$dom = parseHtml($html);
	$xpath = new DOMXPath($dom);
	
	$result->PopisZakazky = $xpath->evaluate("//tr[th='Popis']/td")->item(0)->textContent;
	
	$lhutaDoruceni = $xpath->evaluate("//tr[th='Datum ukončení příjmu nabídek']/td");
	if ($lhutaDoruceni->length) {
		$result->LhutaDoruceni = isoDate($lhutaDoruceni->item(0)->textContent);
	}
	
	$predpokladanaHodnota = price($xpath->evaluate("//tr[th='Předpokládaná hodnota v Kč bez DPH']/td")->item(0)->textContent);
	if ($predpokladanaHodnota) {
		$result->OdhadovanaHodnotaBezDPH = $predpokladanaHodnota['amount'];
		$result->OdhadovanaHodnotaMena = $predpokladanaHodnota['currency'];
	}
	
	$result->CPV = array();
	foreach ($xpath->evaluate("//tr[th='Předmět CPV']/td/ul/li") as $li) {
		$result->CPV[] = $li->textContent; // TODO: Translate to code.
	}
	
	foreach ($result->Dokumenty as &$dokument) {
		$dokument['DirectUrl'] = preg_replace('~&a=detail&~', '&a=download&', $dokument['OficialUrl']);
	}
	unset($dokument);
}
