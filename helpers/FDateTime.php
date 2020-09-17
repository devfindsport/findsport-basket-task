<?php

/**
 * Перепиливание стандартного функционала DateTime
 */
class FDateTime extends DateTime
{
	/**
	 * Секунда
	 */
	const unitSecond = 1;
	
	/**
	 * Минута
	 */
	const unitMinute = 60;
	
	/**
	 * Час
	 */
	const unitHour = 3600;
	
	/**
	 * День
	 */
	const unitDay = 86400;
	
	/**
	 * Месяц (30 дней)
	 */
	const unitMonth = 259200;
	
	/**
	 * Год (365 дней)
	 */
	const unitYear = 31536000;
	
	/**
	 * Относительное время - начало текущего дня
	 */
	const relatedDayStart = 'midnight';
	
	/**
	 * Относительное время - начало текущего месяца
	 */
	const relatedMonthStart = 'first day of this month midnight';
	
	/**
	 * Относительное время - начало следующего месяца
	 */
	const relatedNextMonthStart = 'first day of next month midnight';
	
	/**
	 * Локаль "по умлочанию"
	 * @var FDateLocale
	 */
	protected static $_defaultLocale;
	
	/**
	 * Строковый формат вывода по умолчанию
	 * @var string
	 */
	protected static $_defaultFormat = 'ISO8601';
	
	/**
	 * Форматы форматирования
	 * @var array
	 */
	public static $formats = array(
		'ISO8601' => DateTime::ISO8601,
		'dateStamp' => 'Y-m-d',
		'timeStamp' => 'H:i:s',
		'dateTimeStamp' => 'Y-m-d H:i:s',
		'date' => 'd.m.Y',
		'time' => 'H:i',
		'dateTime' => 'd.m.Y в H:i',
		'dateWord' => 'j F Y',
		'dateTimeWord' => 'j F Y - H:i',
	);
	
	/**
	 * Форматы форматирования (тестовая версия)
	 * @var array
	 */
	public static $formats2 = array(
		'ISO8601' => '{yyyy}-{MM}-{dd}T{HH}:{mm}:{ss}{tzo iso}',
		'dateStamp' => '{yyyy}-{MM}-{dd}',
		'timeStamp' => '{HH}:{mm}:{ss}',
		'dateTimeStamp' => '{yyyy}-{MM}-{dd} {HH}:{mm}:{ss}',
		'date' => '{dd}.{MM}.{yyyy}',
		'time' => '{hours}:{minutes}',
		'dateTime' => '{dd}.{MM}.{yyyy} в {hours}:{minutes}',
		'dateWord' => '{d} {month ord} {year}',
		'dateTimeWord' => '{d} {month ord} {year} в {hours}:{minutes}',
	);
	
	/**
	 * Конструктор
	 * 
	 * @param mixed $time [optional] Unixtime или Timestamp, или относительное время. По умолчанию: текущее время
	 * @param mixed $timezone [optional] Временная зона в текстовом формате или DateTimeZone
	 */
	public function __construct($time = null, $timezone = null)
	{
		$timezone = static::_argTimezone($timezone);
		
		// Проверяем на Unixtime
		if (static::isTimestamp($time))
		{
			parent::__construct(null, $timezone);
			$this->setTimestamp($time);
		}
		else
		{
			parent::__construct($time, $timezone);
		}
	}
	
	/**
	 * Создать экземпляр класса
	 * 
	 * @param mixed $time [optional] Unixtime или Timestamp, или относительное время. По умолчанию: текущее время
	 * @param mixed $timezone [optional] Временная зона в текстовом формате или DateTimeZone
	 * @return \FDateTime
	 */
	public static function instance($time = null, $timezone = null)
	{
		return new static($time, $timezone);
	}
	
	/**
	 * Создать экземпляр класса из формата
	 * 
	 * @param string $format Формат
	 * @param string $time Соответствующее формату время
	 * @param mixed $timezone [optional] Временная зона
	 * @return FDateTime - возможно false при возникновении ошибки
	 */
	public static function createFromFormat($format, $time, $timezone = null)
	{
		$format = static::_argFormat($format);
		$timezone = static::_argTimezone($timezone);
		$dt = is_null($timezone)
				? parent::createFromFormat($format, $time)
				: parent::createFromFormat($format, $time, $timezone);
		return $dt ? new FDateTime($dt->getTimestamp(), $dt->getTimezone()) : false;
	}
	
	/**
	 * Создать из времени и добавить секундный оффсет
	 * 
	 * @example FDateTime::createFromTimeAndSeconds('1990-03-05', 7200, 'Europe/Moscow'); // Результат "1990-03-05 02:00:00 +0400"
	 * 
	 * @param mixed $time Время
	 * @param mixed $interval Интервал
	 * @param type $timezone [optional] Временная зона
	 * @return \FDateTime
	 */
	public static function createAndAddInterval($time, $interval, $timezone = null)
	{
		$dt = new FDateTime($time, $timezone);
		$dt->add($interval);
		return $dt;
	}
	
	/**
	 * Создать объект с заданным временем
	 * 
	 * @param integer $year Год
	 * @param integer $month Месяц (1-12)
	 * @param integer $day [optional] День месяца (1-31)
	 * @param integer $hours [optional] Час (0-23)
	 * @param integer $minutes [optional] Минута (0-59)
	 * @param integer $seconds [optional] Секунда (0-59)
	 * @param mixed $timezone [optional] Временная зона
	 * @return \FDateTime
	 */
	public static function createFromDateTime($year, $month, $day = 1, $hours = 0, $minutes = 0, $seconds = 0, $timezone = null)
	{
		$dt = new FDateTime(null, $timezone);
		$dt->setDate($year, $month, $day);
		$dt->setTime($hours, $minutes, $seconds);
		return $dt;
	}
	
	/**
	 * Создать экземпляр из системного DateTime
	 * 
	 * @param DateTime $dateTime DateTime
	 * @return \FDateTime
	 */
	public static function createFromInstance(DateTime $dateTime)
	{
		return new FDateTime($dateTime->getTimestamp(), $dateTime->getTimezone());
	}
	
	/**
	 * Получить локаль "по умолчанию"
	 * 
	 * @return \FDateLocale
	 */
	public static function getDefaultLocale()
	{
		return static::$_defaultLocale;
	}
	
	/**
	 * Установить локаль "по умолчанию"
	 * 
	 * @param \FDateLocale $locale
	 */
	public static function setDefaultLocale(FDateLocale $locale)
	{
		static::$_defaultLocale = $locale;
	}
	
	/**
	 * Определяет является ли указанное время отметкой времени
	 * 
	 * @param mixed $time
	 * @return boolean
	 */
	public static function isTimestamp($time)
	{
		return is_int($time) || ctype_digit($time);
	}
	
	/**
	 * Создать клон
	 * 
	 * @return \FDateTime
	 */
	public function copy()
	{
		return clone $this;
	}
	
	/**
	 * Полная информация о времени
	 * 
	 * @return \stdClass {year, month, date, hours, minutes, seconds, timezone, timezoneOffset, weekday, isSummerTime}
	 */
	public function getDateInfo($assoc = false)
	{
		$formatted = parent::format('Y n j H i s e Z w I U');
		$explode = explode(' ', $formatted);
		
		$result = [
			'year' => (int) $explode[0],
			'month' => (int) $explode[1],
			'date' => (int) $explode[2],
			
			'hours' => (int) $explode[3],
			'minutes' => (int) $explode[4],
			'seconds' => (int) $explode[5],
			
			'timezone' => $explode[6],
			'timezoneOffset' => (int) $explode[7],
			'weekday' => (int) $explode[8],
			'isSummerTime' => (bool) $explode[9],
			'unixtime' => (int) $explode[10],
		];
		
		return $assoc ? $result : (object) $result;
	}
	
	/**
	 * Год
	 * 
	 * @return integer
	 */
	public function getYear()
	{
		return (int) parent::format('Y');
	}
	
	/**
	 * Месяц (1 - 12)
	 * 
	 * @return integer
	 */
	public function getMonth()
	{
		return (int) parent::format('n');
	}
	
	/**
	 * Дата (1 - 31)
	 * 
	 * @return integer
	 */
	public function getDate()
	{
		return (int) parent::format('j');
	}
	
	/**
	 * Часы (0 - 23)
	 * 
	 * @return integer
	 */
	public function getHours()
	{
		return (int) parent::format('H');
	}
	
	/**
	 * Минуты (0 - 59)
	 * 
	 * @return integer
	 */
	public function getMinutes()
	{
		return (int) parent::format('m');
	}
	
	/**
	 * Секунды (0 - 59)
	 * 
	 * @return integer
	 */
	public function getSeconds()
	{
		return (int) parent::format('s');
	}
	
	/**
	 * День недели (0 - 6)<br>
	 * 0 - вс, 1 - пн, 2 - вт, 3 - ср, 4 - ст, 5 - пт, 6 - сб
	 * 
	 * @return integer
	 */
	public function getWeekday()
	{
		return (int) parent::format('w');
	}
	
	/**
	 * Выходной день?
	 * 
	 * @return boolean
	 */
	public function isWeekend()
	{
		return in_array($this->getWeekday(), array(0, 6));
	}
	
	/**
	 * Летнее время?
	 * 
	 * @return boolean
	 */
	public function isSummerTime()
	{
		return (bool) parent::format('I');
	}
	
	/**
	 * Кол-во юнитов "от" ...
	 * 
	 * @param mixed $since Время "от". Допустимые типы: DateTime или string
	 * @param integer $units Размер юнита (см. константы FDateTime::unit*)
	 * @return integer
	 */
	public function unitsSince($since, $units)
	{
		$date2 = ($since instanceof DateTime) ? $since : $this->copy()->modify($since);
		$diff = $this->getTimestamp() - $date2->getTimestamp();
		
		return floor($diff / $units);
	}
	
	/**
	 * Кол-во секунд "от" ..
	 * 
	 * @param mixed $since Время "от". Допустимые типы: DateTime или string
	 * @return integer
	 */
	public function secondsSince($since)
	{
		return $this->unitsSince($since, static::unitSecond);
	}
	
	/**
	 * Кол-во минут "от" ..
	 * 
	 * @param mixed $since Время "от". Допустимые типы: DateTime или string
	 * @return integer
	 */
	public function minutesSince($since)
	{
		return $this->unitsSince($since, static::unitMinute);
	}
	
	/**
	 * Кол-во часов "от" ..
	 * 
	 * @param mixed $since Время "от". Допустимые типы: DateTime или string
	 * @return integer
	 */
	public function hoursSince($since)
	{
		return $this->unitsSince($since, static::unitHour);
	}
	
	/**
	 * Добавляет заданное количество дней, месяцев, лет, часов, минут и секунд к объекту DateTime
	 * 
	 * @param DateInterval|string $interval Временной интервал
	 * @return FDateTime
	 */
	public function add($interval)
	{
		return parent::add($this->_ensureDateInterval($interval));
	}
	
	/**
	 * Вычитает заданное количество дней, месяцев, лет, часов, минут и секунд из времени объекта DateTime
	 * 
	 * @param DateInterval|string $interval Временной интервал
	 * @return FDateTime
	 */
	public function sub($interval)
	{
		return parent::sub($this->_ensureDateInterval($interval));
	}
	
	/**
	 * Добавить секунды
	 * 
	 * @param integer $val Кол-во секунд
	 * @return \FDateTime
	 * 
	 * @deprecated Необходимо использовать add() и DateInterval
	 */
	public function addSeconds($val)
	{
		$this->setTimestamp($this->getTimestamp() + $val);
		return $this;
	}
	
	/**
	 * Добавить минуты
	 * 
	 * @param integer $val Кол-во минут
	 * @return \FDateTime
	 * 
	 * @deprecated Необходимо использовать add() и DateInterval
	 */
	public function addMinutes($val)
	{
		return $this->addSeconds($val * 60);
	}
	
	/**
	 * Добавить часы
	 * 
	 * @param integer $val Кол-во часов
	 * @return \FDateTime
	 * 
	 * @deprecated Необходимо использовать add() и DateInterval
	 */
	public function addHours($val)
	{
		return $this->addSeconds($val * 3600);
	}
	
	/**
	 * Добавить дни
	 * 
	 * @param integer $val Кол-во дней
	 * @return \FDateTime
	 * 
	 * @deprecated Необходимо использовать add() и DateInterval. Может быть баг с временными зонами
	 */
	public function addDays($val)
	{
		return $this->addSeconds($val * 86400);
	}
	
	/**
	 * Вычислить относительное время
	 * 
	 * @param mixed $relation Отношение к текущему времени. Доступные типы: DateInterval или string
	 * @return \FDateTime
	 */
	public function related($relation)
	{
		$newDateTime = clone $this;
		
		($relation instanceof DateInterval)
			? $newDateTime->add($relation)
			: $newDateTime->modify($relation);
		
		return $newDateTime;
	}
	
	/**
	 * Форматировать время
	 * 
	 * @param string $format [optional] Формат вывода. По умолчанию: `defaultFormat`
	 * @param mixed $toTimezone [optional] Временная зона. По умолчанию: текущая временная зона
	 * @return string
	 */
	public function format($format = null, $toTimezone = null)
	{
		$format = static::_argFormat($format);
		$toTimezone = static::_argTimezone($toTimezone);
		$fromTimezone = null;
		$changeTimezone = false;
		
		if ($toTimezone)
		{
			$fromTimezone = $this->getTimezone();
			$changeTimezone = $toTimezone && $toTimezone->getName() !== $fromTimezone->getName();
		}
		
		$changeTimezone && $this->setTimezone($toTimezone);
		$formatted = parent::format($format);
		$changeTimezone && $this->setTimezone($fromTimezone);
		
		return $formatted;
	}
	
	/**
	 * Форматировать время (тестовая версия)
	 * 
	 * @param string $format [optional] Формат вывода. По умолчанию: `defaultFormat`
	 * @param mixed $toTimezone [optional] Временная зона. По умолчанию: текущая временная зона
	 * @return string
	 */
	public function format2($format = null, $toTimezone = null)
	{
		$format = static::_argFormat($format, 2);
		$toTimezone = static::_argTimezone($toTimezone);
		$fromTimezone = null;
		$changeTimezone = false;
		
		if ($toTimezone)
		{
			$fromTimezone = $this->getTimezone();
			$changeTimezone = $toTimezone && $toTimezone->getName() !== $fromTimezone->getName();
		}
		
		$changeTimezone && $this->setTimezone($toTimezone);
		$formatted = $this->_format2($format);
		$changeTimezone && $this->setTimezone($fromTimezone);
		
		return $formatted;
	}
	
	/**
	 * Форматировать время (код для тестовой версии)
	 * 
	 * @param string $format Формат вывода
	 * @return null|string
	 */
	protected function _format2($format)
	{
		$info = $this->getDateInfo(true);
		$formatParams = $this->_formatParams();
		$locale = static::getDefaultLocale();
		
		// Перебираем строку на наличие вхождений вида "{month}" или "{Month ord}"
		return preg_replace_callback('/\{(\w+)(?:\s([\w\,]+))?\}/u', function ($match) use ($info, $formatParams, $locale)
		{
			// Аргументы вызова
			$param = $match[1];
			$paramLower = strToLower($param);
			$modifiers = isset($match[2]) ? explode(',', $match[2]) : array();
			
			// Ищем подходящий формат
			$formatParam = null;
			
			if (isset($formatParams[$param]))
			{
				$formatParam = $formatParams[$param];
			}
			elseif (!empty($formatParams[$paramLower]['ci'])) // Нечувствительные к регистру форматы
			{
				$formatParam = $formatParams[$paramLower];
				
				// Добавляем регистрозависимые модификаторы
				if (ctype_upper($param))
				{
					$modifiers[] = 'toUpperCase';
				}
				else if (ctype_upper($param[0]))
				{
					$modifiers[] = 'capitalize';
				}
			}
			
			// Ничего не меняем если формат не найден
			if (!$formatParam)
			{
				return $match[0];
			}
			
			// Добавляем обязательные модификаторы
			if (isset($formatParam['modifiers']))
			{
				$modifiers = array_merge($modifiers, $formatParam['modifiers']);
			}
			
			// Обрабатываем результат
			$key = $formatParam[0];
			$value = $info[$key];
			
			if (in_array('locale', $modifiers))
			{
				return $locale->get($key, $value, $modifiers);
			}
			elseif (in_array('padToWidth', $modifiers))
			{
				return Number::padToWidth($value, 2);
			}
			elseif (in_array('truncateToWidth', $modifiers))
			{
				$valueLength = FString::strLen($value);
				$paramLength = FString::strLen($param);
				if ($valueLength > $paramLength)
				{
					return FString::subStr($value, $valueLength - $paramLength, $paramLength);
				}
				else
				{
					return $value;
				}
			}
			else
			{
				return $value;
			}
			
		}, $format);
	}
	
	/**
	 * Доступные форматы для форматирования
	 * 
	 * @return array
	 */
	protected function _formatParams()
	{
		$params = array(
			'd' => array('date'),
			'dd' => array('date', 'modifiers' => array('padToWidth')),
			'M' => array('month'),
			'MM' => array('month', 'modifiers' => array('padToWidth')),
			'yyyy' => array('year'),
			'yy' => array('year', 'modifiers' => array('truncateToWidth')),
			'w' => array('weekday'),

			'H' => array('hours'),
			'HH' => array('hours', 'modifiers' => array('padToWidth')),
			'm' => array('minutes'),
			'mm' => array('minutes', 'modifiers' => array('padToWidth')),
			's' => array('seconds'),
			'ss' => array('seconds', 'modifiers' => array('padToWidth')),

			'month' => array('month', 'ci' => true, 'modifiers' => array('locale')),
			'mon' => array('month', 'ci' => true, 'modifiers' => array('locale', 'short')),
			'weekday' => array('weekday', 'ci' => true, 'modifiers' => array('locale')),
			'wd' => array('weekday', 'ci' => true, 'modifiers' => array('locale', 'short')),

			'tz' => array('timezone'),
			'tzo' => array('timezoneOffset', 'modifiers' => array('locale')),
		);
		
		$params['year'] = $params['yyyy'];
		$params['hours'] = $params['HH'];
		$params['minutes'] = $params['min'] = $params['mm'];
		$params['seconds'] = $params['sec'] = $params['ss'];
		
		return $params;
	}
	
	/**
	 * Преобразовать аргумент "Временная зона"
	 * 
	 * @param mixed $timezone Временная зона из аргументов
	 * @return \DateTimeZone
	 */
	protected static function _argTimezone($timezone)
	{
		if ($timezone instanceof DateTimeZone) return $timezone;
		if (is_int($timezone) || ctype_digit($timezone)) return FDateTimeZone::createFromOffset($timezone);
		if (is_string($timezone)) return new FDateTimeZone($timezone);
		
		return null;
	}
	
	/**
	 * Преобразовать аргумент "Временной интервал"
	 * 
	 * @param mixed $interval Временной интервал
	 * @return \DateInterval|null
	 */
	protected static function _argInterval($interval)
	{
		if ($interval instanceof DateInterval) return $interval;
		if (is_int($interval) || ctype_digit($interval)) return new DateInterval('PT' . $interval . 'S');
		if (is_string($interval)) return new DateInterval($interval);
		
		return null;
	}
	
	/**
	 * Обработать аргумент "Формат времени"
	 * 
	 * @param mixed $format Формат времени из аргументов
	 * @param integer $v [optional] Версия форматтера. По умолчанию: 1
	 * @return string
	 */
	protected static function _argFormat($format, $v = 1)
	{
		if (is_null($format)) $format = static::$_defaultFormat;
		
		if ($v === 1 && array_key_exists($format, static::$formats)) return static::$formats[$format];
		if ($v === 2 && array_key_exists($format, static::$formats2)) return static::$formats2[$format];
		
		return $format;
	}
	
	/**
	 * Получить необходимые данные о текущем времени
	 * 
	 * @param string $format Необходимые данные, например YmdHis
	 * @return array
	 */
	protected function _dateInfo($format)
	{
		$formats = str_split($format);
		$formatted = parent::format(implode(' ', $formats));
		$values = explode(' ', $formatted);
		
		return array_combine($formats, $values);
	}
	
	/**
	 * Проверит и вернёт DateInterval
	 * 
	 * @param DateInterval|string $interval Временной интервал
	 * @return DateInterval
	 */
	protected function _ensureDateInterval($interval)
	{
		return ($interval instanceof DateInterval) ? $interval : new DateInterval($interval);
	}
}

/**
 * Локализация для DateTime
 * 
 * @author Vladimir Bogdanov <wext1990@gmail.com>
 * @date 2014-07-30
 */
class FDateLocale
{
	/**
	 * Текущая локаль
	 * 
	 * @var array
	 */
	protected $_locale = array(
		'code' => 'ru',
		
		'weekday'      => array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'),
		'weekdayShort' => array('вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'),
		'month'        => array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'),
		'monthOrd'     => array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'),
        'monthPrepos'  => array('январе', 'феврале', 'марте', 'апреле', 'мае', 'июне', 'июле', 'августе', 'сентябре', 'октябре', 'ноябре', 'декабре'),
		'monthShort'   => array('янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'),
	);
	
	/**
	 * Получить значение по названию
	 * 
	 * @param string $name Необходимая инфа
	 * @param mixed $val Значение
	 * @param array $modifiers [optional] Модификаторы
	 * @return string
	 */
	public function get($name, $val, array $modifiers = array())
	{
		switch ($name)
		{
			case 'weekday': return $this->getWeekday($val, $modifiers);
			case 'month': return $this->getMonth($val, $modifiers);
			case 'timezoneOffset': return $this->getTimezoneOffset($val, $modifiers);
		}
		
		return '';
	}
	
	/**
	 * Название дня недели
	 * <p>Доступные модификаторы:
	 * <ul>
	 *   <li>toUpperCase
	 *   <li>camelize
	 *   <li>short - сокращение
	 * </ul>
	 * 
	 * @param integer $val Порядковый номер месяца 1-12
	 * @param array $modifiers [optional] Модификаторы
	 * @return string
	 */
	public function getWeekday($val, array $modifiers = array())
	{
		$key = in_array('short', $modifiers) ? 'weekdayShort' : 'weekday';
		return $this->_applyModifiers($this->_locale[$key][$val], $modifiers);
	}
	
	/**
	 * Название месяца
	 * <p>Доступные модификаторы:
	 * <ul>
	 *   <li>toUpperCase
	 *   <li>camelize
	 *   <li>short - сокращение
	 *   <li>ord - в родительном подеже
	 * </ul>
	 * 
	 * @param integer $val Порядковый номер месяца 1-12
	 * @param array $modifiers [optional] Модификаторы
	 * @return string Например, январь или Январь, или Ян
	 */
	public function getMonth($val, array $modifiers = array())
	{
		if (in_array('short', $modifiers)) $key = 'monthShort';
		elseif (in_array('ord', $modifiers)) $key = 'monthOrd';
        elseif (in_array('prepos', $modifiers)) $key = 'monthPrepos';
		else $key = 'month';
		
		return $this->_applyModifiers($this->_locale[$key][$val - 1], $modifiers);
	}
	
	/**
	 * Смещение временной зоны
	 * <p>Доступные модификаторы:
	 * <ul>
	 *   <li>iso - ISO8601 формат
	 * </ul>
	 * 
	 * @param integer $val Порядковый номер месяца 1-12
	 * @param array $modifiers [optional] Модификаторы
	 * @return string Например, +04:00 или +0400
	 */
	public function getTimezoneOffset($val, array $modifiers = array())
	{
		$sign = ($val < 0) ? '-' : '+';
		$hours = floor(abs($val) / 3600);
		$minutes = floor(abs($val) % 3600 / 60);
		$separator = in_array('iso', $modifiers) ? '' : ':';
		
		return sprintf('%s%02d%s%02d', $sign, $hours, $separator, $minutes);
	}
	
	/**
	 * Применить модификаторы "по умолчанию"
	 * <p>Доступные модификаторы
	 * <ul>
	 *   <li>capitalize
	 *   <li>toUpperCase
	 * </ul>
	 * 
	 * @param string $val Значение
	 * @param array $modifiers Модификаторы
	 * @return string
	 */
	protected function _applyModifiers($val, array $modifiers)
	{
		if (in_array('capitalize', $modifiers))
		{
			return FString::capitalize($val);
		}
		
		if (in_array('toUpperCase', $modifiers))
		{
			return FString::toUpperCase($val);
		}
		
		return $val;
	}
}

// Устанавливаем дефолтную локаль
FDateTime::setDefaultLocale(new FDateLocale());