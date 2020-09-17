<?php
/**
 * Интервал времени, полученный из существующих ScheduleHour
 * Используется для удобного поиска по занятыми часам при опеределнии состояния занятости добавляемого времени
 *
 * @see ScheduleHour
 */

namespace app\components\basket\schedule_interval;

class ScheduleInterval
{
	/**
	 * @var int ID расписания площадки
	 */
	public $schedule_id;
	
	/**
	 * @var int Время начала интервала в секундах от начала дня
	 */
	public $start;
	
	/**
	 * @var int Время завершения интервала в секундах от начала дня
	 */
	public $finish;
	
	/**
	 * @var int Занятая доля на площадке интервалом
	 */
	public $weight;
	
	/**
	 * ScheduleInterval constructor.
	 *
	 * @param int $schedule
	 * @param int $start
	 * @param int $finish
	 * @param int $weight
	 */
	public function __construct($schedule, $start, $finish, $weight)
	{
		$this->schedule_id = (int) $schedule;
		$this->start = (int) $start;
		$this->finish = (int) $finish;
		$this->weight = (int) $weight;
	}
}