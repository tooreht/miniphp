<?php

/**
* Mini - a micro PHP 5 framework
*
* @author tooreht <tooreht@gmail.com>
* @copyright 2014 tooreht
* @link http://www.miniframework.com
* @license http://www.miniframework.com/license
* @version 0.0.1
* @package Mini
*/

namespace mini\utils;

class Container implements \ArrayAccess, \Countable, \IteratorAggregate
{
	protected $data = array();

	protected function normalizeKey($key)
	{
		return $key;
	}

	public function has($key)
	{
		return array_key_exists($this->normalizeKey($key), $this->data);
	}

	public function keys()
	{
		return array_keys($this->data);
	}

	public function get($key, $default = null)
	{
		if ($this->has($key)) {
			$value = $this->data[$this->normalizeKey($key)];
			$isInvokable = is_object($value) && method_exists($value, '__invoke');
			return $isInvokable ? $value($this) : $value;
		}
		return $default;
	}

	public function set($key, $value)
	{
		$this->data[$this->normalizeKey($key)] = $value;
	}

	public function replace($items)
	{
		foreach ($items as $key => $value) {
			$this->set($key, $value);
		}
	}

	public function remove($key)
	{
		unset($this->data[$this->normalizeKey($key)]);
	}

	public function clear()
	{
		$this->data = array();
	}

	public function all()
	{
		return $this->data;
	}

	public function __construct($items = array())
	{
		$this->replace($items);
	}

	/**
	* Property Overloading
	*/
	public function __get($key)
	{
		return $this->get($key);
	}

	public function __set($key, $value)
	{
		$this->set($key, $value);
	}
	
	public function __isset($key)
	{
		return $this->has($key);
	}
	
	public function __unset($key)
	{
		return $this->remove($key);
	}

	/**
	* ArrayAccess
	*/
	public function offsetExists($offset)
	{
		return $this->has($offset);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

	/**
	* Countable
	*/
	public function count()
	{
		return count($this->data);
	}

	/**
	* IteratorAggregate
	*/
	public function getIterator()
	{
		return new \ArrayIterator($this->data);
	}

	public function singleton($key, $value)
	{
		$this->set($key, function ($c) use ($value) {
			static $object;
			if (null === $object) {
				$object = $value($c);
			}
			return $object;
		});
	}
}