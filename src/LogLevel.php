<?php

namespace FoamyCastle\Log;

enum LogLevel:int{
	case ALL=255;
	case EMERGENCY=128;
	case ALERT=64;
	case CRITICAL=32;
	case ERROR=16;
	case WARNING=8;
	case NOTICE=4;
	case INFO=2;
	case DEBUG=1;
}
