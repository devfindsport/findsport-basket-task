<?php

/**
 * Расширение стандартного функционала DateTimeZone
 */
class FDateTimeZone extends DateTimeZone
{
	/**
	 * Размер оффсета в секундах
	 */
	const sizeSeconds = 3600;
	
	/**
	 * Размер оффсета в минутах
	 */
	const sizeMinutes = 60;
	
	/**
	 * Размер оффсета в часах
	 */
	const sizeHour = 1;
	
	/**
	 * Создать экземпляр класса
	 * 
	 * @param string $timezone Временная зона
	 * @return \FDateTimeZone
	 */
	public static function instance($timezone)
	{
		return new static($timezone);
	}
	
	/**
	 * Создать экземпляр временноый зоны из оффсета
	 * 
	 * @param integer $offset Оффсет
	 * @param integer $size Размерность оффсета. По умолчанию: 3600 секунд
	 * @return \FDateTimeZone
	 */
	public static function createFromOffset($offset, $size = self::sizeSeconds)
	{
		$sign = ($offset < 0) ? '+' : '-'; // Нумерация в GMT идет наоборот
		$hour = abs(floor($offset / $size));
		$timezone = 'Etc/GMT' . $sign . $hour;
		return new static($timezone);
	}
	
	/**
	 * Создаёт экземпляр временной зоны по-умолчанию
	 * 
	 * @return \FDateTimeZone
	 */
	public static function createDefault()
	{
		return new static(date_default_timezone_get());
	}
}