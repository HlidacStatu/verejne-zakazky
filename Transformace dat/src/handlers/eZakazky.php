<?php
include_once __DIR__ . "/../util.php";

function eZakazky(stdClass $result) {
	if (isset($result->HtmlUrl)) {
		$html = download($result->HtmlUrl);
		$result->RawHtml = "$html\n<!-- Downloaded from $result->HtmlUrl -->";
		$dom = parseHtml($html);
		$xpath = new DOMXPath($dom);
		
		$result->LhutaDoruceni = isoDate($xpath->evaluate("//th[.='Konec lhůty pro podání nabídek:']/following-sibling::td")->item(0)->textContent);
		
		setOdhadovanaHodnota($xpath->evaluate("//th[.='Předpokládaná hodnota VZ:']/following-sibling::td")->item(0)->textContent, $result);
	}
	
	foreach ($result->Dokumenty as &$dokument) {
		if (!preg_match('~^https?://~', $dokument['OficialUrl'])) {
			$dokument['OficialUrl'] = "https://$dokument[OficialUrl]";
		}
	}
	unset($dokument);
	directUrl($result);
}
