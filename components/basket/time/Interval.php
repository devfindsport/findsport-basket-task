<?php
/**
 * Временной интервал для объекта времени
 *
 * @see IntervalTest
 */

namespace app\components\basket\time;

class Interval
{
	/**
	 * @var string $date Y-m-d
	 */
	private $date;
	
	/**
	 * @var \FDateTime|null объект времени, основанный на date
	 */
	private $dateTime;
	
	/**
	 * @var int $timeStart Начало интервала времени, создаваемоего времени (offset в секундах с начала дня)
	 */
	private $offsetStart;
	
	/**
	 * @var int $timeFinish Окончание интервала
	 */
	private $offsetFinish;
	
	/**
	 * @var \FDateTimeZone $timeZone Временная зона интервала
	 */
	private $timeZone;
	
	/**
	 * Interval constructor.
	 *
	 * @param string $date Y-m-d
	 * @param int $offsetStart Начало интервала времени, создаваемоего времени (offset в секундах с начала дня)
	 * @param int $offsetFinish Окончание интервала
	 * @param \FDateTimeZone $timeZone Временная зона интервала
	 */
	public function __construct($date, $offsetStart, $offsetFinish, \FDateTimeZone $timeZone)
	{
		self::ensureDateIsValid($date);
		self::ensureOffsetValid($offsetStart);
		self::ensureOffsetValid($offsetFinish);
		self::ensureStepIsValid($offsetStart);
		self::ensureStepIsValid($offsetFinish);
		self::ensureOffsetsIsValid($offsetStart, $offsetFinish);
		
		$this->date = (string) $date;
		$this->offsetStart = (int) $offsetStart;
		$this->offsetFinish = (int) $offsetFinish;
		$this->timeZone = $timeZone;
	}
	
	/**
	 * Получить дату в формате Y-m-d
	 *
	 * @return string
	 */
	public function getDate()
	{
		return $this->date;
	}
	
	/**
	 * Получить время начала в кол-ве секунд с начала дня
	 *
	 * @return integer
	 */
	public function getStart()
	{
		return $this->offsetStart;
	}
	
	/**
	 * Время конца
	 * @return integer
	 */
	public function getFinish()
	{
		return $this->offsetFinish;
	}
	
	/**
	 * Получить массив из интервалов, разделённых по шагам времени.
	 *
	 * @param int $step
	 *
	 * @return array
	 */
	public function getStepped($step)
	{
		$start = $this->getStart();
		$finish = $this->getFinish();
		$result = [];
		for ($i = $start; $i < $finish; $i += $step) {
			$result[] = [$i, $i + $step];
		}
		if (($finish - $start) % $step) {
			$result[count($result) - 1][1] = $finish;
		}
		
		return $result;
	}
	
	/**
	 * Получить unix timestamp начала
	 * @return integer
	 */
	public function getTimeStart()
	{
		return $this->getDateTimeStart()->getTimestamp();
	}
	
	/**
	 * Получить unix timestamp конца
	 * @return integer
	 */
	public function getTimeFinish()
	{
		return $this->getDateTimeFinish()->getTimestamp();
	}
	
	/**
	 * Получить временную зону интервала
	 *
	 * @return \FDateTimeZone
	 */
	public function getTimezone()
	{
		return $this->timeZone;
	}
	
	/**
	 * Получить DateTime от даты
	 *
	 * @return \FDateTime
	 */
	private function getDateTime()
	{
		if (!$this->dateTime) {
			$this->dateTime = new \FDateTime($this->date, $this->timeZone);
		}
		return $this->dateTime;
	}
	
	/**
	 * Получить объект времени начала интервала времени
	 *
	 * @return \FDateTime
	 */
	public function getDateTimeStart()
	{
		$hours = (int) ($this->offsetStart / 3600);
		$minutes = (int) ($this->offsetStart % 3600 / 60);
		
		$dateTime = $this->getDateTime()->copy();
		$dateTime->setTime($hours, $minutes, 0);
		
		return $dateTime;
	}
	
	/**
	 * Получить объект времени окончания интервала времени
	 *
	 * @return \FDateTime
	 */
	public function getDateTimeFinish()
	{
		$hours = (int) ($this->offsetFinish / 3600);
		$minutes = (int) ($this->offsetFinish % 3600 / 60);
		
		$dateTime = $this->getDateTime()->copy();
		$dateTime->setTime($hours, $minutes, 0);
		
		return $dateTime;
	}
	
	/**
	 * Получить смещение временной зоны
	 * @return integer
	 */
	public function getTimezoneOffset()
	{
		return $this->getDateTimeStart()->getOffset();
	}
	
	/**
	 * Месяц в формате yyyy-MM
	 *
	 * @return string
	 */
	public function getMonth()
	{
		return \FString::subStr($this->date, 0, 7);
	}
	
	/**
	 * Получить кол-во часов в интервале
	 * @return float
	 */
	public function getCount()
	{
		return \timeOffset::countStatic($this->offsetStart, $this->offsetFinish);
	}
	
	/**
	 * Удостовериться, что время интервала валидно
	 *
	 * @param integer $value
	 */
	protected static function ensureOffsetValid($value)
	{
		if (((is_string($value) && !ctype_digit($value))) || $value < 0 || $value > 24 * 3600) {
			throw new \InvalidArgumentException('Передано невалидное значение времени');
		}
	}
	
	/**
	 * Удостовериться, что время интервала валидно
	 *
	 * @param integer $start
	 * @param integer $finish
	 */
	protected static function ensureOffsetsIsValid($start, $finish)
	{
		if ($start == $finish || $finish <= $start) {
			throw new \InvalidArgumentException('Передано невалидное значение промежутка времени');
		}
	}
	
	/**
	 * Удостовериться, что дата валидна
	 *
	 * @param string $date
	 */
	protected static function ensureDateIsValid($date)
	{
		if (false === is_string($date) || false === \DateTime::createFromFormat('Y-m-d', $date)) {
			throw new \InvalidArgumentException('Передано невалидное значение даты');
		}
	}
	
	/**
	 * Удостовериться, что шаг интервала валидный
	 *
	 * @param integer $offset
	 */
	protected static function ensureStepIsValid($offset)
	{
		$minStep = 1800; // минимальный шаг расписания
		$value = $offset % $minStep;
		if (!in_array($value, [0, $minStep])) {
			throw new \InvalidArgumentException('Передано невалидное значение промежутка времени: неверный шаг расписания');
		}
	}
	
	/**
	 * Является ли интервал интервалов в текущем году?
	 *
	 * @return bool
	 */
	public function isCurrentYear()
	{
		return date('Y') == date('Y', $this->getTimeStart());
	}
	
	/**
	 * Покрывается ли искомый интервал данным интервалом?
	 *
	 * @param int $start Начало искомого интервала в секундах от начала дня
	 * @param int $finish Конец искомого интервала в секундах от начала дня
	 *
	 * @return bool
	 */
	public function isOffsetOverlap($start, $finish)
	{
		return self::isRangeOverlapRange($this->getStart(), $this->getFinish(), $start, $finish);
	}
	
	/**
	 * Прошёл ли интервал времени?
	 *
	 * @return bool
	 */
	public function isPassed()
	{
		return time() >= $this->getTimeStart();
	}
	
	/**
	 * Совпадает ли интервал 1 с интервалом 2?
	 *
	 * @param int $start1
	 * @param int $finish1
	 * @param int $start2
	 * @param int $finish2
	 *
	 * @return bool
	 */
	public static function isRangeOverlapRange($start1, $finish1, $start2, $finish2)
	{
		return !(($finish1 <= $start2) || ($finish2 <= $start1));
	}
	
	/**
	 * Установить время начала интервала в секундах от начала дня
	 *
	 * @param int $value
	 */
	public function setOffsetStart($value)
	{
		self::ensureOffsetValid($value);
		$this->offsetStart = (int) $value;
	}
	
	/**
	 * Установить время  интервала в секундах от начала дня
	 *
	 * @param int $value
	 */
	public function setOffsetFinish($value)
	{
		self::ensureOffsetValid($value);
		$this->offsetFinish = (int) $value;
	}
}