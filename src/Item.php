<?php namespace Creolab\Resources;

use Str;

class Item {

	/**
	 * Data for item
	 * @var array
	 */
	protected $data;

	/**
	 * Transformer class
	 * @var array
	 */
	protected $transformer;

	/**
	 * Definitions for transforming data types
	 * @var array
	 */
	protected $transform = array();

	/**
	 * Init new item
	 * @param array $data
	 */
	public function __construct($data = array())
	{
		$this->transformer = new Transformer($this->transform);
		$this->data        = $this->transformer->transform($data);
	}

	/**
	 * Return property for item
	 * @param  string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		$attrMethod = 'get' . Str::studly($key) . 'Attribute';

		if (method_exists($this, $attrMethod))
		{
			return $this->$attrMethod(array_get($this->data, $key));
		}
		else
		{
			return array_get($this->data, $key);
		}
	}

	/**
	 * Set a property
	 * @param string $key
	 * @param mixed  $val
	 */
	public function __set($key, $val)
	{
		$this->data[$key] = $val;
	}

	/**
	 * Return array representation of item
	 * @return array
	 */
	public function toArray()
	{
		$this->data = $this->transformer->transformBack($this->data);

		return $this->data;
	}

	/**
	 * Return JSON string of item data
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return @json_encode($this->toArray(), $options);
	}

	/**
	 * Return JSON for string
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}

}
