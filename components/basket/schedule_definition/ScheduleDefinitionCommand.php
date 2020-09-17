<?php
/**
 * Команда по определению наиболее подходящего расписания для определённого интервала
 */

namespace app\components\basket\schedule_definition;

use app\components\basket\interval_step\IntervalStepRepository;
use app\components\basket\time\Interval;

class ScheduleDefinitionCommand
{
	/**
	 * @var Interval
	 */
	private $interval;
	
	/**
	 * @var IntervalStepRepository
	 */
	private $repository;
	
	/**
	 * @var int
	 */
	private $step;
	
	/**
	 * ScheduleDefinitionCommand constructor.
	 *
	 * @param Interval $interval Интервал, для которого нужно определить расписание
	 * @param IntervalStepRepository $repository Репозиторий с занятым временем
	 * @param int $scheduleStep Шаг расписания (3600 или 1800 секунд)
	 */
	public function __construct(Interval $interval, IntervalStepRepository $repository, $scheduleStep)
	{
		$this->interval = $interval;
		$this->repository = $repository;
		$this->step = (int)$scheduleStep;
	}
	
	/**
	 * @return int ID расписания
	 */
	public function execute()
	{
		if ($this->step === 3600) {
			$principles = new SmallestClientBustlePrinciple(new GoodNeighborsPrinciple());
		} else {
			$principles = new SmallestClientBustlePrinciple(new AvoidingBlackHolesPrinciple(new GoodNeighborsPrinciple()));
		}
		
		$schedules = $principles->handle($this->interval, $this->repository);
		return array_shift($schedules);
	}
}