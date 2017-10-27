<?php

require_once "vendor/autoload.php";

$inputStream = fopen("php://input", "r");
$tmpStream = fopen($tmpFileName = tempnam(sys_get_temp_dir(), "tmp_word").".docx", "w");
$success = stream_copy_to_stream($inputStream, $tmpStream);
fclose($tmpStream);
if (false !== $success) {
	$pdf = new PdfConverter();
	header("Content-Type: application/pdf");
	header("Content-Disposition: inline; filename=pdf_document.pdf");
	header('Cache-Control: private, max-age=0, must-revalidate');
	header('Pragma: public');
	$pdf->convert($tmpFileName, true);
}
unlink($tmpFileName);