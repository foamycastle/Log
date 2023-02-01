<?php

namespace FoamyCastle\Log;

interface LogFileInterface {
	function write(string $data):bool;
	function getRealPath():string;
	function getPath():string;
	function getFilename():string;
}