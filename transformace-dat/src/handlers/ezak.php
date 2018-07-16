<?php
include_once __DIR__ . "/../util.php";

function ezak(stdClass $result, stdClass $profile) {
	$searchUrl = absoluteUrl("/contract_search.html?system_number=$result->EvidencniCisloZakazky&archive=ALL&submit_search=1", $profile->url);
	$dom = downloadHtml($searchUrl);
	$url = (new DOMXPath($dom))->evaluate("string(//a[starts-with(@href, 'contract_display_')]/@href)");
	$url = absoluteUrl($url, $searchUrl);
	$html = download($url);
	$result->RawHtml = "$html\n<!-- Downloaded from $url -->";
	$dom = parseHtml($html);
	$xpath = new DOMXPath($dom);
	
	// Stručný popis předmětu:<br>$PopisZakazky
	$result->PopisZakazky = $xpath->evaluate("string(//p[starts-with(., 'Stručný popis předmětu:')]/text()[2])");
	
	if (preg_match('~(?:Nabídku podat do|Datum nákupu / nabídek):\s*([\d. :]+)~', $xpath->evaluate("string(id('centerBlock'))"), $match)) {
		$result->LhutaDoruceni = isoDate($match[1]);
	}
	
	// Předpokládaná hodnota:<b>$OdhadovanaHodnotaBezDPH $OdhadovanaHodnotaMena bez DPH</b>
	// TODO: Following <h4>Předpokládaná hodnota</h4> at https://zakazky.krajbezkorupce.cz/contract_display_12345.html
	setOdhadovanaHodnota($xpath->evaluate("string(//li[starts-with(., 'Předpokládaná hodnota')]/b)"), $result);
	
	$directUrls = array();
	foreach ($xpath->evaluate("//tr[starts-with(td/a/@href, 'document_download_')]") as $tr) {
		$as = $xpath->evaluate("td/a", $tr);
		if ($as->length == 2) {
			$directUrls[absoluteUrl($as->item(0)->getAttribute('href'), $url)] = ezakDirectUrl($as->item(1)->getAttribute('href'), $url);
		}
	}
	
	// TODO: $result->Formulare = array();

	foreach ($result->Dokumenty as &$dokument) {
		if (isset($directUrls[$dokument['OficialUrl']])) {
			$dokument['DirectUrl'] = $directUrls[$dokument['OficialUrl']];
		} else {
			// TODO: /document_download_1941.html -> /document_1941/_
			$dom = downloadHtml($dokument['OficialUrl']);
			$dokument['DirectUrl'] = ezakDirectUrl((new DOMXPath($dom))->evaluate("string(//tr[th='Jméno souboru:']/td/a/@href)"), $dokument['OficialUrl']);
		}
	}
	unset($dokument);
}

function ezakDirectUrl($href, $url) {
	return preg_replace('~(/document_\d+/)\w+-~', '\1', absoluteUrl($href, $url)); // Strip random prefix.
}
