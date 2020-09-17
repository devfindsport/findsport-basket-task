<?php
/**
 * Объект интервала времени, основанного на шаге расписания.
 */

namespace app\components\basket\interval_step;

class IntervalStep
{
	/**
	 * @const int Время свободно для бронирования в данном расписании площадки
	 */
	const STATE_FREE = 0;
	
	/**
	 * @const int Время занято для бронирования в данном расписании площадки
	 */
	const STATE_BUSY = 1;
	
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
	 * @var int Статус занятости интервала существующим временем в данном расписании
	 */
	public $state;
	
	/**
	 * IntervalStep constructor.
	 *
	 * @param int $schedule
	 * @param int $start
	 * @param int $finish
	 * @param int $state
	 */
	public function __construct($schedule, $start, $finish, $state)
	{
		$this->schedule_id = $schedule;
		$this->start = $start;
		$this->finish = $finish;
		$this->state = $state;
	}
	
	/**
	 * Получить размерность шага
	 *
	 * @return int
	 */
	public function getStep()
	{
		return $this->finish - $this->start;
	}
	
	/**
	 * Является ли интервал занятым?
	 *
	 * @return bool
	 */
	public function isStateBusy()
	{
		return $this->state === self::STATE_BUSY;
	}
}