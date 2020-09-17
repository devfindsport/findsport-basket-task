<?php

/**
 * Коллекция моделей
 * 
 * @property-read CollectionIterator $iterator Итератор коллекции
 * @property-read boolean $isEmpty Коллекция пустая?
 * @property-read mixed $first Первый элемент коллекции или null если коллекция пустая
 * @property-read mixed $last Последний элемент коллекции или null если коллекция пустая
 */
class Collection extends CList implements JsonSerializable
{
	/**
	 * @var string Название класса для итерации элементов коллекции
	 */
	public $iteratorClass = 'CollectionIterator';
	
	/**
	 * Создаёт новый экземпляр класса
	 * 
	 * @param array $items [optional] Массив элементов
	 * @param boolean $readOnly [optional] Только для чтения. По умолчанию: false
	 * @return \Collection
	 *
	 * @deprecated
	 */
	static public function create($items = [], $readOnly = false)
	{
		return new static($items, $readOnly);
	}
	
	/**
	 * Создаёт итератор коллекции
	 * 
	 * @return \CollectionIterator
	 */
	public function getIterator()
	{
		return new $this->iteratorClass($this);
	}
	
	/**
	 * Возвращает пустая ли коллекция
	 * 
	 * @return boolean
	 */
	public function getIsEmpty()
	{
		return $this->getCount() === 0;
	}
	
	/**
	 * Первый элемент коллекции
	 * 
	 * @return mixed или null если коллекция пуста
	 */
	public function getFirst()
	{
		if ($this->offsetExists(0))
		{
			return $this->itemAt(0);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Последний элемент коллекции
	 * 
	 * @return mixed или null если коллекция пуста
	 */
	public function getLast()
	{
		if ($this->getCount())
		{
			return $this->itemAt($this->getCount() - 1);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Применяет функцию ко всем элементам коллекции
	 * 
	 * @param callable $callback Применяемая функция. Аргументы: (mixed $value, integer $key, CollectionIterator $iterator)
	 * @return \Collection Себя
	 */
	public function each($callback)
	{
		foreach (($iterator = $this->getIterator()) as $index => $value)
		{
			call_user_func($callback, $value, $index, $iterator);
		}
		
		return $this;
	}
	
	/**
	 * Возвращает результаты применения функции ко всем элементам коллекции
	 * 
	 * @param callable $callback Применяемая функция. Возращает значение. Аргументы: (mixed $value, integer $key, CollectionIterator $iterator)
	 * @return array Результаты выполнения каждой операции
	 */
	public function map($callback)
	{
		$result = [];
		
		foreach (($iterator = $this->getIterator()) as $index => $value)
		{
			$result[$index] = call_user_func($callback, $value, $index, $iterator);
		}
		
		return $result;
	}
	
	/**
	 * Возвращает результат выполнения всех операций над каждым элементом
	 * 
	 * @param callable $callback Применяемая функция. Возвращает результирующее значение. Аргументы: (mixed $result, mixed $value, integer $key, CollectionIterator $iterator)
	 * @param mixed $defaultValue Значение "по умолчанию"
	 * @return mixed Результат выполнения всех операций
	 */
	public function reduce($callback, $defaultValue)
	{
		$result = $defaultValue;
		
		foreach (($iterator = $this->getIterator()) as $index => $value)
		{
			$result = call_user_func($callback, $result, $value, $index, $iterator);
		}
		
		return $result;
	}
	
	/**
	 * Ищет подходящий элемент коллекции
	 * 
	 * @param callable $filter Функция-условие. Возвращает true = выбрать, false = отбросить. Аргументы: (mixed $value, integer $key, CollectionIterator $iterator)
	 * @return object Элемент коллекции или null если не найдено
	 */
	public function find($filter)
	{
		$result = null;
		
		foreach (($iterator = $this->getIterator()) as $index => $value)
		{
			if (call_user_func($filter, $value, $index, $iterator))
			{
				$result = $value;
				$iterator->stop();
			}
		}
		
		return $result;
	}
	
	/**
	 * Ищет подходящий элемент коллекции, совпадающий с сопоставляемыми атрибутами
	 * 
	 * @param array $attributes Сопоставляемые атрибуты
	 * @return object Элемент коллекции или null если не найдено
	 */
	public function findByAttributes(array $attributes)
	{
		return $this->find(new AttributesMatcher($attributes));
	}
	
	/**
	 * Ищет все подходящие элементы коллекции
	 * 
	 * @param callable $filter Функция-условие. Возвращает true = выбрать, false = отбросить. Аргументы: (mixed $value, integer $key, CollectionIterator $iterator)
	 * @return Collection Коллекция с подходящими элементами
	 */
	public function findAll($filter)
	{
		$result = [];
		
		foreach (($iterator = $this->getIterator()) as $index => $value)
		{
			if (call_user_func($filter, $value, $index, $iterator))
			{
				$result[$index] = $value;
			}
		}
		
		return static::create($result);
	}
	
	/**
	 * Ищет все подходящие элементы коллекции, совпадающие с сопоставляемыми атрибутами
	 * 
	 * @param array $attributes Сопоставляемые атрибуты
	 * @return Collection Коллекция с подходящими элементами
	 */
	public function findAllByAttributes(array $attributes)
	{
		return $this->findAll(new AttributesMatcher($attributes));
	}
	
	/**
	 * Сортирует коллекцию
	 * 
	 * @param Comparator|Closure|callable|string $comparator Сравниватель элементов
	 * @return Collection Отсортированная коллекция
	 */
	public function sort($comparator)
	{
		$items = $this->toArray();
		usort($items, $comparator);
		return static::create($items);
	}
	
	/**
	 * Сортирует коллекцию по геттеру
	 * 
	 * @param Comparator|Closure|callable|string $getter Геттер сопоставляемого значения
	 * @param boolean $reverse [optional] Сортировать в обратном порядке. По умолчанию: false
	 * @return Collection Отсортированная коллекция
	 */
	public function sortBy($getter, $reverse = false)
	{
		return $this->sort(new Comparator(['getter' => $getter, 'reverse' => $reverse]));
	}
	
	/**
	 * Сортирует коллекцию по атрибуту
	 * 
	 * @param string $attribute Сортируемый атрибут
	 * @param boolean $reverse [optional] Сортировать в обратном порядке. По умолчанию: false
	 * @return Collection Отсортированная коллекция
	 */
	public function sortByAttribute($attribute, $reverse = false)
	{
		return $this->sort(new Comparator(['attribute' => $attribute, 'reverse' => $reverse]));
	}
	
	/**
	 * Находит минимальный элемент в коллекции
	 * 
	 * @param Comparator|Closure|callable|string $comparator Сравниватель элементов
	 * @return object Элемент или null если коллекция пустая
	 */
	public function min($comparator)
	{
		if (!($comparator instanceof Comparator))
		{
			$comparator = new Comparator(['comparator' => $comparator]);
		}
		
		$comparator->reverse = !$comparator->reverse;
		return $this->max($comparator);
	}
	
	/**
	 * Находит максимальный элемент в коллекции
	 * 
	 * @param Comparator|Closure|callable|string $comparator Сравниватель элементов
	 * @return object Элемент или null если коллекция пустая
	 */
	public function max($comparator)
	{
		if ($this->getIsEmpty())
		{
			return null;
		}
		
		$max = $this->itemAt(0);
		$iterator = $this->getIterator();
		$iterator->next(); // т.к. первый элемент мы уже приняли максимумом
		
		while ($iterator->valid())
		{
			$current = $iterator->current();
			
			if (call_user_func($comparator, $current, $max) > 0)
			{
				$max = $current;
			}
			
			$iterator->next();
		}
		
		return $max;
	}
	
	/**
	 * Возвращает указанное свойство каждого элемента коллекции
	 * 
	 * @param string $attribute Необходимый атрибут
	 * @return array Свойство всех элементов коллекции
	 */
	public function getValues($attribute)
	{
		return $this->map(function ($object) use ($attribute)
		{
			return $object->{$attribute};
		});
	}
	
	/**
	 * Задает данные, которые должны быть сериализованы в JSON.
	 * Реализация интерфейса {@see JsonSerializable}.
	 * 
	 * @return array Список элементов
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}
}
