#!/usr/bin/env php
<?php
foreach (rglob('*_test.php', 'src/') as $filename) {
	include __DIR__ . "/$filename";
}

foreach (get_defined_functions()['user'] as $function) {
	if (preg_match('~^test~', $function)) {
		echo ".";
		$function();
	}
}
echo "\n";

function rglob($pattern, $dir = '') {
  foreach (glob($dir . $pattern) as $filename) {
    yield $filename;
  }
  foreach (glob("$dir*", GLOB_ONLYDIR) as $subdir) {
    foreach (rglob($pattern, "$subdir/") as $filename) {
      yield $filename;
    }
  }
}
