<?php
include_once __DIR__ . '/../util.php';

function zakazkyLesyCr(stdClass $result, stdClass $profile)
{
    if (!$result->Dokumenty) {
        return;
    }

    foreach ($result->Dokumenty as &$dokument) {
        $dom = downloadHtml($dokument['OficialUrl']);
        $url = (new DOMXPath($dom))->evaluate('string(//*[@id="document_download"]//a/@href)');
        $dokument['DirectUrl'] = $url;
    }
    unset($dokument);
}
