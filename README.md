# Veřejné zakázky na Hlídači státu

Stát uveřejňuje veřejné zakázky v XML souborech ([ukázka](https://zakazky.krajbezkorupce.cz/profile_display_263.html/XMLdataVZ?od=01022018&do=01032018)). Ty jsou k dispozici na mnoha různých serverech ([seznam](Transformace%20dat/#s-čím-potřebujeme-pomoc)) a každý server je poskytuje trochu jinak. Konkrétně jde o URL binárních dokumentů (např. PDF), které zakázku popisují. Některé servery odkazují přímo adresu tohoto binárního dokumentu ([ukázka](https://nen.nipez.cz/profil/MVCR/XMLdataVZ?od=12072017&do=12072017)), ale jiné odkazují jen HTML stránku, na které je teprve odkaz na binární dokument. My bychom potřebovali získat URL těchto binárních dokumentů.

Vaším úkolem je napsat [scraper](https://en.wikipedia.org/wiki/Data_scraping), který z HTML stránky odkazující binární dokument ci dokumenty získá adresu těchto dokumentů. Scraper může být velmi jednoduchý, např. na serveru https://zakazky.krajbezkorupce.cz je odkaz uveden v řádku tabulky, jejíž nadpis je "Jméno souboru:". Pomocí [XPath](http://www.kosek.cz/xml/xslt/vyrazy.html#d5e780) se můžeme přímo na tu správnou značku `<a>` odkázat pomocí `//tr[th='Jméno souboru:']//a`, což znamená: kdekoliv v dokumentu (`//`) vezmi `<tr>` (`//tr`), jehož potomek `<th>` má danou hodnotu (`//tr[th='...']`) a v tomto řádku vezmi libovolně vnořený `<a>`. Z odkazu už jen vezmeme hodnotu atributu `href`, uděláme z něj absolutní odkaz a je hotovo. Kód v PHP:

```php
$url = 'https://zakazky.krajbezkorupce.cz/document_download_66097.html';
$dom = new DOMDocument;
$dom->loadHTMLFile($url);
$a = (new DOMXPath($dom))->evaluate("//tr[th='Jméno souboru:']//a")->item(0);
return absoluteUrl($a->getAttribute('href'), $url);
```

Jde tedy hlavně o to vytvořit co nejlepší výraz XPath (případně obecnější kód), který bude počítat s různými podobami stránky na konkrétním serveru. Např. není moc vhodné se odkazovat na element podle pořadí, protože když server třeba u některých dokumentů zobrazí volitelné pole navíc, tak výraz nebude fungovat. Ze stejného důvodu není vhodné používat ani XPath výrazy, které [dovoluje zkopírovat](https://stackoverflow.com/a/42194160/783580) Chrome Developer Tools, ale je možné je vzít jako základ. V ideálním případě by kód měl přežít i drobné změny stránky, pokud ale dojde např. ke kompletnímu redesignu serveru, tak se kód celkem pochopitelně nejspíš rozbije a bude ho potřeba opravit. To už je úděl scrapingu.

Napsání scraperu pro daný server je hlavní část vaší práce. Kromě toho je potřeba získat seznam veřejných zakázek na daném serveru (poskytujeme API), pak konkrétní zakázku s informacemi z XML souboru (poskytujeme API) a následně uložit získané informace (poskytujeme API). To je v zásadě nezávislé na konkrétním serveru. [Postup](Transformace%20dat/#získání-nezpracované-zakázky-malého-rozsahu)

V některých případech ani není potřeba scraper psát. Jak už jsme popsali výše, některé servery se odkazují přímo na binární dokument. Pak stačí toto URL uložit jako adresu binárního dokumentu. U některých serverů také může jít adresa binárního dokumentu odvodit z adresy HTML stránky (např. si všimnete, že ze stránky `123.html` vede odkaz vždycky na `download/123`), pak HTML stránky není ani potřeba stahovat. Dejte ale pozor, že binární dokumenty můžou mít různé formáty, takže pokud např. odkaz vede na `download/123.pdf`, tak se bez scrapování asi neobejdete.

[Podrobná dokumentace](https://github.com/HlidacStatu/verejne-zakazky/tree/master/Transformace%20dat)
