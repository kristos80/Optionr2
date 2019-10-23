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
	 * @param string $preSuf
	 *
	 * @return mixed
	 */
	public function __invoke($name = '', $pool = array(), $default = NULL, $sensitive = FALSE, $acceptedValues = array(), string $preSuf = '') {
		return $this->get($name, $pool, $default, $sensitive, $acceptedValues, $preSuf);
	}

	/**
	 * Main method
	 *
	 * @param string|array|object $name
	 * @param array|object $pool
	 * @param mixed $default
	 * @param bool $sensitive
	 * @param bool|array $acceptedValues
	 * @param string $preSuf
	 *
	 * @return mixed
	 */
	public function get($name = '', $pool = array(), $default = NULL, $sensitive = FALSE, $acceptedValues = array(), string $preSuf = '') {
		if ($config = $this->nameIsConfiguration($name)) {
			$name = $this->get('name', $config);
			$pool = $this->get('pool', $config);
			$default = $this->get(array(
				'default',
				'defaultValue',
			), $config);
			$sensitive = $this->get('sensitive', $config);
			$acceptedValues = $this->get('acceptedValues', $config);
		}

		$name = $this->convertNameToCompatibleStructure($name, (bool) $sensitive);
		$pool = $this->convertPoolToCompatibleStructure($pool, (bool) $sensitive);
		$acceptedValues = $this->convertAcceptedValuesToCompatibleStructure($acceptedValues);

		$option = $this->find($name, $pool, $default);
		$option = $this->validateValue($option, $acceptedValues, $default);

		if (is_string($option) && $preSuf) {
			$fixes = $this->fixes($preSuf);
			$option = $fixes['pre'] . $option . $fixes['suf'];
		}

		return $option;
	}

	/**
	 *
	 * @param string $preSuf
	 * @return string
	 */
	protected function fixes(string $preSuf): array {
		$fixesToks = explode('\,', $preSuf);

		$prefix = FALSE;
		$suffix = FALSE;
		if (substr($fixesToks[0], 0, 4) === 'PRE:') {
			$prefix = substr($fixesToks[0], 4);
		}

		if (substr($fixesToks[0], 0, 4) === 'SUF:') {
			$suffix = substr($fixesToks[0], 4);
		}

		if (isset($fixesToks[1])) {
			if ($prefix) {
				if (substr($fixesToks[1], 0, 4) === 'SUF:') {
					$suffix = substr($fixesToks[1], 4);
				}
			} else {
				if (substr($fixesToks[1], 0, 4) === 'PRE:') {
					$prefix = substr($fixesToks[1], 4);
				}
			}
		}

		return array(
			'pre' => $prefix ?: (! $suffix ? $preSuf : ''),
			'suf' => $suffix,
		);
	}

	/**
	 *
	 * @param mixed $name
	 * @return boolean|array
	 */
	protected function nameIsConfiguration($name = '') {
		$nameIsConfiguration = FALSE;

		if (is_array($name) || is_object($name)) {
			$name = (array) $name;
			if (isset($name['name']) && isset($name['pool'])) {
				$nameIsConfiguration = TRUE;
			}
		}

		return $nameIsConfiguration ? $name : FALSE;
	}

	/**
	 *
	 * @param mixed $name
	 * @param bool $sensitive
	 * @return array
	 */
	protected function convertNameToCompatibleStructure($name = '', bool $sensitive): array {
		if (is_string($name) || is_numeric($name)) {
			$name = array(
				$name,
			);
		}

		$name = array_values((array) $name);

		return $sensitive ? $this->flatten($name) : $name;
	}

	/**
	 *
	 * @param mixed $pool
	 * @param bool $sensitive
	 * @return array
	 */
	protected function convertPoolToCompatibleStructure($pool = array(), bool $sensitive): array {
		$pool = (array) $pool;

		return $sensitive ? $this->flatten($pool) : $pool;
	}

	/**
	 *
	 * @param mixed $acceptedValues
	 * @return array
	 */
	protected function convertAcceptedValuesToCompatibleStructure($acceptedValues): array {
		$acceptedValues = array_values((array) $acceptedValues);

		return $acceptedValues;
	}

	/**
	 *
	 * @param array $objectToFlatten
	 * @return array
	 */
	protected function flatten(array $objectToFlatten = array()): array {
		$objectToFlatten_ = array();
		foreach ($objectToFlatten as $objectToFlattenKey => $objectToFlattenValue) {
			$objectToFlatten_[strtolower((string) $objectToFlattenKey)] = $objectToFlattenValue;
		}

		return $objectToFlatten_;
	}

	/**
	 *
	 * @param array $name
	 * @param array $pool
	 * @param mixed $default
	 * @return mixed
	 */
	protected function find(array $name = array(), array $pool = array(), $default) {
		$option = $default;
		foreach ($name as $possibleName) {
			if (array_key_exists($possibleName, $pool)) {
				if ($pool[$possibleName]) {
					$option = $pool[$possibleName];
					break;
				}
			}
		}

		return $option;
	}

	/**
	 *
	 * @param mixed $option
	 * @param array $acceptedValues
	 * @param mixed $default
	 * @return mixed|NULL
	 */
	protected function validateValue($option, array $acceptedValues = array(), $default) {
		if (count($acceptedValues)) {
			if (! in_array($option, $acceptedValues)) {
				if (! in_array($default, $acceptedValues)) {
					$default = NULL;
				}

				$option = $default;
			}
		}

		return $option;
	}
}