<?php namespace Creolab\Resources;

use Carbon\Carbon;

class Transformer {

	/**
	 * Definitions for transforming data types
	 * @var array
	 */
	protected $rules = array();

	/**
	 * Default definitions for transforming data types
	 * @var array
	 */
	protected $defaultRules = array(
		'created_at' => 'parse_datetime',
		'updated_at' => 'parse_datetime',
		'deleted_at' => 'parse_datetime',
	);

	/**
	 * Init
	 * @param array $rules
	 */
	public function __construct($rules = null)
	{
		$this->rules = $rules;
	}

	/**
	 * Transform data types for item
	 * @param  array $data
	 * @param  array $rules
	 * @return array
	 */
	public function transform($data = null, $rules = null)
	{
		// The data
		if ( ! $data) $data = $this->data;

		// The rules
		if ( ! $rules) $rules = $this->transform;

		// Merge the default ones in
		$rules = array_merge($this->transformDefault, $rules);

		foreach ($rules as $key => $rule)
		{
			if (isset($data[$key]))
			{
				if     ($rule == 'parse_datetime')                $data[$key] = Carbon::parse($data[$key]);
				elseif ($rule == 'parse_time')                    $data[$key] = Carbon::parse($data[$key]);
				elseif ($rule == 'parse_date')                    $data[$key] = Carbon::parse($data[$key]);
				elseif ($rule == 'strip_tags')                    $data[$key] = strip_tags($data[$key]);
				elseif (is_string($rule) and class_exists($rule)) $data[$key] = new $rule($data[$key]);
				elseif (is_array($rule))                          $data[$key] = $data[$key];
			}
		}

		return $data;
	}

	/**
	 * Transform back data types to array/string values
	 * @param  array $data
	 * @param  array $rules
	 * @return array
	 */
	public function transformDataBack($data = null, $rules = null)
	{
		// The data
		if ( ! $data) $data = $this->data;

		// The rules
		if ( ! $rules) $rules = $this->transform;

		// Merge the default ones in
		$rules = array_merge($this->transformDefault, $rules);

		foreach ($rules as $key => $rule)
		{
			if (isset($data[$key]))
			{
				if     ($rule == 'parse_datetime' and is_a($data[$key], 'Carbon'))  $data[$key] = $data[$key]->format('Y-m-d H:i:s');
				elseif ($rule == 'parse_time' and is_a($data[$key], 'Carbon'))      $data[$key] = $data[$key]->format('H:i:s');
				elseif ($rule == 'parse_date' and is_a($data[$key], 'Carbon'))      $data[$key] = $data[$key]->format('Y-m-d');
				elseif (is_string($rule) and method_exists($data[$key], 'toArray')) $data[$key] = $data[$key]->toArray();
				// elseif (is_array($rule))       $data[$key] = $data[$key];
			}
		}

		return $data;
	}

}
