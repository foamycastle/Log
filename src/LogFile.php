<?php

namespace FoamyCastle\Log;

use DateTime;
use DateTimeZone;
use SplFileObject;
use LogicException;
use RuntimeException;

class LogFile extends Log  {
	const DEFAULT_LOG_FILENAME = "change_default_log_filename.log";
	const DEFAULT_LOG_PATH = "logs/";
	private string $path;
	private SplFileObject $logFile;

	public function __construct(?string $path=null,?string $fileName=null,array $options=[]) {
		if(!$this->setPath($path ?? self::DEFAULT_LOG_PATH)){
			$this->setPath(__DIR__.DIRECTORY_SEPARATOR."logs");
		}
		if(!$this->setFile($fileName ?? self::DEFAULT_LOG_FILENAME)){
			$this->setFile("logFile.log");
		}

		$this->setTime();
		$this->setOptions($options);
		$this->readyState=true;
	}
	function write(string $message):bool{
		if(!str_ends_with($message,PHP_EOL)){
			$message.=PHP_EOL;
		}
		if($this->readyState){
			return $this->logFile->fwrite($message)==strlen($message);
		}
		return false;
	}
	public function getRealPath(): string {
		return $this->logFile->getRealPath() ?? "";
	}
	function getPath(): string {
		return $this->logFile->getPath() ?? "";
	}
	public function getFilename(): string {
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
			$this->logFile = new SplFileObject($this->joinPathAndFile($this->path, $fileName), 'a');
			return true;
		}catch(LogicException){
			try{
				$this->logFile = @new SplFileObject($this->joinPathAndFile($this->path . $fileName, $defaultFileName), 'a');
			}catch (RuntimeException) {
				return false;
			}
		}catch (RuntimeException){
			return false;
		}
		return false;
	}
	public function setTimezone($timezone=null):LoggingInstance{
		if($timezone instanceof DateTimeZone) {
			$tz=$timezone;
		}
		if(is_string($timezone)){
			if (!in_array($timezone, timezone_identifiers_list())) return $this;
			$tz = new DateTimeZone($timezone);
		}
		if($timezone===null){
			$tz=new DateTimeZone("UTC");
		}
		if(!isset($tz)) return $this;
		$this->timestamp->setTimezone($tz);
		return $this;
	}
	public function setTimeFormat(string $format):LoggingInstance {
		$this->timeFormat = $format;
		return $this;
	}
	public function setOptions(array $options):LoggingInstance{
		foreach ($options as $option=>$value) {
			switch (strtolower($option)){
				case 'timezone':$this->setTimezone($value);break;
				case 'time_format': $this->setTimeFormat($value);break;
			}
		}
		return $this;
	}
	private function setTime():void{
		$this->timestamp=new DateTime('now');
	}
	public function getTimestamp():string{
		return $this->timestamp->setTimestamp(time())->format($this->timeFormat ?? DATE_RFC3339);
	}
}