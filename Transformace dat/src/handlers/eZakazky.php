<?php

function eZakazky(stdClass $result) {
	foreach ($result->Dokumenty as &$dokument) {
		if (!preg_match('~^https?://~', $dokument['OficialUrl'])) {
			$dokument['OficialUrl'] = "https://$dokument[OficialUrl]";
		}
	}
	unset($dokument);
	directUrl($result);
}
