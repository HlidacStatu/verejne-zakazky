<?php
include_once __DIR__ . "/../util.php";

function krajbezkorupce(stdClass $result) {
	if (!$result->Dokumenty) {
		return;
	}
	$dokument = $result->Dokumenty[0];
	$dom = downloadHtml($dokument['OficialUrl']);
	// Get VerejnaZakazka URL from Document URL because that's all we have.
	$url = (new DOMXPath($dom))->evaluate("//a[starts-with(@href, 'contract_display')]")->item(0)->getAttribute('href');
	$url = absoluteUrl($url, $dokument['OficialUrl']);
	
	$html = download($url);
	$result->RawHtml = "$html\n<!-- Downloaded from $url -->";
	$dom = parseHtml($html);
	$xpath = new DOMXPath($dom);
	
	// Stručný popis předmětu:<br>$PopisZakazky
	$result->PopisZakazky = $xpath->evaluate("//p[starts-with(., 'Stručný popis předmětu:')]")->item(0)->childNodes->item(2)->textContent;
	
	// TODO: $result->LhutaDoruceni in "Nabídku podat do:" at https://zakazky.krajbezkorupce.cz/contract_display_13341.html or "Datum nákupu / nabídek" at https://zakazky.krajbezkorupce.cz/contract_display_13335.html
	
	// Předpokládaná hodnota:<b>$OdhadovanaHodnotaBezDPH $OdhadovanaHodnotaMena bez DPH</b>
	// TODO: Following <h4>Předpokládaná hodnota</h4> at https://zakazky.krajbezkorupce.cz/contract_display_12345.html
	setOdhadovanaHodnota($xpath->evaluate("//li[starts-with(., 'Předpokládaná hodnota')]/b")->item(0)->textContent, $result);
	
	$directUrls = array();
	foreach ($xpath->evaluate("//tr[starts-with(td/a/@href, 'document_download')]") as $tr) {
		$as = $xpath->evaluate("td/a", $tr);
		$oficialUrl = preg_replace('~^(document_\d+/)\w+-~', '\1', $as->item(1)->getAttribute('href')); // Strip random prefix.
		$directUrls[absoluteUrl($as->item(0)->getAttribute('href'), $url)] = $oficialUrl;
	}
	foreach ($result->Dokumenty as &$dokument) {
		if (isset($directUrls[$dokument['OficialUrl']])) {
			$dokument['DirectUrl'] = absoluteUrl($directUrls[$dokument['OficialUrl']], $url);
		}
	}
	unset($dokument);
	
	// TODO: $result->Formulare = array();
}
