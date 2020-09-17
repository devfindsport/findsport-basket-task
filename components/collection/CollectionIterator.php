<?php

/**
 * Переборщик коллекции
 */
class CollectionIterator implements Iterator
{
	/**
	 * @var Collection Итерируемая коллекция
	 */
	protected $_collection;
	/**
	 * @var array Итерируемые данные коллекции (темповая переменная для "инкапсуляции" итерации)
	 */
	protected $_data;
	/**
	 * @var integer Индекс текущего элемента
	 */
	protected $_index;
	/**
	 * @var integer Количество итерируемых элементов
	 */
	protected $_count;
	/**
	 * @var boolean Остановлена ли итерация
	 */
	protected $_stopped = false;

	/**
	 * Конструктор
	 * 
	 * @param Collection $collection Итерируемая коллекция
	 */
	public function __construct(Collection $collection)
	{
		$this->_collection = $collection;
		$this->rewind();
	}
	
	/**
	 * Сбрасывает курсор текущего элемента коллекции.
	 */
	public function rewind()
	{
		$this->_data = $this->_collection->toArray();
		$this->_index = 0;
		$this->_count = count($this->_data);
		$this->_stopped = false;
	}

	/**
	 * Возвращает индекс текущего элемента коллекции
	 * 
	 * @return integer Индекс текущего элемента
	 */
	public function key()
	{
		return $this->_index;
	}

	/**
	 * Возвращает текущий элемент коллекции
	 * 
	 * @return mixed Текущий элемент
	 */
	public function current()
	{
		return $this->_data[$this->_index];
	}

	/**
	 * Передвигает курсор на следующий элемент
	 */
	public function next()
	{
		$this->_index++;
	}

	/**
	 * Проверяет существует ли элемент в текущей позиции и можно ли продолжить перебор.
	 * 
	 * @return boolean
	 */
	public function valid()
	{
		return !$this->_stopped && $this->_index < $this->_count;
	}
	
	/**
	 * Останавливает перебор
	 */
	public function stop()
	{
		$this->_stopped = true;
	}
	
	/**
	 * Возвращает перебируемую коллекцию
	 * 
	 * @return Collection
	 */
	public function getCollection()
	{
		return $this->_collection;
	}
	
	/**
	 * Возвращает состояние "остановлен ли перебор"
	 * 
	 * @return boolean true если перебор остановлен, false если можно продолжать перебор
	 */
	public function getIsStopped()
	{
		return $this->_stopped;
	}
}
