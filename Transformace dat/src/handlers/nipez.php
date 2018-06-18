<?php

function nipez(stdClass $result) {
	// TODO: PopisZakazky is available in "Popis předmětu" at https://nen.nipez.cz/ZakladniInformaceOZadavacimPostupu-123589745-18361112/
	// TODO: CPV is available in "Kód z číselníku CPV" at https://nen.nipez.cz/ZakladniInformaceOZadavacimPostupu-123589745-18361112/
	
	foreach ($result->Dokumenty as &$dokument) {
		$dokument['DirectUrl'] = $dokument['OficialUrl'];
	}
	unset($dokument);
}
