<?php

/**
 * Сравнивает два элемента коллекции
 */
class Comparator
{
	/**
	 * @var boolean Инвертировать результат для обратной сортировки
	 */
	public $reverse = false;
	
	/**
	 * @var string Сравниваемое значение
	 */
	public $attribute;
	/**
	 * @var callable Выборщик сравниваемого значения элемента
	 */
	public $getter;
	/**
	 * @var callable Сравниватель элементов
	 */
	public $comparator;
	
	/**
	 * Конструктор
	 * 
	 * @param array $config [optional] Настройки сортировки
	 */
	public function __construct(array $config = [])
	{
		foreach ($config as $property => $value)
		{
			$this->{$property} = $value;
		}
	}
	
	/**
	 * Сравнивает два элемента.
	 * Магический метод
	 * 
	 * @param mixed $a Элемент 1
	 * @param mixed $b Элемент 2
	 * @return integer Положительные значения: a > b, отрицательные: a < b, 0 - если равны
	 */
	public function __invoke($a, $b)
	{
		return $this->compare($a, $b);
	}
	
	/**
	 * Сравнивает два элемента
	 * 
	 * @param mixed $a Элемент 1
	 * @param mixed $b Элемент 2
	 * @return integer Положительные значения: a > b, отрицательные: a < b, 0 - если равны
	 */
	public function compare($a, $b)
	{
		$compared = $this->compareValues($this->getValueFrom($a), $this->getValueFrom($b));
		return $this->reverse ? (0 - $compared) : $compared;
	}
	
	/**
	 * Выбирает значение из элемента
	 * 
	 * @param mixed $object Элемент
	 * @return mixed Выбранное значение
	 */
	public function getValueFrom($object)
	{
		if ($this->attribute)
		{
			return $object->{$this->attribute};
		}
		elseif ($this->getter)
		{
			return call_user_func($this->getter, $object);
		}
		else
		{
			return $object;
		}
	}
	
	/**
	 * Сравнивает указанные значения
	 * 
	 * @param mixed $a Значение 1
	 * @param mixed $b Значение 2
	 * @return integer Положительные значения: a > b, отрицательные: a < b, 0 - если равны
	 */
	public function compareValues($a, $b)
	{
		if ($this->comparator)
		{
			return call_user_func($this->comparator, $a, $b);
		}
		else
		{
			if ($a > $b) return 1;
			else if ($a < $b) return -1;
			else return 0;
		}
	}
}
