# Skripty pro stahování a transformaci veřejných zakázek

- `run.php` - stahuje jednotlivé veřejné zakázky a transformuje je. Lze spustit s parametrem (např. `VVZ-2016-646720`), pak bude stahovat jen zakázky z daného profilu. Všechny stažené stránky také ukládá do adresáře `../cache/`. Vyžaduje zapnutou direktivu `allow_url_fopen` a extenze `dom` a `simplexml`.
- `run_tests.php` - spustí všechny testy.
- `authToken.txt` - do tohoto souboru umístěte token získaný na https://www.hlidacstatu.cz/api/.
