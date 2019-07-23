## Install

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/3adeff3bb790438a887f79bc73456010)](https://app.codacy.com/app/kristos80/Optionr2?utm_source=github.com&utm_medium=referral&utm_content=kristos80/Optionr2&utm_campaign=Badge_Grade_Dashboard)

Install latest version using [composer](https://getcomposer.org/).
```
$ composer require kristos80/optionr2
```

## Optionr
Optionr is a simple class with a single method ->get() that makes it extremely easy to grab an option 
from an array of options.

```php
/*
*  @param string|array|object	$name    	A string/arr/obj containing the name of the key/attribute to search for
*  @param array|object  	$pool 		An arr/object to whose keys/properties will search in
*  @param mixed         	$default	Default value if nothing is found
*  @param bool          	$sensitive	Case sensitive search
*  @param bool|array    	$acceptedValues A pool of values that the return/default value should belong to
*/
public function get($name = '', $pool = array(), $default = NULL, $sensitive = FALSE, $acceptedValues = FALSE) 
```
## Version 2.0.0 Update
Class is now callable, so all examples can be rewritten without the need of the ```->get()``` method

***When the class is used within another class, the class SHOULD BE WRAPPED in `()`***

```($this->options)($name,$pool)```

***see [here](https://stackoverflow.com/questions/41460662/why-php-invoke-not-working-when-triggered-from-an-object-property)***

## Examples
Examples include, but not limited to:

```php
<?php
require_once 'vendor/autoload.php';
use Kristos80\Optionr2\Optionr as Options;

$options = new Options();

define('POOL_ONE_DEFAULT_VALUE', 'Value not found');
$poolOne = array(
	'value_1' => 'a',
	'value_2' => 'b',
	'value_3' => 'c',
);
$poolOneAcceptedValues = array(
	'd',
	'e',
	'f'
);

$poolTwo = new stdClass();
$poolTwo->value_1 = 'a';
$poolTwo->value_2 = 'b';
$poolTwo->value_3 = 'c';

// Example #1
// ==> `a`
// The simplest example
echo 'Example #1: ';
echo $options->get('value_1', $poolOne);
echo '<br/>';

// Example #2
// ==> `Value not found`
// Because the search is case sensitive (4th parameter) no value found,
// so the returned value falls back to the default (3rd parameter).
// Note that Parameter 2 can be an array or object
echo 'Example #2: ';
echo $options->get('Value_1', $poolTwo, POOL_ONE_DEFAULT_VALUE, TRUE);
echo '<br/>';

// Example #3
// ==> `` (null)
// Because the search is case sensitive (4th parameter) no value found,
// so the returned value falls back to the default (3rd parameter),
// but the default value is not within the accepted values (5th parameter)
echo 'Example #3: ';
echo $options->get('Value_1', $poolOne, POOL_ONE_DEFAULT_VALUE, TRUE, $poolOneAcceptedValues);
echo '<br/>';

// Examples #4.1, #4.2
// ==> `b`
// Parameter 1 can be an array or object. The return value is the first one to
// be found.
// Note that by default the search is not sensitive, so Value_2 = value_2
echo 'Example #4.1: ';
echo $options->get(array(
	'Value_2',
	'value_3',
), $poolOne);
echo '<br/>';

echo 'Example #4.2: ';
echo $options->get((object) array(
	'value_2',
	'value_3',
), $poolTwo);
echo '<br/>';

// Example #5
// ==> `Customer Id cannot be empty`
echo doSomethingVeryComplex(array(
	'mode' => 'FIND_CUSTOMER'
));
echo '<br/>';

// Examples #6
// ==> `This is id is way too old to have it in a database`
echo doSomethingVeryComplex(array(
	'mode' => 'FIND_CUSTOMER',
	'customerId' => 4,
));
echo '<br/>';
echo doSomethingVeryComplex(array(
	'mode' => 'FIND_CUSTOMER',
	'ID' => 4,
));
echo '<br/>';

function doSomethingVeryComplex($config = array()) {
	$options = new Options();
	$mode = $options->get('mode', $config);

	if ($mode === 'FIND_CUSTOMER') {
		$id = $options->get(array(
			'id',
			'customerId'
		), $config);
		if ($id === NULL) {
			return 'Customer Id cannot be empty';
		}
		if ($id <= 5) {
			return 'This is id is way too old to have it in a database';
		}
	}
}
```
