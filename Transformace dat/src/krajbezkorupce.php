<?php
include_once __DIR__ . "/util.php";

function krajbezkorupce($result) {
	foreach ($result->Dokumenty as &$dokument) {
		$dom = downloadHtml($dokument['OficialUrl']);
		$directUrl = (new DOMXPath($dom))->evaluate("//tr[th='Jméno souboru:']//a")->item(0);
		$dokument['DirectUrl'] = ($directUrl ? absoluteUrl($directUrl->getAttribute('href'), $dokument['OficialUrl']) : null);
	}
	unset($dokument);
}
