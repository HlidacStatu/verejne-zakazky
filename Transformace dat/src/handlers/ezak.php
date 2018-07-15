<?php
include_once __DIR__ . "/../util.php";

function ezak(stdClass $result) {
	if (!$result->Dokumenty) {
		// TODO: Get from /contract_search.html?system_number=$result->EvidencniCisloZakazky&archive=ALL&submit_search=1
		return;
	}
	$directUrls = array();
	foreach ($result->Dokumenty as &$dokument) {
		if (isset($directUrls[$dokument['OficialUrl']])) {
			$dokument['DirectUrl'] = $directUrls[$dokument['OficialUrl']];
		} else {
			$dom = downloadHtml($dokument['OficialUrl']);
			$xpath = new DOMXPath($dom);
			$dokument['DirectUrl'] = ezakDirectUrl($xpath->evaluate("//tr[th='Jméno souboru:']/td/a")->item(0)->getAttribute('href'), $dokument['OficialUrl']);
			if (!$directUrls) {
				// Get VerejnaZakazka URL from Document URL because that's all we have.
				$url = $xpath->evaluate("//a[starts-with(@href, 'contract_display_')]")->item(0)->getAttribute('href');
				$url = absoluteUrl($url, $dokument['OficialUrl']);
				$html = download($url);
				$result->RawHtml = "$html\n<!-- Downloaded from $url -->";
				$dom = parseHtml($html);
				$xpath = new DOMXPath($dom);
				
				// Stručný popis předmětu:<br>$PopisZakazky
				$result->PopisZakazky = $xpath->evaluate("//p[starts-with(., 'Stručný popis předmětu:')]")->item(0)->childNodes->item(2)->textContent;
				
				if (preg_match('~(?:Nabídku podat do|Datum nákupu / nabídek):\s*([\d. :]+)~', $xpath->evaluate("id('centerBlock')")->item(0)->textContent, $match)) {
					$result->LhutaDoruceni = isoDate($match[1]);
				}
				
				// Předpokládaná hodnota:<b>$OdhadovanaHodnotaBezDPH $OdhadovanaHodnotaMena bez DPH</b>
				// TODO: Following <h4>Předpokládaná hodnota</h4> at https://zakazky.krajbezkorupce.cz/contract_display_12345.html
				setOdhadovanaHodnota($xpath->evaluate("//li[starts-with(., 'Předpokládaná hodnota')]/b")->item(0)->textContent, $result);
				
				foreach ($xpath->evaluate("//tr[starts-with(td/a/@href, 'document_download_')]") as $tr) {
					$as = $xpath->evaluate("td/a", $tr);
					if ($as->length == 2) {
						$directUrls[absoluteUrl($as->item(0)->getAttribute('href'), $url)] = ezakDirectUrl($as->item(1)->getAttribute('href'), $url);
					}
				}
				
				// TODO: $result->Formulare = array();
			}
		}
	}
	unset($dokument);
}

function ezakDirectUrl($href, $url) {
	return preg_replace('~(/document_\d+/)\w+-~', '\1', absoluteUrl($href, $url)); // Strip random prefix.
}
