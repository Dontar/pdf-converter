<?php

class PdfConverter {

	// function __construct() {
	// 	$tmp = `unoconv popo`;
	// }

	function convert($fileName, $out = false) {
		$tmpDir = sys_get_temp_dir();
		$tmpFileName = $tmpDir."/".(new SplFileInfo($fileName))->getBasename(".docx").".pdf";
		error_log(shell_exec("env HOME=/tmp soffice --headless --convert-to pdf --outdir $tmpDir $fileName"));

		if ($out) {
			$f = new SplFileObject($tmpFileName);
			while (!$f->eof()) {
				echo $f->fread(8192);
			}
			unlink($tmpFileName);
		} else {
			return $tmpFileName;
		}
	}

}