<?php

/**
 * Функции для работы со строками
 */
class FString
{
	/**
	 * Перевести все символы в нижний регистр
	 * 
	 * @param string $value Преобразуемая строка
	 * @return string
	 */
	public static function toLowerCase($value)
	{
		return mb_strtolower($value);
	}
	
	/**
	 * Перевести все символы в верхний регистр
	 * 
	 * @param string $value Преобразуемая строка
	 * @return string
	 */
	public static function toUpperCase($value)
	{
		return mb_strtoupper($value);
	}
	
	/**
	 * Перевести первый символ в верхний регистр
	 * 
	 * @param string $value Преобразуемая строка
	 * @return string
	 */
	public static function capitalize($value, $forceToLower = false)
	{
		$firstChar = static::toUpperCase(static::subStr($value, 0, 1));
		$other = static::subStr($value, 1);
		return $firstChar . ($forceToLower ? static::toLowerCase($other) : $other);
	}
	
	/**
	 * Получает длину строки
	 *
	 * @param string $value Строка, для которой измеряется длина.
	 * @return integer
	 */
	public static function strLen($value)
	{
		return mb_strlen($value);
	}
	
	/**
	 * Обрезать строку
	 * 
	 * @param string $value Обрезаемая строка
	 * @param int $start Позиция первого символа
	 * @param int $length [optional] Максимальное кол-во символов
	 * @return string
	 */
	public static function subStr($value, $start, $length = null)
	{
		return call_user_func_array('mb_substr', func_get_args());
	}
}
