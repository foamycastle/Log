# Log
A (slightly modified) PSR-3 logging tool. Instead of the logging instance model, this library takes the approach of using an instance of a log file object with static methods. 
## Usage
### Example
Establish any number of `LogFile` objects that each represent a different log file or location. Then use the static methods named by the different log levels to print lines to the log. Any data type may be used as a message, even callables and closures.
```php
//Establish a log file object
$logFile = new LogFile("/path/to/log/file/","logfile.name.log",['option'=>'value']);

//static method indicates log level
Log::Alert($logFile,"Log Message with @{variable}",['variable'=>'variables!']);
```
### `LogFileInterface` API

| Function             | Arguments          | Returns            | Description                                                                         |
|----------------------|--------------------|--------------------|-------------------------------------------------------------------------------------|
| ```getFilename()```  |                    | `string`           | Returns the filename portion of the log file path.                                  |
| ```getPath()```      |                    | `string`           | Returns the path portion of the log file path.                                      |
| ```getRealPath()```  |                    | `string`           | Returns the entire path of the lof file.                                            |
| ```getTimestamp()``` |                    | `string`           | Returns the formatted timestamp according to the format set by `setTimeFormat()`    |
| ```setOptions()```   | - `array $options` | `LogFileInterface` | Allows many options to be set at once via a key/pair array<br/>`timezone=>[string]` |
|                      |                    |                    |                                                                                     |


```php
function setOptions(array $options):LogFileInterface;
```
Set the timestamp format according to the `DateTime` format code.
```php
function setTimeFormat(string $format):LogFileInterface;
```
Sets the timezone for the timestamp. The `$timezone` argument can either a `DateTimeZone` object or a string containing a valid timezone identifier.
```php
function setTimezone($timezone=null):LogFileInterface;
```
```php
function write(string $data):bool;
```

### `LogFile` object API
#### Constructor
```php
LogFile(
    string $path, 
    string $fileName, 
    array $options
):LogFileInterface
```
##### Arguments
`string $path` A file system path or a remote path.

`string $fileName` A file resource. If the resource does not exist in the path, it will be created

`array $options` A hash of options

- `timezone => (string)` - a valid timezone identifier given by `timezone_indentifiers_list()`
- `time_format => (string)` - a string containing a date/time format. best to use the DATE_xxxx constants here. 

#### 

