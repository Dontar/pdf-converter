<?php

class Test {

	function testConvertToPdf() {
		$url = "http://localhost:3000/pdf";
		// $url = "http://localhost:3000/pdf";
		$streamContext = stream_context_create(array("http" => array(
			"method" => "POST",
			"header" => "Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			"content" => file_get_contents(__DIR__."/h_agreement.docx")
		)));
		
		$inStream = fopen($url, "r", null, $streamContext);
		$outStream = fopen(__DIR__."/doc.pdf", "w");
		stream_copy_to_stream($inStream, $outStream);
		fclose($outStream);
		fclose($inStream);
	}

	function testPdfMerge() {

	}

	function testForm() {
		$pipi = array(
			"var1" => "popo",
			"var2" => fopen(__DIR__."/h_agreement.docx", "r")
		);
		// var_export(http_)
	}

}

$test = new Test();

$test->testConvertToPdf();

