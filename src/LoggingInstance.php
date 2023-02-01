<?php

namespace FoamyCastle\Log;

interface LoggingInstance {
	function getTimestamp():string;
	function setOptions(array $options):LoggingInstance;
	function setTimeFormat(string $format):LoggingInstance;
	function setTimezone($timezone=null):LoggingInstance;
}