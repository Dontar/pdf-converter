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
	function convertFile($fileName) {
		$tmpDir = sys_get_temp_dir();
		$tmpFileName = $tmpDir."/".(new SplFileInfo($fileName))->getBasename(".docx").".pdf";

		error_log(shell_exec($this->cmdLine[PHP_OS]." --headless --convert-to pdf --outdir $tmpDir $fileName"));

		return $tmpFileName;
	}

	function convertStream($stream) {
		file_put_contents($fName = tempnam(sys_get_temp_dir(), "word_file").".docx", $stream);
		if ($success !== false) {
			$resultFile = $this->convertFile($fName);
			stream_copy_to_stream($t = fopen($resultFile, "r"), $r = fopen("php://temp", "rw"));
			fclose($t);
			unlink($resultFile);
			rewind($r);
			return $r;
		}
		return null;
	}

	/**
	 * Undocumented function
	 *
	 * @param Traversable $files
	 * @return string|void
	 */
	function mergePdfs($files) {
		$pdf = new Fpdi();
		foreach ($files as $file) {
			$pageCount = $pdf->setSourceFile($file);
			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
				$tplidx = $pdf->importPage($pageNo);
				$pdf->AddPage();
				$pdf->useTemplate($tplidx);
			}
		}
		$f = fopen("php://temp", "rw");
		fwrite($f, $pdf->Output("S"));
		rewind($f);
		return $f;
	}
}