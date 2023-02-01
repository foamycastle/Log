<?php

namespace FoamyCastle\Log;

interface LogFileInterface {
	function write(string $data):bool;
	function getRealPath():string;
	function getPath():string;
	function getFilename():string;
	function getTimestamp():string;
	function setTimezone($timezone=null):LogFileInterface;
	function setTimeFormat(string $format):LogFileInterface;
	function setOptions(array $options):LogFileInterface;
}