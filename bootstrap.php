<?php

require_once "vendor/autoload.php";

$router = new SimpleRouter();

$router->on("/pdf", function($method, $params, $headers) {
	$pdf = new PdfConverter();
	
	$traverseFiles = function() use ($pdf) {
		foreach ($_FILES as $value) {
			yield $pdf->convertFile($value['tmp_name']);
		}
	};
	$tmpStream = $pdf->mergePdfs($traverseFiles());
	return array(
		"headers" => array(
			"Content-Type: application/pdf",
			"Content-Disposition: inline; filename=pdf_document.pdf",
			'Cache-Control: private, max-age=0, must-revalidate',
			'Pragma: public'
		),
		"data" => $tmpStream
	);
});

$router->dispatch();
