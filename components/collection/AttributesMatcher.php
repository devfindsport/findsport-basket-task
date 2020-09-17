<?php

/**
 * Сопоставляет атрибуты с проверяемым объектом
 */
class AttributesMatcher
{
	public $attributes = [];
	
	/**
	 * Конструктор
	 * 
	 * @param array $attributes Сопоставлеямые атрибуты
	 */
	public function __construct(array $attributes)
	{
		$this->attributes = $attributes;
	}
	
	/**
	 * Сопоставляет атрибуты с проверяемым объектом
	 * Магический метод
	 * 
	 * @param object $object Проверяемый объект
	 * @return boolean
	 */
	public function __invoke($object)
	{
		return $this->matchTo($object);
	}
	
	/**
	 * Сопоставляет атрибуты с проверяемым объектом
	 * 
	 * @param object $object Проверяемый объект
	 * @return boolean
	 */
	public function matchTo($object)
	{
		$match = true;
		
		foreach ($this->attributes as $attribute => $value)
		{
			if ($object->{$attribute} != $value)
			{
				$match = false;
				break;
			}
		}
		
		return $match;
	}
}
