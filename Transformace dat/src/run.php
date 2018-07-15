#!/usr/bin/env php
<?php
include_once __DIR__ . "/util.php";
include_once __DIR__ . "/handlers/eZakazky.php";
include_once __DIR__ . "/handlers/ezak.php";
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
	'zakazky.krajbezkorupce.cz' => 'ezak',
	'zakazky.lesycr.cz' => 'ezak',
	// www.profilyzadavatelu.cz redirects to www.tenderarena.cz.
);
$profileId = ($argc == 2 ? $argv[1] : null);
if ($argc > 2 || ($profileId && !preg_match('~^VVZ-~', $profileId))) {
	echo "Usage: run.php [profileId]\n";
	exit(1);
}

if (!file_exists(__DIR__ . '/authToken.txt')) {
	echo "Get your auth token at https://www.hlidacstatu.cz/api/ and save it to authToken.txt.\n";
	exit(1);
}
define('AUTH_TOKEN', rtrim(file_get_contents(__DIR__ . '/authToken.txt')));

$url = 'https://www.hlidacstatu.cz/Api/v1/VZProfilesList';
//~ unlink(cachePath($url));
foreach (json_decode(download($url)) as $profile) {
	if ($profileId && $profileId != $profile->profileId) {
		continue;
	}
	$zakazky = json_decode(download("https://www.hlidacstatu.cz/Api/v1/VZList/$profile->profileId"));
	foreach ($zakazky as $zakazkaWrapper) {
		$zakazka = $zakazkaWrapper->ZakazkaNaProfilu;
		$result = new stdClass;
		$result->EvidencniCisloZakazky = $zakazka->VZ->kod_vz_na_profilu->Value;
		$result->ZakazkaNaProfiluId = $zakazkaWrapper->Profil;
		// TODO: $result->Zadavatel = $zadavatel;
		$result->NazevZakazky = $zakazka->VZ->nazev_vz->Value;
		$result->DatumUverejneni = $zakazka->VZ->datum_cas_zverejneni->Value;
		$result->DatumUzavreniSmlouvy = $zakazka->VZ->datum_uzavreni_smlouvy->Value;
		// Not set according to #4: $result->StavVZ = $zakazka->VZ->stav_vz->Value;
		if ($zakazka->dodavatel) {
			// TODO: Get from $zakazka->VZ->cast_zakazky[]->dodavatel if available.
			$konecnaHodnotaBezDPH = 0;
			foreach ($zakazka->dodavatel as $dodavatel) {
				$konecnaHodnotaBezDPH += $dodavatel->cena_celkem_dle_smlouvy_bez_DPH->Value;
			}
			$result->KonecnaHodnotaBezDPH = $konecnaHodnotaBezDPH;
			$result->KonecnaHodnotaMena = $zakazka->dodavatel[0]->cena_celkem_dle_smlouvy_bez_DPH->menaKod;
		}
		$result->Dokumenty = array();
		foreach ($zakazka->VZ->dokument as $dokument) {
			$result->Dokumenty[] = array(
				'OficialUrl' => $dokument->url->Value,
				'TypDokumentu' => $dokument->typ_dokumentu->Value,
				'VlozenoNaProfil' => $dokument->cas_vlozeni_na_profil->Value,
				'CisloVerze' => $dokument->cislo_verze->Value,
			);
		}
		
		$result->Dodavatele = array();
		foreach ($zakazka->dodavatel as $dodavatel) {
			// TODO: Add $zakazka->VZ->cast_zakazky[]->dodavatel if available.
			$result->Dodavatele[] = array(
				'ICO' => $dodavatel->ico->Value,
				'Jmeno' => $dodavatel->nazev_dodavatele->Value,
			);
		}
		
		preg_match('~^https?://([^/]+)~', $profile->url, $match);
		list(, $domain) = $match;
		if (isset($handlers[$domain])) {
			/* Fields to populate:
			PopisZakazky
			LhutaDoruceni
			OdhadovanaHodnotaBezDPH
			OdhadovanaHodnotaMena
			RawHtml
			Dokumenty[].DirectUrl
			Formulare
			Kriteria
			CPV
			*/
			$handlers[$domain]($result);
		}
		print_r($result);
		exit;
	}
}
