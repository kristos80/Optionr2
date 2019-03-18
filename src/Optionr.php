<?php
declare(strict_types = 1);

/*
 * Copyright (c) 2019 Christos Athanasiadis
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy
 * of this software and associated documentation files (the "Software"), to
 * deal
 * in the Software without restriction, including without limitation the
 * rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE
 * SOFTWARE.
 */

/**
 *
 * @see https://github.com/kristos80/optionr2
 * @license https://www.opensource.org/licenses/mit-license.php
 */
namespace Kristos80\Optionr2;

class Optionr implements \PetrKnap\Php\Singleton\SingletonInterface {
	
	use \PetrKnap\Php\Singleton\SingletonTrait;
	
	public function get($name = '', $pool = array(), $default = NULL, $sensitive = FALSE) {
		if (! is_array($name)) {
			if (! is_string($name) && ! is_numeric($name)) {
				$name = (string) serialize($name);
			}
		}
		
		$pool = (array) $pool;
		$option = $default;
		
		if (! $sensitive) {
			if (! is_array($name)) {
				$name = ! is_numeric($name) ? strtolower($name) : $name;
			} else {
				$name_ = array();
				foreach ($name as $name__) {
					$name_[] = strtolower($name__);
				}
				$name = $name_;
			}
			
			$pool_ = array();
			foreach ($pool as $poolKey => $poolValue) {
				$pool_[strtolower((string) $poolKey)] = $poolValue;
			}
			
			$pool = $pool_;
		}
		
		if (is_array($name)) {
			foreach ($name as $possibleName) {
				if (array_key_exists($possibleName, $pool)) {
					if ($pool[$possibleName]) {
						$option = $pool[$possibleName];
					}
					break;
				}
			}
		} else {
			$option = array_key_exists($name, $pool) ? ($pool[$name] ? $pool[$name] : $option) : $option;
		}
		
		return $option;
	}
}