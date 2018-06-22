# Veřejné zakázky na Hlídači státu

## Transformace dat

Český stát se snaží poskytovat open data týkající se veřejných zakázek, ale moc mu to nejde. Nějaká data poskytuje, ale jsou větsinově neúplná, neobsahují zadávací dokumentaci a velmi často jsou i nevalidní.

**A to bychom chtěli s vaší pomocí změnit.**

Potřebujeme pomoc s dolováním a transformací dat z profilů zadavatelů (co to je, vysvětlujeme dále), doplnění zadávacích dokumentací a to vše transformovat a uložit do finální datové struktury Hlídače státu.


## Stručný úvod do veřejných zakázek ČR

Veřejné zakázky (vypsané státní a veřejnou správou) se řídí zákonem [134/2016 Sb.](https://www.zakonyprolidi.cz/cs/2016-134/zneni-20180101) *o zadávání veřejných zakázek*, a souvisejícími vyhláškami. Nás zajímá hlavně vyhláška [168/2016 Sb.](https://www.zakonyprolidi.cz/cs/2016-168) *o uveřejňování formulářů pro účely zákona o zadávání veřejných zakázek a náležitostech profilu zadavatele*.


Veřejné zakázky se zjednodušeně dělí na 2 základní druhy.
* VZ malého rozsahu
* VZ velkého rozsahu

Každá VZ velkého rozsahu musí být současně uveřejněna ve [Věstníku veřejných zakázek](https://www.vestnikverejnychzakazek.cz/) (VVZ). Zde jsou uvedeny pouze základní metadata zakázky, veškerá dokumentace a podrobnosti o zakázce stále zůstávají v profilu zadavatele.

Všechna data ve VVZ jsou dostupná ve formě dumpů XML souborů (po letech) a jsou obnovována přibližně 3x týdně. Tato data Hlídač státu již zpracovává a jsou dostupná v [Hlídači veřejných zakázek](https://www.hlidacstatu.cz/VerejneZakazky). 

### Profil zadavatele

Všechny zakázky musí být vždy uveřejněny na tzv. profilu zadavatele. Zde je ukázka [profilu zadavatele Ministerstva vnitra na NEN](https://nen.nipez.cz/profil/MVCR). Každý zadavatel může mít současně libovolný počet profilů zadavatele.

Dle vyhlášky  [168/2016 Sb.](https://www.zakonyprolidi.cz/cs/2016-168) musí každý profil zadavatele poskytovat data v XML, a to na pevně dané adrese profilu zadavatele.

Technická specifikace uveřejněných dat z vyhlášky:
> a) Základní vybrané informace budou zveřejňovány na profilu ve strukturované podobě. Podrobný popis XML schémat je uveřejněn na internetových stránkách Informačního systému o veřejných zakázkách.
>
> b) Základní informace na profilu zadavatele budou k dispozici na internetové adrese `http://(adresa profilu zadavatele)/XMLdataVZ?od=ddmmrrrr&do=ddmmrrrr`
> nebo `https://(adresa profilu zadavatele)/XMLdataVZ?od=ddmmrrrr&do=ddmnmrr`, kde
> 1. „(adresa profilu zadavatele)“ je internetová adresa evidovaná v seznamu Profilu zadavatelů ve Věstníku veřejných zakázek,
> 2. „`/XMLdataVZ?`“ je konstantní řetězec znaků,
> 3. parametry „od=“ a „do=“ specifikují časový úsek, ve kterém jsou data o veřejných zakázkách poskytována, tj. poskytují se informace o všech veřejných zakázkách evidovaných na profilu zadavatele, které byly v uvedeném období (včetně dnů od - do) uveřejněny na profilu zadavatele, maximální doba mezi parametry od - do je 366 dnů,
> 4. hodnota „ddmmrrrr“ udává strukturu předávaného formátu pro konkrétní datum, kde „dd“ znamená den, „mm“ měsíc a „rrrr“ rok,
> 5. data jsou zpřístupněna prostřednictvím metody GET.

Stahování dat přímo z profilů zadavatelů je poměrně obtížné, musí se dělat po malých časových úsecích a v minimálním množství threadů a zátěže serverů. 

**Toto stahování, často po dohodě s provozovateli, dělá již Hlídač státu a stažené údaje dává k dispozici přes API (s rychlostí a výkonností o několik řádů vyšší). Nestahujte pro potřeby transformace dat XML z profilů, ale využijte výkonné API Hlídače. Díky.** 

## Co dělá Hlídač a co potřebujeme od vás

Hlídač stahuje všechny zakázky velkého rozsahu z VVZ a ty zpracovává a analyzuje. Ale nepřidává k nim žádné informace (zejména zadávací dokumentaci) z profilu zadavatele.
Dále Hlídač stahuje data z profilu zadavatelů, mechanicky 1:1 je konvertuje na JSON a ukládá do Elastic serveru.

### S čím potřebujeme pomoc

Jak píšeme výše, v XML z profilu zadavatele je jen pár základních údajů o zakázce. Ostatní údaje je potřeba scrapovat (stáhnout a vydolovat) z HTML stránek profilu. V ČR existuje několik desítek provozovatelů profilů zadavatelů. Každý provozovatel (jdou poměrně dobře rozlišit podle domény) profilu má jiný design, strukturu stránek a formátování údajů.  

Nejvíce profilů je na doménách (k 15.6.2018):

Doména | počet profilů
------ | --------------
[www.vhodne-uverejneni.cz](https://www.vhodne-uverejneni.cz/zakazka/zs-frymburk-vybaveni-uceben) | 5983
[www.e-zakazky.cz](https://www.e-zakazky.cz/profil-zadavatele/73e0befb-9f53-448c-b748-1f034c285e4c/zakazka/P18V00000065) | 1605
[www.egordion.cz](https://www.egordion.cz/nabidkaGORDION/zakazka.seam?cid=82175) | 1061
[www.profilzadavatele.cz](https://www.profilzadavatele.cz/profil-zadavatele/obec-nesovice_4470/moderni-detske-hriste-zazemi-bez-urazu_18939/) | 608
[www.stavebnionline.cz](https://stavebnionline.cz/Profily/profil.asp?Typ=2&ID=265&IDZak=8037) | 572
[www.tenderarena.cz](https://www.tenderarena.cz/profil/zakazka/detail.jsf?id=189687) | 463
[nen.nipez.cz](https://nen.nipez.cz/ZakladniInformaceOZadavacimPostupu-390252495-310920815/) | 300
[www.kdv.cz](https://www.kdv.cz/pza_zakazka.php?ic=00288683&id=3480) | 296
www.profilyzadavatelu.cz | 258
[zakazky.krajbezkorupce.cz](https://zakazky.krajbezkorupce.cz/contract_display_14425.html) | 239
zakazky.kr-stredocesky.cz | 173
www.softender.cz | 161
uverejnovani.cz | 145
zakazky.rpa.cz | 135
ezakazky.grantikacs.cz | 123
ezak.kr-vysocina.cz | 111
zakazky.cenakhk.cz | 103
www.tendermarket.cz | 89
sluzby.e-zakazky.cz | 89
ezak.cnpk.cz | 87
www.profilzadavatele-vz.cz | 81


### Dostupné datové struktury

#### Cílová datová struktura

Cílová datová struktura, do které je potřeba zdrojová data ztransformovat, je popsaná tímto [JSON Schematem](VerejneZakazky.schema.json). Schema je dostupné i [v čitelnější podobě](https://hlidacstatu.github.io/verejne-zakazky/VerejneZakazkySchema/index.html).

#### Datová struktura zakázek velkého rozsahu
Struktura je totožná jako cílová datová struktura, je potřeba "pouze doplnit" chybějící údaje z profilu zadavatele. TODO  

#### Datová struktura zakázek malého rozsahu
Zakázky malého rozsahu jsou získány z XML exportu profilu zadavatele. Hlídač státu toto XML 1:1 bez jakýchkoliv úprav konvertuje do JSON. 
Popis formátu (adekvátně i JSON struktur) s popisem XML a XSD schémat je možno stáhnout přímo z prohlížeče ve formátu zip z internetové stránky http://www.isvz.cz/ProfilyZadavatelu/Profil_Zadavatele_134_2016_SchemaVZ.zip ([rozbalené](Profil_Zadavatele_134_2016_SchemaVZ.xsd)).


### Dostupná API Hlídače státu - !! již dostupná

Autorizace je prováděna pomocí autentizačního tokenu, který je vám přidělen po registraci na HlidacStatu.cz. 
Autentizační token je nutno odesílat v hlavičce každého požadavku na API.
Příklad k použití:
`curl -X GET https://www.hlidacstatu.cz/Api/v1/Detail/204737 -H 'Authorization: Token XYZABCD'`

Autentizační token pro volání API najdete na https://www.hlidacstatu.cz/api. Na stejném místě najdete i přehled všech nabízenách API serveru.

#### Získání nezpracované zakázky malého rozsahu
Zakázky malého rozsahu jsou Hlídačem státu stáhnuty a zkonvertovány do JSON.

1. Získat další zakázku malého rozsahu, která nebyla zpracována, z domény určené parametrem:
  `curl -X GET https://www.hlidacstatu.cz/Api/v1/VZProfilesList -H 'Authorization: Token XYZABCD'`
  API vrátí pole 250 profilů zadavatelů (id, URL profilu a pocet nezpracovanych zakazek), seřazených podle počtu nezpracovaných zakázek.

2. Získat zakázky z profilu
  `curl -X GET https://www.hlidacstatu.cz/Api/v1/VZList/<id profilu zadavatele> -H 'Authorization: Token XYZABCD'`
Vrátí 50 zakázek (plné záznamy), ze seznamu dosud nezpracovaných zakázek z profilu zadavatele.


**Zpracování - transformace zakázky**

3. V JSON zakázky je pole `dokument`, které v atributu `url` obsahuje URL na HTML stránku, odkud je možné stáhnout binární soubor (PDF, Word, apod) se zadávací dokumentací. Cílem je získat URL samotného binárního souboru.

4. Převést zakázku do cílové datové struktury včetně URL na samotné soubory se zadávací dokumentací. Popisné atributy dokumentů je nutné také převést.

5. Poslat jednu transformovanou zakázku v cílové datové strukturě na Hlídač státu 
  `curl -X POST https://www.hlidacstatu.cz/Api/v1/VZDetail?id=<id zakazky> -H 'Authorization: Token XYZABCD'
       -d '{... json ...}'
  `
  *Toto API bude dostupné během 23.6.2018*
  
6. a takto pro  další zakázky ze seznamu zakázek získaných v 2.

Můžete volat API multithreadově, prosíme však o přiměřenou zátěž.


