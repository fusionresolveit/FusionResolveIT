<?php

$xml = file_get_contents(__DIR__ . '/fedora-2025-01-15-16-47-11.ocs');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1/api/v1/fusioninventory');

// For xml, change the content-type.
curl_setopt ($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/xml"));

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // ask for results to be returned

// Send to remote and return data to caller.
$result = curl_exec($ch);
curl_close($ch);

// print_r($result);
