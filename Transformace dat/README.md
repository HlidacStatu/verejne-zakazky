# Veřejné zakázky na Hlídači Státu

## Transformace dat

Český stát se snaží poskytovat open data týkající se veřejných zakázek, ale moc mu to nejde. Nějaká data poskytuje, ale jsou větsinove neúplná, neobsahují zadávací dokumentaci a velmi častou jsou i nevalidní.

**A to bychom chtěli s vaší pomocí změnit**

Potřebujeme pomoc s dolováním a transoformací dat z profilů zadavatelů (co to je vysvětlujeme dále), doplnění zadávacích dokumentací a to vše transformovat a uložit do finální datové struktury Hlídače Státu.


## Stručný úvod do veřejných zakázek ČR

Veřejné zakázky (vypsané státní a veřejnou správou) se řídí zákonem [134/2016 Sb.](https://www.zakonyprolidi.cz/cs/2016-134/zneni-20180101) *o zadávání veřejných zakázek*, a souvisejícími vyhláškami. Nás zajímá hlavně vyhláška [168/2016 Sb.](https://www.zakonyprolidi.cz/cs/2016-168) *o uveřejňování formulářů pro účely zákona o zadávání veřejných zakázek a náležitostech profilu zadavatele*


Veřejné zakázky se zjednodušeně dělí na 2 základní druhy.
* VZ malého rozsahu
* VZ velkého rozsahu

Každá VZ vekého rozsahu musí být současně uveřejněna ve [Věstníku veřejných zakázek](https://www.vestnikverejnychzakazek.cz/) (VVZ). Zde jsou uvedeny pouze základní metadata zakázky, veškerá dokumentace a podrobnosti o zakázce stále zůstávají v profilu zadavatele.

Všechna data ve VVZ jsou dostupná ve formě dumpů XML souborů (po letech) a jsou obnovována přibližně 3x týdně. Tato data Hlídač státu již zpracovává a jsou dostupná v [Hlídači veřejných zakázek](https://www.hlidacstatu.cz/VerejneZakazky). 

### Profil zadavatele

Všechny zakázky musí být vždy uveřejněny na tzv. profilu zadavatele. Zde je ukázka [profilu zadavatele Ministerstva vnitra na NEN](https://nen.nipez.cz/profil/MVCR). Každý zadavatel může mít současně libovolný počet profilů zadavatele.

Dle vyhlášky  [168/2016 Sb.](https://www.zakonyprolidi.cz/cs/2016-168) musí každý profil zadavatele poskytovat data v XML, a to pevne dane adrese profilu zadavatele.

Technická specifikace uveřejněných dat z vyhlášky :
> a) Základní vybrané informace budou zveřejňovány na profilu ve strukturované podobě. Podrobný popis XML schémat je uveřejněn na internetových stránkách Informačního systému o veřejných zakázkách.
> b) Základní informace na profilu zadavatele budou k dispozici na internetové adrese `http://(adresa profilu zadavatele)/XMLdataVZ?od=ddmmrrrr&do=ddmmrrrr`
> nebo `https://(adresa profilu zadavatele)/XMLdataVZ?od=ddmmrrrr&do=ddmnmrr`, kde
> 1. „(adresa profilu zadavatele)“ je internetová adresa evidovaná v seznamu Profilu zadavatelů ve Věstníku veřejných zakázek,
> 2. „`/XMLdataVZ?`“ je konstantní řetězec znaků,
> 3. parametry „od=“ a „do=“ specifikují časový úsek, ve kterém jsou data o veřejných zakázkách poskytována, tj. poskytují se informace o všech veřejných zakázek evidovaných na profilu zadavatele, které byly v uvedeném období (včetně dnů od - do) uveřejněny na profilu zadavatele, maximální doba mezi parametry od - do je 366 dnů,
> 4. hodnota „ddmmrrrr“ udává strukturu předávaného formátu pro konkrétní datum, kde „dd“ znamená den, „mm“ měsíc a „rrrr“ rok,
> 5. data jsou zpřístupněna prostřednictví metody GET.

Stahování dat přímo z profilů zadavatelů je poměrně obtížné, musí se dělat po malých časových úsecích a v minimálním množství threadů a zátěže serverů. 

**Toto stahování, často po dohodě s provozovateli, dělá již Hlídač státu a stažené údaje dává k dispozici přes API (s rychlostí a vykonností o několik řádů vyšší). Nestahujte pro potřeby transformace dat XML z profilů, ale využijte výkonné API Hlídače. Díky. ** 

## Co dělá Hlídač a co potřebujeme od vás

Hlídač stahuje všechny zakázky velkého rozsahu z VVZ, a ty zpracovává a analyzuje. Ale nepřidává k nim žádné informace (zejména zadávací dokumentaci) z profilu zadavatele.
Dále Hlídač stahuje data z profilu zadavatelu, mechanicky 1:1 je konvertuje na JSON a ukládá do Elastic serveru.

### S čím potřebujeme pomoc

Jak píšeme výše, v XML z profilu zadavatele je jen pár základních údajů o zakázce. Ostatní údaje je potřeba scrapovat (stahnout a vydolovat) z HTML stránek profilu. V ČR existuje několik desítek provozovatelů profilů zadavatelů. Každý provozovatel (jdou poměrně dobře rozlišit podle domény) profilu má jiný design, strukturu stránek a formátování údajů.  

Nejvíce profilů je na doménách (k 15.6.2018):

Doména | počet profilů
------ | --------------
www.vhodne-uverejneni.cz | 5983
www.e-zakazky.cz | 1605
www.egordion.cz | 1061
www.profilzadavatele.cz | 608
www.stavebnionline.cz | 572
www.tenderarena.cz | 463
nen.nipez.cz | 300
www.kdv.cz | 296
www.profilyzadavatelu.cz | 258
zakazky.krajbezkorupce.cz | 239
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

Cílová datová struktura, do které je potřeba zdrojová data ztransformovat, je popsaná tímto [JSON Schematem](/HlidacStatu/VerejneZakazky/blob/master/Transformace%20dat/VerejneZakazky.schema.json). Schema je dostupné i [v čitelnější podobě](https://hlidacstatu.github.io/VerejneZakazky/VerejneZakazkySchema/index.html).

#### Datová struktura zakázek velkého rozsahu
Struktura je totožná jako cílová datová struktura, je potřeba "pouze doplnit" chybějící údaje z profilu zadavatele. TODO  

#### Datová struktura zakázek malého rozsahu
Zakázky malého rozsahu jsou získány z XML exportu profilu zadavatele. Hlídač Státu toto XML 1:1 bez jakýchkoliv úprav konvertuje do JSON. 
Popis formátu (adekvátně i JSON struktur) s popisem XML a XSD schémat je možno stáhnout přímo z prohlížeče ve formátu zip z internetové stránky http://www.isvz.cz/ProfilyZadavatelu/Profil_Zadavatele_137_2006_SchemaVZ.zip 


### Dostupná API Hlídač Státu

Autorizace je prováděna pomocí autentizačního tokenu, který je Vám přidělen po registraci na HlidacStatu.cz. 
Autentizační token je nutno odesílat v hlavičce každého požadavku na API.
Příklad k použití:
`curl -X GET https://www.hlidacstatu.cz/Api/v1/Detail/204737 -H 'Authorization: Token XYZABCD'`

Autentizační token pro volání API najdete na https://www.hlidacstatu.cz/api. Na stejném místě najdete i přehled všech nabízenách API serveru.

#### Získání nezpracované zakázky malého rozsahu
Zakázky malého rozsahu jsou Hlídačem státu stáhnuty a zkonvertovány do JSON.

1. Získat další zakázku malého rozsahu, která nebyla zpracována, z domény určené parametrem:
  `curl -X GET https://www.hlidacstatu.cz/Api/v1/VZMRList?domena=mfcr.ezak.cz -H 'Authorization: Token XYZABCD'`
  API vrátí pole ID identifikátorů zakázek, které je potřeba zpracovat.

2. Získat samotnou zakázku podle ID
  `curl -X GET https://www.hlidacstatu.cz/Api/v1/VZMRDetail?id=<id zakazky> -H 'Authorization: Token XYZABCD'`
 
Zpracování - transformace zakázky
3. V JSON zakázky je pole `dokument`, které v atributu `url` obsahuje URL na HTML stránku, odkud je možné stáhnout binární soubor (PDF, Word, apod) se zadávací dokumentací. Cílem je získat URL samotného binárního souboru.

4. Převézt zakázku do cílové datové struktury včetně URL na samotné soubory se zadávací dokumentací. Popisné atributy dokumentů je nutné také převezt.

5. Poslat zakázku v cílové datové strukturě na Hlídač státu
  `curl -X POST https://www.hlidacstatu.cz/Api/v1/VZDetail?id=<id zakazky> -H 'Authorization: Token XYZABCD'
       -d '{... json ...}'
  `
6. zpět na 2 pro další zakázku ze seznamu zakázek získaných v 1.

Můžete volat API multithreadově, prosíme však o přiměřenou zátěž.


