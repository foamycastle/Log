<?php

namespace FoamyCastle\Log;
use Closure;
use Stringable;
use DateTimeZone;
use DateTime;
/**
 * This is a simple Logger implementation that other Loggers can inherit from.
 *
 * It simply delegates all log-level-specific methods to the `log` method to
 * reduce boilerplate code that a simple Logger that does the same thing with
 * messages regardless of the error level has to implement.
 */
abstract class AbstractLogger implements LoggerInterface
{
	protected LogFileInterface $logFile;
	protected LogLevel $defaultLevel;
	protected DateTime $timestamp;
	protected string $timeFormat;
	public function __invoke(string|Stringable|Closure $message, array $context=[]):Log{
		$this->log($this->defaultLevel,$message,$context);
		return $this;
	}
	public function setTimezone($timezone=null):Log{
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
	public function setTimeFormat(string $format):Log{
		$this->timeFormat=$format;
		return $this;
	}
	public function setDefaultLogLevel(LogLevel $level):Log{
		$this->defaultLevel=$level;
		return $this;
	}
	public function setLogFile(LogFile $file):Log{
		if($file->readyState){
			$this->logFile=$file;
		}
		return $this;
	}
	public function setOptions(array $options):Log{
		foreach ($options as $option=>$value) {
			switch (strtolower($option)){
				case 'timezone': $this->timestamp->setTimezone($value);break;
				case 'time_format': $this->timeFormat=$value;break;
				case 'default_level': $this->setDefaultLogLevel($value);
			}
		}
		return $this;
	}
	protected function setTime():void{
		$this->timestamp=new DateTime('now');
	}
	public function getTimestamp():string{
		return $this->timestamp->setTimestamp(time())->format($this->timeFormat ?? DATE_RFC3339);
	}
	/**
	 * Replaces variables in the `$message` string with values in the `$symbolTable`
	 * @param  string  $message A string containing readable text and variables in the format of @{variable_name}
	 * @param  array   $symbolTable A hash of variables and their values
	 *
	 * @return string A message string with all the context variable replaced with data
	 */
	protected function replaceContextSymbols(string $message, array $symbolTable):string{
		$regexSymbols=array_map(fn($element):string=>"/(@\{$element\})/",array_keys($symbolTable));
		$valueStrings=array_map(fn($element):string=>$this->stringify($element),array_values($symbolTable));
		unset($symbolTable);
		return preg_replace($regexSymbols,$valueStrings,$message);
	}
	protected function stringify($object):string{
		if(is_array($object)){
			if($object[0] instanceof Closure){
				if(isset($object[1])) {
					$object = $object[0]->call($this, $object[1]);
				}else {
					$object = $object[0]->call($this);
				}

			}elseif(is_callable($object[0])){
				if(isset($object[1])) {
					$object = call_user_func($object[0],$object[1]);
				}else {
					$object = call_user_func($object[0]);
				}
			}
		}
		if($object instanceof Closure){
			$object = $object->call($this,$object);
		}
		if(is_string($object)) return $object;
		if(is_numeric($object)) return (string)$object;
		if(is_null($object)) return "NULL";
		if(is_bool($object)) return $object ? "TRUE":"FALSE";
		if(is_object($object)||is_array($object)){
			return print_r($object,true);
		}
		return "(no string representation available)";
	}


}
