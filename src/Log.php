<?php

namespace FoamyCastle\Log;

use Stringable;
use Closure;

abstract class Log implements LogFileInterface {
	public static function Emergency(LogFileInterface $logFile, Stringable|string|Closure $message, array $context = []): void {
		self::log($logFile,LogLevel::EMERGENCY,$message,$context);
	}
	public static function Alert(LogFileInterface $logFile,Stringable|string|Closure $message, array $context = []): void {
		self::log($logFile,LogLevel::ALERT,$message,$context);
	}
	public static function Critical(LogFileInterface $logFile, Stringable|string|Closure $message, array $context = []): void {
		self::log($logFile,LogLevel::CRITICAL,$message,$context);
	}
	public static function Error(LogFileInterface $logFile, Stringable|string|Closure $message, array $context = []): void {
		self::log($logFile,LogLevel::ERROR,$message,$context);
	}
	public static function Warning(LogFileInterface $logFile, Stringable|string|Closure $message, array $context = []): void {
		self::log($logFile,LogLevel::WARNING,$message,$context);
	}
	public static function Notice(LogFileInterface $logFile, Stringable|string|Closure $message, array $context = []): void {
		self::log($logFile,LogLevel::NOTICE,$message,$context);
	}
	public static function Info(LogFileInterface $logFile, Stringable|string|Closure $message, array $context = []): void {
		self::log($logFile,LogLevel::INFO,$message,$context);
	}
	public static function Debug(LogFileInterface $logFile, Stringable|string|Closure $message, array $context = []): void {
		self::log($logFile,LogLevel::DEBUG,$message,$context);
	}
	private static function log(LogFileInterface $logFile, LogLevel $level, Stringable|string|Closure $message, array $context = []): void {
		$message=self::stringify($message);
		if($context){
			$message=self::replaceContextSymbols($message,$context);
		}
		$logFile->write($level->name.": ".$logFile->getTimestamp()." $message");
	}
	private static function replaceContextSymbols(string $message, array $symbolTable):string{
		/*
		 * $symbolTable is a key=>value array where the "key" is the symbol in the message @{symbol}
		 * and the "value" is the data (string, object, closure, etc.) that will replace the symbol.
		 * The self::stringify() method provides the data conversion to string.
		 */
		//convert all symbols in the message to regex expressions
		$regexSymbols=array_map(fn($element):string=>"/(@\{$element\})/",array_keys($symbolTable));
		//convert all symbol data to a string
		$valueStrings=array_map(fn($element):string=>self::stringify($element),array_values($symbolTable));
		//ditch the original symbol table. not necessary but let's be tidy.
		unset($symbolTable);
		//replace all regex symbols with the converted strings
		return preg_replace($regexSymbols,$valueStrings,$message);
	}
	private static function stringify($object):string{
		//an array may be a Closure()=>[args]
		//A closure MUST return a primitive type lest it be var_dump'd by a call to print_r
		if(is_array($object)){
			if($object[0] instanceof Closure){
				if(isset($object[1])) {
					$object = $object[0]->call($object[0], $object[1]);
				}else {
					$object = $object[0]->call($object[0]);
				}
			//an array may also be a callable with [args]
			}elseif(is_callable($object[0])){
				if(isset($object[1])) {
					$object = call_user_func($object[0],$object[1]);
				}else {
					$object = call_user_func($object[0]);
				}
			}
		}
		//if it's just a plain Closure with no args
		if($object instanceof Closure){
			$object = $object->call($object);
		}
		//now check for primitive types
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