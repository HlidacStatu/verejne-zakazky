#!/usr/bin/env php
<?php
include_once __DIR__ . "/src/util.php";
include_once __DIR__ . "/src/authToken.php";
include_once __DIR__ . "/src/krajbezkorupce.php";

$handlers = array(
	'nen.nipez.cz' => function ($zakazka) {},
	'zakazky.krajbezkorupce.cz' => 'krajbezkorupce',
);
if ($argc > 1) {
	if ($argc == 2 && isset($handlers[$argv[1]])) {
		$handlers = array($argv[1] => $handlers[$argv[1]]);
	} else {
		echo "Usage: run.php [domain]\n";
		exit(1);
	}
}

$demoUrls = array(
	'nen.nipez.cz' => 'https://nen.nipez.cz/profil/MVCR/XMLdataVZ?od=12072017&do=12072017',
	'zakazky.krajbezkorupce.cz' => 'https://zakazky.krajbezkorupce.cz/profile_display_263.html/XMLdataVZ?od=07022018&do=08022018',
);

// Single threaded to not overload servers.
foreach ($handlers as $server => $handler) {
	//~ $profil = json_decode(download('https://www.hlidacstatu.cz/Api/v1/VZMRList?domena=' . urlencode($server), 'Authorization: Token ' . AUTH_TOKEN));
	$profil = simplexml_load_string(download($demoUrls[$server]));
	$zadavatel = array(
		'ICO' => $profil->zadavatel->ico_vlastni,
		'Jmeno' => $profil->zadavatel->nazev_zadavatele,
	);
	foreach ($profil->zakazka as $zakazka) {
		$result = new stdClass;
		$result->EvidencniCisloZakazky = $zakazka->VZ->kod_vz_na_profilu;
		$result->ZakazkaNaProfiluId = $profil->profil_kod;
		$result->Zadavatel = $zadavatel;
		$result->NazevZakazky = $zakazka->VZ->nazev_vz;
		$result->PopisZakazky = $zakazka->VZ->popis;
		$result->DatumUverejneni = $zakazka->VZ->datum_cas_zverejneni;
		$result->LhutaDoruceni = $zakazka->VZ->lhuta_pro_podani_nabidek;
		$result->DatumUzavreniSmlouvy = $zakazka->VZ->datum_podpisu;
		$result->PosledniZmena = $zakazka->VZ->posledni_zmena;
		$result->StavVZ = $zakazka->VZ->stav_vz; // TODO: int.
		$result->OdhadovanaHodnotaBezDPH = $zakazka->VZ->predpokladana_hodnota;
		$result->KonecnaHodnotaBezDPH = $zakazka->dodavatel->cena_celkem_dle_smlouvy_bez_DPH; // TODO: Sum?
		$result->OdhadovanaHodnotaMena = $zakazka->VZ->predpokladana_hodnota['menaKod']; // http://www.sluzby-isvs.cz/isdp/xsd/CoreComponentTypes.xsd
		$result->KonecnaHodnotaMena = $zakazka->dodavatel->cena_celkem_dle_smlouvy_bez_DPH['menaKod'];
		
		$result->Dokumenty = array();
		foreach ($zakazka->VZ->dokument as $dokument) {
			//~ $url = 'https://zakazky.krajbezkorupce.cz/document_download_66097.html';
			$url = (string) $dokument->url;
			$result->Dokumenty[] = array(
				'OficialUrl' => $url,
				'DirectUrl' => $url,
				'TypDokumentu' => $dokument->typ_dokumentu,
				'VlozenoNaProfil' => $dokument->cas_vlozeni_na_profil,
				'CisloVerze' => $dokument->cislo_verze,
			);
		}
		
		$result->Formulare = array(); // TODO: ?
		
		$result->Kriteria = array(array(
			'Nazev' => $zakazka->VZ->zpusob_hodnoceni,
			// TODO: Popis in $zakazka->VZ->cast_zakazky->zpusob_hodnoceni_textem.
		));
		
		$result->Dodavatele = array();
		foreach ($zakazka->dodavatel as $dodavatel) {
			$result->Dodavatele[] = array(
				'ICO' => $dodavatel->ico,
				'Jmeno' => $dodavatel->nazev_dodavatele,
			);
		}
		
		$cpv = (array) $zakazka->VZ->vedlejsi_cpv;
		if ($zakazka->VZ->hlavni_cpv) {
			array_unshift($cpv, $zakazka->VZ->hlavni_cpv);
		}
		$result->CPV = $cpv;
		
		$result = (array) $result;
		array_walk_recursive($result, function (&$val, $key) {
			$val = strval($val);
		});
		$result = (object) $result;

		$handler($result);
		print_r($result);
	}
}
