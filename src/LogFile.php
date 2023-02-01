<?php

namespace FoamyCastle\Log;

use FoamyCastle\Log\LogFileInterface;

class LogFile implements LogFileInterface {
	const DEFAULT_LOG_FILENAME = "standard_error_.log";
	const DEFAULT_LOG_PATH = "logs/";
	private string $path;
	private \SplFileObject $logFile;
	public bool $readyState = false;

	public function __construct(?string $path=null,?string $fileName=null) {
		if(!$this->setPath($path ?? self::DEFAULT_LOG_PATH)){
			$this->setPath(__DIR__.DIRECTORY_SEPARATOR."logs");
		}
		if(!$this->setFile($fileName ?? self::DEFAULT_LOG_FILENAME)){
			$this->setFile("logFile.log");
		}
		$this->readyState=true;
	}
	public function write(string $data):bool{
		if(!str_ends_with($data,PHP_EOL)){
			$data.=PHP_EOL;
		}
		if($this->readyState){
			return $this->logFile->fwrite($data)==strlen($data);
		}
		return false;
	}
	public function getRealPath(): string {
		return $this->logFile->getRealPath() ?? "";
	}

	function getPath(): string {
		return $this->logFile->getPath() ?? "";
	}

	function getFilename(): string {
		return $this->logFile->getFilename() ?? "";
	}

	private function pathExplode(string $path):array{
		return explode("/",$path);
	}
	private function validatePath(string $path):string|false{
		$pathElements=$this->pathExplode($path);
		$builtString=DIRECTORY_SEPARATOR;
		while(count($pathElements)>0){
			$builtString.=array_shift($pathElements);
			if(!is_dir($builtString)){
				if(!@mkdir($builtString)){
					return false;
				}
			}
			$builtString.=str_ends_with($builtString,DIRECTORY_SEPARATOR)?"":DIRECTORY_SEPARATOR;
		}
		return $builtString;
	}
	private function joinPathAndFile(string $path,string $file):string{
		$path .= str_ends_with($path,DIRECTORY_SEPARATOR)
			? ""
			: DIRECTORY_SEPARATOR;
		$file = str_starts_with($file,DIRECTORY_SEPARATOR)
			? substr($file,1)
			: $file;
		return $path.$file;
	}
	private function setPath(string $path):bool{
		$valid=$this->validatePath($path);
		if(!$valid) return false;
		$this->path=$valid;
		return true;
	}
	private function setFile(string $fileName):bool{
		$defaultFileName = self::DEFAULT_LOG_FILENAME != ""
			? self::DEFAULT_LOG_FILENAME
			: "logFile.log";
		$fileName = $fileName!=""
			? $fileName
			: $defaultFileName;
		try{
			$this->logFile = new \SplFileObject($this->joinPathAndFile($this->path, $fileName), 'a');
			return true;
		}catch(\LogicException){
			try{
				$this->logFile = @new \SplFileObject($this->joinPathAndFile($this->path . $fileName, $defaultFileName), 'a');
			}catch (\RuntimeException) {
				return false;
			}
		}catch (\RuntimeException){
			return false;
		}
		return false;
	}
}