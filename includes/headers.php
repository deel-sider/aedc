<?php

$hfile = fopen("/var/www/htdocs/carryover/headers.txt", "a") or die("Unable to open file!");

fwrite($hfile, "DateTime: " . date("Y-m-d") . "T" . date("H:i:s") . "\n");
fwrite($hfile, "Address: " . $_SERVER['REQUEST_URI'] . "\n");

$headers = apache_request_headers();

foreach ($headers as $header => $value) {
    fwrite($hfile, "$header: $value\n");
}

fclose($hfile);

?>
