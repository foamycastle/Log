<?php

namespace FoamyCastle\Log;
include "LogLevel.php";
include "LogFileInterface.php";

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use FoamyCastle\Log\LogLevel;
use FoamyCastle\Log\LogFileInterface;

class Log extends AbstractLogger {

	private LogFileInterface $logFile;
	private LogLevel $defaultLevel;
	private \DateTime $timestamp;
	private string $timeFormat;

	/**
	 * @param  \FoamyCastle\Log\LogFileInterface  $file
	 * @param  array{timezone:string,event_mask:int,time_format:string} $options
	 */
	public function __construct(LogFileInterface $file,array $options=[]) {
		$this->setLogFile($file);
		$this->setDefaultLogLevel(LogLevel::DEBUG);
		$this->setTime();
		$this->setOptions($options);
	}

	/**
	 * @param  string  $message
	 * @param  array   $context
	 *
	 * @return void
	 */
	public function __invoke(string $message,array $context=[]):Log{
		$this->log($this->defaultLevel,$message,$context);
		return $this;
	}
	public function setTimezone($timezone=null):Log{
		if($timezone instanceof \DateTimeZone) {
			$tz=$timezone;
		}
		if(is_string($timezone)){
			if (!in_array($timezone, timezone_identifiers_list())) return $this;
			$tz = new \DateTimeZone($timezone);
		}
		if($timezone===null){
			$tz=new \DateTimeZone("UTC");
		}
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
	private function setTime():void{
		$this->timestamp=new \DateTime('now');
	}
	/**
	 * Replaces variables in the `$message` string with values in the `$symbolTable`
	 * @param  string  $message A string containing readable text and variables in the format of @{variable_name}
	 * @param  array   $symbolTable A hash of variables and their values
	 *
	 * @return string A message string with all the context variable replaced with data
	 */
	private function replaceContextSymbols(string $message, array $symbolTable):string{
		$regexSymbols=array_map(fn($element):string=>"/(@\{$element\})/",array_keys($symbolTable));
		$valueStrings=array_map(fn($element):string=>$this->stringify($element),array_values($symbolTable));
		unset($symbolTable);
		return preg_replace($regexSymbols,$valueStrings,$message);
	}
	private function stringify($object):string{
		if(is_string($object)) return $object;
		if(is_numeric($object)) return (string)$object;
		if(is_null($object)) return "NULL";
		if(is_bool($object)) return $object ? "TRUE":"FALSE";
		if(is_object($object)||is_array($object)){
			return print_r($object,true);
		}
		return "(no string representation available)";
	}
	public function getTimestamp():string{
		return $this->timestamp->setTimestamp(time())->format($this->timeFormat ?? DATE_RFC3339);
	}

	public function emergency(\Stringable|string $message, array $context = []): void {
		$this->log(LogLevel::EMERGENCY,$message,$context);
	}

	public function alert(\Stringable|string $message, array $context = []): void {
		$this->log(LogLevel::ALERT,$message,$context);
	}

	public function critical(\Stringable|string $message, array $context = []): void {
		$this->log(LogLevel::CRITICAL,$message,$context);
	}

	public function error(\Stringable|string $message, array $context = []): void {
		$this->log(LogLevel::ERROR,$message,$context);
	}

	public function warning(\Stringable|string $message, array $context = []): void {
		$this->log(LogLevel::WARNING,$message,$context);
	}

	public function notice(\Stringable|string $message, array $context = []): void {
		$this->log(LogLevel::NOTICE,$message,$context);
	}

	public function info(\Stringable|string $message, array $context = []): void {
		$this->log(LogLevel::INFO,$message,$context);
	}

	public function debug(\Stringable|string $message, array $context = []): void {
		$this->log(LogLevel::DEBUG,$message,$context);
	}

	/**
	 * @param  LogLevel            $level
	 * @param  \Stringable|string  $message
	 * @param  array               $context
	 *
	 * @return void
	 */
	public function log($level, \Stringable|string $message, array $context = []): void {
		if($context){
			$message=$this->replaceContextSymbols($message,$context);
		}
		$this->logFile->write($level->name.": ".$this->getTimestamp()." $message");
	}
}