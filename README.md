## Install

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/3adeff3bb790438a887f79bc73456010)](https://app.codacy.com/app/kristos80/Optionr2?utm_source=github.com&utm_medium=referral&utm_content=kristos80/Optionr2&utm_campaign=Badge_Grade_Dashboard)

Install latest version using [composer](https://getcomposer.org/).
```
$ composer require kristos80/optionr2
```

## Optionr
Optionr is a simple helper with a single method ->get() that makes it extremely easy to grab an option 
from a collection of options

```php
/*
* @param string|array|object $name
* @param array|object $pool
* @param mixed $default
* @param bool $sensitive
* @param bool|array $acceptedValues
* @return mixed
*/
public function get($name = '', $pool = array(), $default = NULL, $sensitive = FALSE, $acceptedValues = FALSE) 
```
## Version 2.0.0 Update
Class is now callable, so all examples can be rewritten without the need of the ```->get()``` method

***When the class is used within another class, the class SHOULD BE WRAPPED in `()`***

```($this->options)($name,$pool)```

***see [here](https://stackoverflow.com/questions/41460662/why-php-invoke-not-working-when-triggered-from-an-object-property)***

## Version 3.0.0 Update
```get``` method can now accept a configuration array/object as a single parameter. ***See examples*** 

## Examples
Examples include, but not limited to:

```php
require_once 'vendor/autoload.php';
$options = new \Kristos80\Optionr2\Optionr();

# Example 1 ================================
$example1LookingForVar = 'index1';
$example1Pool = array(
	'index1' => 'valueOfIndex1',
	'index2' => 'valueOfIndex2',
);

$example1Result1 = $options->get($example1LookingForVar, $example1Pool);
$example1Result2 = $options->get(array(
	'name' => $example1LookingForVar,
	'pool' => $example1Pool,
));

# Example 1 print ================================
var_dump($example1Result1);
var_dump($example1Result2);
var_dump($example1Result1 === $example1Result2);

echo "\r\n";

# Example 2 ================================
$example2LookingForVar = array(
	'index2',
	'index1'
);

$example2Pool = new \stdClass();
$example2Pool->index1 = 'valueOfIndex1';
$example2Pool->index2 = 'valueOfIndex2';

$example2Result = $options->get(array(
	'name' => $example2LookingForVar,
	'pool' => $example2Pool,
));

# Example 2 print ================================
var_dump($example2Result);

echo "\r\n";

# Example 3 ================================
$example3LookingForVar = array(
	'index2',
	'index1',
	'index0',
);

$example3Pool = new \stdClass();
$example3Pool->index4 = 'valueOfIndex4';
$example3Pool->index5 = 'valueOfIndex5';
$example3Pool->index6 = 'valueOfIndex6';

$example3Result = $options->get(array(
	'name' => $example3LookingForVar,
	'pool' => $example3Pool,
	# default can be passed as defaultValue as well ;) === 'defaultValue' => 'defaultValue'
	'default' => 'defaultValue',
));

# Example 3 print ================================
var_dump($example3Result);

echo "\r\n";

# Example 4 ================================
$example4LookingForVar = 'index4';

$example4Pool = new \stdClass();
$example4Pool->index4 = 'valueOfIndex4';

$example4DefaultValue = 'anyOtherValueButNotIndex4';

$example4SensitiveSearch = FALSE;

$example4AcceptedValues = array(
	'valueOfIndex4IsNotAllowed',
	'anyOtherValueButNotIndex4',
);

$example4Result = $options->get($example4LookingForVar, $example4Pool, $example4DefaultValue, $example4SensitiveSearch, $example4AcceptedValues);
# Example 4 print ================================
var_dump($example4Result);

echo "\r\n";

# Example 5 ================================
$example5LookingForVar = 'Index4';

$example5Pool = array(
	'index4' => 'valueOfIndex4'
);

$example5DefaultValue = NULL;

$example5SensitiveSearch = TRUE;

$example5Result = $options->get($example5LookingForVar, $example5Pool, $example5DefaultValue, $example5SensitiveSearch);
# Example 5 print ================================
var_dump($example5Result);

echo "\r\n";

/** Prints ================================
string(13) "valueOfIndex1"
string(13) "valueOfIndex1"
bool(true)

string(13) "valueOfIndex2"

string(12) "defaultValue"

string(25) "anyOtherValueButNotIndex4"

NULL

 ================================ **/
```
