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
