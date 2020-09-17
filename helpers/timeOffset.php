<?php

/**
 * Форматтер времени
 */
class timeOffset
{
	/**
	 * Размерность часа
	 */
	const hourDimension = 3600;
	
	/**
	 * Размерность минуты
	 */
	const minuteDimension = 60;
	
	/**
	 * Размерность секунды
	 */
	const secondDimension = 1;
	
	/**
	 * Целый час
	 */
	const fullHour = 3600;
	
	/**
	 * Пол часа
	 */
	const halfHour = 1800;
	
	/**
	 * Четверть часа
	 */
	const quarterHour = 900;
	
	/**
	 * Треть часа
	 */
	const thirdHour = 1200;
	
	/**
	 * Дефолтный строковый формат
	 */
	const defaultFormat = '%02d:%02d';
	
	/**
	 * Значение
	 * @var int
	 */
	protected $_val;
	
	/**
	 * Конструктор
	 * 
	 * @param mixed $val Число или время
	 * @return \timeOffset
	 */
	public function __construct($val)
	{
		$this->_val = static::parse($val);
		return $this;
	}
	
	/**
	 * Магический метод GET
	 * 
	 * @param string $var Интересующие данные
	 * @return mixed Результат
	 */
	public function __get($var)
	{
		switch (strToLower($var))
		{
			case 'val':
			case 'num':
			case 'int':
			case 'integer':
				return $this->num();
			
			case 'text':
			case 'format':
				return $this->format();
			
			case 'hour':
				return $this->hour();
			
			case 'min':
			case 'minute':
				return $this->minute();
			
			case 'sec':
			case 'second':
				return $this->second();
		}
	}
	
	/**
	 * Магический метод toString
	 * @return string Отформатированное время
	 */
	public function __toString()
	{
		return $this->format();
	}
	
	/**
	 * Возвращает новый экземпляр объекта этого класса
	 * 
	 * @param mixed $val Число или время
	 * @return \timeOffset
	 */
	public static function instance($val)
	{
		return new self($val);
	}
	
	/**
	 * Добавить время
	 * 
	 * @param mixed $val Время
	 * @return \timeOffset
	 */
	public function add($val)
	{
		$this->_val += static::parse($val);
		return $this;
	}
	
	/**
	 * Вычесть время
	 * 
	 * @param mixed $val Время
	 * @return \timeOffset
	 */
	public function sub($val)
	{
		$this->_val -= static::parse($val);
		return $this;
	}
	
	/**
	 * Устанавливает время
	 * 
	 * @param mixed $val Число или время
	 */
	public function set($val)
	{
		$this->_val = static::parse($val);
		return $this;
	}
	
	/**
	 * Аппроксимирует время в меньшую сторону
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @param int $precision [optional] Точность (default: self::hourDimension)
	 * @return int Время в числовом формате
	 */
	public function floor($precision = self::hourDimension)
	{
		return static::floorStatic($this->_val, $precision);
	}
	
	/**
	 * Аппроксимирует время в большую сторону
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @param int $precision [optional] Точность (default: self::hourDimension)
	 * @return int Время в числовом формате
	 */
	public function ceil($precision = self::hourDimension)
	{
		return static::ceilStatic($this->_val, $precision);
	}
	/**
	 * Аппроксимирует время в меньшую сторону
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @param int $precision [optional] Точность (default: self::hourDimension)
	 * @return int Время в числовом формате
	 */
	public function selfFloor($precision = self::hourDimension)
	{
		return $this->set(static::floorStatic($this->_val, $precision));
	}
	
	/**
	 * Аппроксимирует время в большую сторону
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @param int $precision [optional] Точность (default: self::hourDimension)
	 * @return int Время в числовом формате
	 */
	public function selfCeil($precision = self::hourDimension)
	{
		return $this->set(static::ceilStatic($this->_val, $precision));
	}
	
	/**
	 * Парсит время
	 * 
	 * @param mixed $val Число или время
	 * @return int Время в числовом формате
	 */
	public static function parse($val)
	{
		if (is_numeric($val))
		{
			return (int) $val;
		}
		
		if (preg_match('/^(?:\d+.\d+.\d+.)?(\d+).(\d+)(?:.(\d+))?$/', $val, $match))
		{
			$r = $match[1] * static::hourDimension + $match[2] * static::minuteDimension;
			if (isset($match[3]))
			{
				$r += $match[3];
			}
			return $r;
		}
		
		return 0;
	}
	
	/**
	 * Возвращает время в числовом формате
	 * 
	 * @return int
	 */
	public function num()
	{
		return $this->_val;
	}
	
	/**
	 * Форматирует время
	 * 
	 * @param string $format [optional] Формат для printf() (default: self::defaultFormat)
	 * @return string Отформатированное время
	 */
	public function format($format = self::defaultFormat)
	{
		return static::formatStatic($this->_val, $format);
	}
	
	/**
	 * Возвращает час
	 * 
	 * @return int
	 */
	public function hour()
	{
		return static::hourStatic($this->_val);
	}
	
	/**
	 * Возвращает минуту
	 * 
	 * @return int
	 */
	public function minute()
	{
		return static::minuteStatic($this->_val);
	}
	
	/**
	 * Возвращает секунду
	 * 
	 * @return int
	 */
	public function second()
	{
		return static::secondStatic($this->_val);
	}
	
	/**
	 * Возвращает время в числовом формате
	 * 
	 * @param mixed $val Значение времени
	 * @return int
	 */
	public static function numStatic($val)
	{
		return is_numeric($val)
			? (int) $val
			: static::parse($val);
	}
	
	/**
	 * Разница м\у часами
	 * 
	 * @param int $start Начальное время
	 * @param int $finish Конечное время
	 * @return float Кол-во часов
	 */
	public static function countStatic($start, $finish)
	{
		return abs(($finish - $start) / self::hourDimension);
	}
	
	/**
	 * Доля часа
	 * 
	 * @param int $val Время
	 * @return float Доля
	 */
	public static function percentStatic($val)
	{
		return $val / self::hourDimension;
	}
	
	/**
	 * Форматирует время
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @param string $format [optional] Формат для printf() (default: self::defaultFormat)
	 * @return string Отформатированное время
	 */
	public static function formatStatic($val, $format = self::defaultFormat)
	{
		return sprintf($format, static::hourStatic($val), static::minuteStatic($val), static::secondStatic($val));
	}
	
	/**
	 * Возвращает час
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @return int
	 */
	public static function hourStatic($val)
	{
		return floor($val / static::hourDimension);
	}
	
	/**
	 * Возвращает минуту
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @return int
	 */
	public static function minuteStatic($val)
	{
		return floor(($val % static::hourDimension) / static::minuteDimension);
	}
	
	/**
	 * Возвращает секунду
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @return int
	 */
	public static function secondStatic($val)
	{
		return $val % static::minuteDimension;
	}
	
	/**
	 * Аппроксимирует время в меньшую сторону
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @param int $precision [optional] Точность (default: self::hourDimension)
	 * @return int Время в числовом формате
	 */
	public static function floorStatic($val, $precision = self::hourDimension)
	{
		return (int) floor($val / $precision) * $precision;
	}
	
	/**
	 * Аппроксимирует время в большую сторону
	 * 
	 * @param int $val Значение времени в числовом формате
	 * @param int $precision [optional] Точность (default: self::hourDimension)
	 * @return int Время в числовом формате
	 */
	public static function ceilStatic($val, $precision = self::hourDimension)
	{
		return (int) ceil($val / $precision) * $precision;
	}
}