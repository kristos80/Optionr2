<?php
declare(strict_types = 1);

/*
 * Copyright 2019 Christos Athanasiadis <christos.k.athanasiadis@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */
namespace Kristos80\Optionr2;

/**
 * Optionr is a simple class that makes it extremely easy to grab an option from a collection of
 * options, without the need to make all those ```isset``` and ```array_key_exists``` comparisons.
 *
 * @author Christos Athanasiadis <christos.k.athanasiadis@gmail.com>
 * @license https://www.opensource.org/licenses/mit-license.php
 */
class Optionr implements \PetrKnap\Php\Singleton\SingletonInterface {
	use \PetrKnap\Php\Singleton\SingletonTrait;

	/**
	 * Invoke main method
	 *
	 * @param string|array|object $name
	 * @param array|object $pool
	 * @param mixed $default
	 * @param bool $sensitive
	 * @param bool|array $acceptedValues
	 * @return mixed
	 */
	public function __invoke($name = '', $pool = array(), $default = NULL, $sensitive = FALSE, $acceptedValues = FALSE) {
		return $this->get($name, $pool, $default, $sensitive, $acceptedValues);
	}

	/**
	 * Main method
	 *
	 * @param string|array|object $name
	 * @param array|object $pool
	 * @param mixed $default
	 * @param bool $sensitive
	 * @param bool|array $acceptedValues
	 * @return mixed
	 */
	public function get($name = '', $pool = array(), $default = NULL, $sensitive = FALSE, $acceptedValues = FALSE) {
		# We check the possibility that the user passed all arguments in one variable
		#
		# Example:
		#
		# get(array(
		#  'name' => 'option',
		#  'pool' => array(
		#		'option' => TRUE,
		#		'otherOption' => FALSE,
		#	),
		#	'default' => FALSE,
		#	'sensitive' => TRUE,
		#	'acceptedValues' => array(
		#		TRUE,
		#		FALSE
		#	),
		# ));
		$nameIsConfiguration = FALSE;
		if (is_array($name) || is_object($name)) {
			$name = (array) $name;
			if (isset($name['name']) && isset($name['pool'])) {
				$nameIsConfiguration = TRUE;
			}
		}

		# name is a configuration array, so let's do some recursion
		if ($nameIsConfiguration) {
			$name_ = $name;
			$name = $this->get('name', $name_, '');
			$pool = $this->get('pool', $name_, $pool);
			$default = $this->get(array(
				'default',
				'defaultValue',
			), $name_, $default);
			$sensitive = $this->get('sensitive', $name_, $sensitive);
			$acceptedValues = $this->get('acceptedValues', $name_, $acceptedValues);
		}

		# We check all posibilities that could give us a valid name.
		# In different case we do create one, so that the code doesn't break
		if (! is_array($name) && ! is_object($name) && ! is_string($name) && ! is_numeric($name)) {
			$name = (string) serialize($name);
		}

		# Cast name as array and remove indexes as they play no role
		if (is_object($name)) {
			$name = array_values((array) $name);
		}

		# We cast the pool to array so that we can traverse it
		$pool = (array) $pool;

		# Default value
		$option = $default;

		# If we don't make case sensitive searches, we need to flat all keys to lower case ones
		if (! (bool) $sensitive) {
			# Name is string or number
			if (! is_array($name) && ! is_object($name)) {
				# Lower case string name. Keep number intact
				$name = ! is_numeric($name) ? strtolower($name) : $name;
			} else {
				# Traverse name and lower case values
				$name_ = array();
				foreach ($name as $name__) {
					$name_[] = strtolower($name__);
				}
				$name = $name_;
			}

			# Traverse pool and lower case values
			$pool_ = array();
			foreach ($pool as $poolKey => $poolValue) {
				$pool_[strtolower((string) $poolKey)] = $poolValue;
			}

			$pool = $pool_;
		}

		# Traverse name if is array until name is found in pool keys
		if (is_array($name)) {
			foreach ($name as $possibleName) {
				if (array_key_exists($possibleName, $pool)) {
					if ($pool[$possibleName]) {
						$option = $pool[$possibleName];
						break;
					}
				}
			}
		} else {
			# name is string or numeric, so we check if exists as key in pool, otherwise we return the default value
			$option = array_key_exists($name, $pool) ? $pool[$name] : $option;
		}

		# Accepted values were set...
		if ($acceptedValues !== FALSE) {
			# ...but we accept only array or object...
			if (! is_array($acceptedValues) && ! is_object($acceptedValues)) {
				$acceptedValues = FALSE;
			} else {
				# ...which is casted to a numeric indexed array as keys play no role (again)
				$acceptedValues = array_values((array) $acceptedValues);
			}
		}

		# We check against accepted values
		if ($acceptedValues !== FALSE) {
			# The value is not within accepted values...
			if (! in_array($option, $acceptedValues)) {
				# ...But the default value is not within as well, so we reset it
				if (! in_array($default, $acceptedValues)) {
					$default = NULL;
				}

				# Reset returned value to default
				$option = $default;
			}
		}

		return $option;
	}
}