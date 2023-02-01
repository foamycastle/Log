<?php

namespace FoamyCastle\Log\Exception;

class CannotCreatePathException extends \Exception {
	public function __construct(string $method,string $file,int $line) {
		parent::__construct("$file $method line:$line Cannot create new path for log file");
	}
}