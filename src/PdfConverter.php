<?php

use setasign\Fpdi\Fpdi;

class PdfConverter {

	private $cmdLine = array(
		"Linux" => "env HOME=/tmp soffice",
		"WINNT" => "\"C:/Program Files/LibreOffice 5/program/soffice\""
	);

	/**
	 * Undocumented function
	 *
	 * @param string $fileName
	 * @param boolean $out
	 * @return string|void
	 */
	function convert($fileName) {
		$tmpDir = sys_get_temp_dir();
		$tmpFileName = $tmpDir."/".(new SplFileInfo($fileName))->getBasename(".docx").".pdf";

		error_log(shell_exec($this->cmdLine[PHP_OS]." --headless --convert-to pdf --outdir $tmpDir $fileName"));

		$f = new SplFileObject($tmpFileName);
		while (!$f->eof()) {
			echo $f->fread(8192);
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param array $files
	 * @return string|void
	 */
	function mergePdfs(array $files) {
		$pdf = new Fpdi();
		foreach ($files as $file) {
			$pageCount = $pdf->setSourceFile($file);
			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
				$tplidx = $pdf->importPage($pageNo);
				$pdf->AddPage();
				$pdf->useTemplate($tplidx);
			}
		}
		$pdf->Output("I");
	}
}