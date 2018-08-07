# Veřejné zakázky na Hlídači státu

Stát uveřejňuje veřejné zakázky v XML souborech ([ukázka](https://zakazky.krajbezkorupce.cz/profile_display_263.html/XMLdataVZ?od=01022018&do=01032018)). Ty jsou k dispozici na mnoha různých serverech ([seznam](transformace-dat#s-čím-potřebujeme-pomoc)) a každý server je poskytuje trochu jinak. Konkrétně jde např. o URL binárních dokumentů (např. PDF), které zakázku popisují. Některé servery odkazují přímo adresu tohoto binárního dokumentu ([ukázka](https://nen.nipez.cz/profil/MVCR/XMLdataVZ?od=12072017&do=12072017)), ale jiné odkazují jen HTML stránku, na které je teprve odkaz na binární dokument. My bychom potřebovali získat URL těchto binárních dokumentů. Některé informace navíc v XML exportu vůbec nejsou, např. taková drobnost jako předpokládaná hodnota zakázky. Na HTML stránce ale obvykle jsou ([ukázka](https://zakazky.krajbezkorupce.cz/contract_display_13335.html)).

**Vaším úkolem je napsat [scraper](https://en.wikipedia.org/wiki/Data_scraping), který z HTML stránky získá chybějící informace.**

Scraper může být velmi jednoduchý, lze se inspirovat u [těch stávajících](transformace-dat/src/handlers). V ideálním případě by kód měl přežít i drobné změny stránky, pokud ale dojde např. ke kompletnímu redesignu serveru, tak se kód celkem pochopitelně nejspíš rozbije a bude ho potřeba opravit. To už je úděl scrapingu. Z toho důvodu je obzvlášť vhodné psát i testy, které nefunkčnost scraperu odhalí.

Napsání scraperu pro daný server je hlavní část vaší práce. Kromě toho je potřeba získat seznam veřejných zakázek na daném serveru (poskytujeme API), pak konkrétní zakázku s informacemi z XML souboru (poskytujeme API) a následně uložit získané informace (poskytujeme API). To je v zásadě nezávislé na konkrétním serveru. [Postup](transformace-dat#získání-nezpracované-zakázky-malého-rozsahu)

[Podrobná dokumentace](transformace-dat) 
[Otázky? Slack channel!](https://join.slack.com/t/hlidacstatu/shared_invite/enQtNDEyNjgxNzM3NTQxLTZiMzVmZmE4ZGQ0NzdiZDY2MTk3ZjA0OWI2ZmI3NGNjMjRmZjlhZDg0ODc1YmUzMTM0ZDVjYTQ4MmIxOGFiYWM)
