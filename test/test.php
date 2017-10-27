<?php

$streamContext = stream_context_create(array("http" => array(
	"method" => "POST",
	"header" => "Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document",
	"content" => file_get_contents(__DIR__."/h_agreement.docx")
)));

$inStream = fopen("http://localhost:3000", "r", null, $streamContext);
$outStream = fopen(__DIR__."/doc.pdf", "w");
stream_copy_to_stream($inStream, $outStream);
fclose($outStream);
fclose($inStream);
