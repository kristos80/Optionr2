## Install
Install latest version using [composer](https://getcomposer.org/).
```
$ composer require kristos80/optionr2
```

## Optionr
Optionr is a simple class with a single method ->get() that makes it extremely easy to grab an option 
from an array of options.

```php
/*
*  @param string|array	$name		A string or an array containing the name of the key/attribute to search for
*  @param array|object	$pool 		An array or an object to whose keys/properties will search in
*  @param mixed		$default	Default value if nothing is found
*  @param bool		$sensitive	Case sensitive search
*/
public function get($name = '', $pool = array(), $default = NULL, $sensitive = FALSE) 
```

Examples include, but not limited to:

```php
require_once 'vendor/autoload.php';
use Kristos80\Optionr2\Optionr as Optionr;
define('DEFAULT_VALUE_CONS', 'Value4');

$option = new Optionr();

$poolOfValues = array(
	'key1' => 'valueOf_key1',
	'key2' => 'valueOf_key2',
	'Key2' => 'valueOf_Key2',
);

$value1 = $option->get('key1', $poolOfValues); // ==> valueOf_key1
$value2 = $option->get(array(
	'key2',
	'Key2'
), (object) $poolOfValues); // ==> valueOf_Key2
                           // because during runtime the last value will override
                           // any similar named keys (keyName, keyname, KEYNAME, etc)
$value3 = $option->get(array(
	'key2'
), $poolOfValues, NULL, TRUE); // ==> valueOf_key2 (lower k)
                              // because 4rd parameter makes the search case sensitive
$value4 = $option->get(array(
	'KeyDoesntExist'
), $poolOfValues, DEFAULT_VALUE_CONS); // ==> Value4
```
