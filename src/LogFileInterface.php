<?php

namespace FoamyCastle\Log;

interface LogFileInterface {
	function getFilename():string;
	function getPath():string;
	function getRealPath():string;
	function getTimestamp():string;
	function setOptions(array $options):LogFileInterface;
	function setTimeFormat(string $format):LogFileInterface;
	function setTimezone($timezone=null):LogFileInterface;
	function write(string $data):bool;
}