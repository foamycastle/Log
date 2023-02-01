# Log
A (slightly modified) PSR-3 logging tool. Instead of the logging instance model, this library takes the approach of using an instance of a log file object with static methods. 
## Usage
### Example
Establish any number of `LogFile` objects that each represent a different log file or location. Then use the static methods named by the different log levels to print lines to the log. Any data type may be used as a message, even callables and closures.
```php
//Establish a log file instance
$logFile = (new LogFile("/path/to/log/file/","logfile.name.log",['option'=>'value']))
                ->setTimeZone(new DateTimeZone('America/Chicago'))
                ->setTimeFormat(DATE_RFC1123);

//static method indicates log level
Log::Alert($logFile,"Log Message with @{variable}",['variable'=>'variables!']);

//output to log file
ALERT: Wed, 01 Feb 2023 16:41:19 -0600 Log Message with variables!
```
### `Log` API
#### Static Methods
```php
Log::Alert(LoggingInstance $instance, string $message, array $context):void
Log::Warning(LoggingInstance $instance, string $message, array $context):void
Log::Emergency(LoggingInstance $instance, string $message, array $context):void
Log::Critical(LoggingInstance $instance, string $message, array $context):void
Log::Error(LoggingInstance $instance, string $message, array $context):void
Log::Notice(LoggingInstance $instance, string $message, array $context):void
Log::Info(LoggingInstance $instance, string $message, array $context):void

Log::Debug(
    LoggingInstance $instance,
    string $message, 
    array $context
):void
```
##### Arguments
`LoggingInstance $instance` - An instance of an object that implements `LoggingInstance`<br/>
`string $message` - a string containing readable text and symbols @{symbol} that will be replaced by a corresponding key/value pair in the `$context` array.
`array $context` - a key/value array that contains symbols as keys and any object as the value. The value object will be stringified and replace its symbol in the `$message` string. 
#### Abstract Methods
```php
protected function write(string $message):bool;
```
### `LoggingInstance` API

| Function          | Arguments                                 | Returns           | Description                                                                                                                           |
|-------------------|-------------------------------------------|-------------------|---------------------------------------------------------------------------------------------------------------------------------------|
| `getTimestamp()`  |                                           | `string`          | Returns the formatted timestamp according to the format set by `setTimeFormat()`                                                      |
| `setOptions()`    | `array $options`                          | `LoggingInstance` | Allows many options to be set at once via a key/pair array                                                                            |
| `setTimeFormat()` | `string $format`                          | `LoggingInstance` | Sets the time format. Uses the stand time formatting codes.                                                                           |       
| `setTimezone()`   | `string $format` or `DateTimeZone` object | `LoggingInstance` | Sets the timezone offset for the timestamp. Accepts either a string containing a timezone identifier or an instance of `DateTimeZone` |       

### `LogFile` object API
#### Constructor
```php
LogFile(
    string $path, 
    string $fileName, 
    array $options
):LoggingInstance
```
##### Arguments
`string $path` A file system path.  If the path does not exist, it will be created.

`string $fileName` A file resource. If the resource does not exist in the path, it will be created.

`array $options` A hash of options

- `timezone => (string)` - a valid timezone identifier given by `timezone_indentifiers_list()`
- `time_format => (string)` or `DateTimeZone` instance - best to use the DATE_xxxx constants here. 

#### Public Methods
| Method          | Arguments | Returns  | Description                             |
|-----------------|-----------|----------|-----------------------------------------|
| `getPath()`     |           | `string` | returns the path without the file name. |
| `getFilename()` |           | `string` | returns only the file name in the path. |
| `getRealPath()` |           | `string` | returns the complete path string.       |


