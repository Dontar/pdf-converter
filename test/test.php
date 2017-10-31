<?php

class CurlWrapper {
	public $context;

	private $curl;

	private $tempStream;

	public function dir_closedir() {return false;}
	public function dir_opendir($path, $options) {return false;}
	public function dir_readdir() {return false;}
	public function dir_rewinddir() {return false;}
	public function mkdir($path, $mode, $options) {return false;}
	public function rename($path_from, $path_to) {return false;}
	public function rmdir($path, $options) {return false;}
	public function stream_cast($cast_as) {return false;}
	public function stream_close() {
		curl_close($this->curl);
		fclose($this->tempStream);
	}
	public function stream_eof() {
		return feof($this->tempStream);
	}
	public function stream_flush() {}
	public function stream_lock($operation) {}
	public function stream_metadata($path, $option, $value) {return false;}
	public function stream_open($path, $mode, $options, &$opened_path) {
		$this->curl = curl_init($path);
		if ($this->curl !== false) {
			$this->tempStream = $strm = fopen("php://temp", "rw");
			$streamOpts = stream_context_get_options($this->context)['http'];
			$opts = array(
				CURLOPT_FORBID_REUSE => true,
				CURLOPT_FRESH_CONNECT => true,
				CURLOPT_SAFE_UPLOAD => true,
				CURLOPT_CUSTOMREQUEST => $streamOpts['method'],
				CURLOPT_WRITEFUNCTION => function ($ch, $data) use ($strm) {
					return fwrite($strm, $data);
				}
			);

			if (isset($streamOpts['header'])) {
				$opts[CURLOPT_HTTPHEADER] = $streamOpts['header'];
			}
			if (isset($streamOpts['data'])) {
				$opts[CURLOPT_POSTFIELDS] = $streamOpts['data'];
			}

			curl_setopt_array($this->curl, $opts);
			curl_exec($this->curl);

			rewind($this->tempStream);
			$opened_path = $path;
			return true;
		}
		return $this->curl;
	}
	public function stream_read($count) {
		return fread($this->tempStream, $count);
	}
	public function stream_seek($offset, $whence = SEEK_SET) {
		return (bool)fseek($this->tempStream, $offset, $whence);
	}
	public function stream_set_option($option, $arg1, $arg2) {}
	public function stream_stat() {return false;}
	public function stream_tell() {return ftell($this->tempStream);}
	public function stream_truncate($new_size) {return false;}
	public function stream_write($data) {return false;}
	public function unlink($path) {return false;}
	public function url_stat($path, $flags) {return false;}

}
stream_wrapper_unregister("http");
stream_wrapper_register("http", "CurlWrapper", STREAM_IS_URL);

class Test {

	function testPdfMerge() {
		// $url = "http://160.44.207.102:3000/pdf";
		$url = "http://localhost:3000/pdf";
		$streamContext = stream_context_create(array("http" => array(
			"method" => "POST",
			"data" => array(
				"file1" => new CURLFile(__DIR__."/h_agreement.docx"),
				"file2" => new CURLFile(__DIR__."/contract.docx")
			)
		)));
		
		$inStream = fopen($url, "r", null, $streamContext);
		$outStream = fopen(__DIR__."/doc_multi.pdf", "w");
		stream_copy_to_stream($inStream, $outStream);
		fclose($outStream);
		fclose($inStream);
	}
	
	function testConvertToPdf() {
		// $url = "http://160.44.207.102:3000/pdf";
		$url = "http://localhost:3000/pdf";
		$streamContext = stream_context_create(array("http" => array(
			"method" => "POST",
			"header" => array("Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document"),
			"data" => file_get_contents(__DIR__."/h_agreement.docx")
		)));
		
		$inStream = fopen($url, "r", null, $streamContext);
		$outStream = fopen(__DIR__."/doc.pdf", "w");
		stream_copy_to_stream($inStream, $outStream);
		fclose($outStream);
		fclose($inStream);
	}

}

$test = new Test();

$test->testPdfMerge();
$test->testConvertToPdf();
