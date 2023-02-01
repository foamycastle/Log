<?php

namespace FoamyCastle\Log;

use Stringable;
use Closure;

class Log extends AbstractLogger {

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

	public function emergency(Stringable|string|Closure $message, array $context = []): void {
		$this->log(LogLevel::EMERGENCY,$message,$context);
	}
	public function alert(Stringable|string|Closure $message, array $context = []): void {
		$this->log(LogLevel::ALERT,$message,$context);
	}
	public function critical(Stringable|string|Closure $message, array $context = []): void {
		$this->log(LogLevel::CRITICAL,$message,$context);
	}
	public function error(Stringable|string|Closure $message, array $context = []): void {
		$this->log(LogLevel::ERROR,$message,$context);
	}
	public function warning(Stringable|string|Closure $message, array $context = []): void {
		$this->log(LogLevel::WARNING,$message,$context);
	}
	public function notice(Stringable|string|Closure $message, array $context = []): void {
		$this->log(LogLevel::NOTICE,$message,$context);
	}
	public function info(Stringable|string|Closure $message, array $context = []): void {
		$this->log(LogLevel::INFO,$message,$context);
	}
	public function debug(Stringable|string|Closure $message, array $context = []): void {
		$this->log(LogLevel::DEBUG,$message,$context);
	}

	public function log($level, Stringable|string|Closure $message, array $context = []): void {
		$message=$this->stringify($message);
		if($context){
			$message=$this->replaceContextSymbols($message,$context);
		}
		$this->logFile->write($level->name.": ".$this->getTimestamp()." $message");
	}
}