#!/usr/bin/env php
<?php
include_once __DIR__ . "/util.php";
include_once __DIR__ . "/handlers/eZakazky.php";
include_once __DIR__ . "/handlers/krajbezkorupce.php";
include_once __DIR__ . "/handlers/vhodneUverejneni.php";

$handlers = array(
	'www.vhodne-uverejneni.cz' => 'vhodneUverejneni',
	'www.e-zakazky.cz' => 'eZakazky',
	// www.egordion.cz is not updated to 2016.
	// www.profilzadavatele.cz is not updated to 2016.
	'www.stavebnionline.cz' => 'directUrl',
	'www.tenderarena.cz' => 'directUrl',
	'nen.nipez.cz' => 'directUrl',
	'www.kdv.cz' => 'directUrl',
	'zakazky.krajbezkorupce.cz' => 'krajbezkorupce',
	// www.profilyzadavatelu.cz redirects to www.tenderarena.cz.
);
if ($argc > 1) {
	if ($argc == 2 && isset($handlers[$argv[1]])) {
		$handlers = array($argv[1] => $handlers[$argv[1]]);
	} else {
		echo "Usage: run.php [domain]\n";
		exit(1);
	}
}

if (!file_exists(__DIR__ . '/authToken.txt')) {
	echo "Get your auth token at https://www.hlidacstatu.cz/api/ and save it to authToken.txt.\n";
	exit(1);
}
define('AUTH_TOKEN', rtrim(file_get_contents(__DIR__ . '/authToken.txt')));

$demoUrls = array(
	'www.vhodne-uverejneni.cz' => 'https://www.vhodne-uverejneni.cz/profil/dopravni-podnik-karlovy-vary-a-s/XMLdataVZ?od=18062018&do=18062018',
	'www.e-zakazky.cz' => 'https://www.e-zakazky.cz/profil-zadavatele/cd02694b-87b6-47bc-8f1b-38c30587962c/XMLdataVZ?od=18062018&do=19062018',
	'www.stavebnionline.cz' => 'https://stavebnionline.cz/profil/bzenec/XMLdataVZ?od=25082017&do=26082017',
	'www.tenderarena.cz' => 'https://www.tenderarena.cz/profily/RSD/XMLdataVZ?od=07062018&do=07062018',
	'nen.nipez.cz' => 'https://nen.nipez.cz/profil/MVCR/XMLdataVZ?od=12072017&do=12072017',
	'www.kdv.cz' => 'https://www.kdv.cz/profil.php?ic=00303763/XMLdataVZ?od=18062018&do=18062018',
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
		// 2006: $result->PopisZakazky = $zakazka->VZ->popis;
		$result->DatumUverejneni = $zakazka->VZ->datum_cas_zverejneni;
		// 2006: $result->LhutaDoruceni = $zakazka->VZ->lhuta_pro_podani_nabidek;
		$result->DatumUzavreniSmlouvy = $zakazka->VZ->datum_uzavreni_smlouvy; // 2006: $zakazka->VZ->datum_podpisu
		// 2006: $result->PosledniZmena = $zakazka->VZ->posledni_zmena;
		$result->StavVZ = $zakazka->VZ->stav_vz; // TODO: int.
		// 2006: $result->OdhadovanaHodnotaBezDPH = $zakazka->VZ->predpokladana_hodnota;
		// 2006: $result->OdhadovanaHodnotaMena = $zakazka->VZ->predpokladana_hodnota['menaKod'];
		$result->KonecnaHodnotaBezDPH = $zakazka->dodavatel->cena_celkem_dle_smlouvy_bez_DPH; // TODO: Sum?
		$result->KonecnaHodnotaMena = $zakazka->dodavatel->cena_celkem_dle_smlouvy_bez_DPH['menaKod']; // http://www.sluzby-isvs.cz/isdp/xsd/CoreComponentTypes.xsd
		
		$result->Dokumenty = array();
		foreach ($zakazka->VZ->dokument as $dokument) {
			$url = $dokument->url;
			$result->Dokumenty[] = array(
				'OficialUrl' => $url,
				'TypDokumentu' => $dokument->typ_dokumentu,
				'VlozenoNaProfil' => $dokument->cas_vlozeni_na_profil,
				'CisloVerze' => $dokument->cislo_verze,
			);
		}
		
		/* 2006:
		$result->Kriteria = array(array(
			'Nazev' => $zakazka->VZ->zpusob_hodnoceni,
			// TODO: Popis in $zakazka->VZ->cast_zakazky->zpusob_hodnoceni_textem.
		));
		*/
		
		$result->Dodavatele = array();
		foreach ($zakazka->dodavatel as $dodavatel) {
			$result->Dodavatele[] = array(
				'ICO' => $dodavatel->ico,
				'Jmeno' => $dodavatel->nazev_dodavatele,
			);
		}
		
		/* 2006:
		$cpv = (array) $zakazka->VZ->vedlejsi_cpv;
		if ($zakazka->VZ->hlavni_cpv) {
			array_unshift($cpv, $zakazka->VZ->hlavni_cpv);
		}
		$result->CPV = $cpv;
		*/
		
		$result = (array) $result;
		array_walk_recursive($result, function (&$val, $key) {
			$val = strval($val);
		});
		$result = (object) $result;

		$handler($result);
		print_r($result);
	}
}
