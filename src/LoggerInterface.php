<?php

namespace FoamyCastle\Log;

use Closure;
use Stringable;

interface LoggerInterface
{
    public function emergency(string|Stringable|Closure $message, array $context = []): void;
    public function alert(string|Stringable|Closure $message, array $context = []): void;
    public function critical(string|Stringable|Closure $message, array $context = []): void;
    public function error(string|Stringable|Closure $message, array $context = []): void;
    public function warning(string|Stringable|Closure $message, array $context = []): void;
    public function notice(string|Stringable|Closure $message, array $context = []): void;
    public function info(string|Stringable|Closure $message, array $context = []): void;
    public function debug(string|Stringable|Closure $message, array $context = []): void;
    public function log(LogLevel $level, string|Stringable|Closure $message, array $context = []): void;
}
