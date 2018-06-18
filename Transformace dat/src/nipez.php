<?php

function nipez($result) {
	foreach ($result->Dokumenty as &$dokument) {
		$dokument['DirectUrl'] = $dokument['OficialUrl'];
	}
	unset($dokument);
}
