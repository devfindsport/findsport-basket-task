<?php
/**
 * Класс-итератор для работы с объектам IntervalStep.
 *
 * Хранит в себе информацию об идентификаторе расписания, к которому относятся объект IntervalStep,
 */

namespace app\components\basket\interval_step;

class IntervalStepList implements \Countable, \Iterator
{
	/**
	 * @var IntervalStep[]
	 */
	private $steps;
	
	/**
	 * @var int
	 */
	private $currentIndex = 0;
	
	/**
	 * @var int ID расписания, к которому относятся IntervalStep
	 */
	private $schedule;
	
	/**
	 * IntervalStepList constructor.
	 *
	 * @param int $schedule ID расписания, к которому относятся IntervalStep
	 */
	public function __construct($schedule)
	{
		$this->setSchedule($schedule);
	}
	
	/**
	 * Добавление нового интервала-шага
	 *
	 * @param IntervalStep $step
	 */
	public function addStep(IntervalStep $step)
	{
		$this->steps[] = $step;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function count()
	{
		return count($this->steps);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function current()
	{
		return $this->steps[$this->currentIndex];
	}
	
	/**
	 * Получить последний свободный интервал, найденный до первого занятого.
	 * Если первого занятого интервала в списке не существует, то возвращается
	 * просто последний свободный интервал.
	 *
	 * Последний свободный интервал определяется по времени начала.
	 *
	 * @param null $start
	 *
	 * @return IntervalStep|null
	 */
	public function getLastFreeIntervalBeforeFindingFirstBusyInterval($start = null)
	{
		$steps = $this->steps;
		
		if (!is_null($start)) {
			$steps = array_filter($steps, function (IntervalStep $step) use ($start) {
				return $step->start >= $start;
			});
		}
		
		$busySteps = array_filter($steps, function (IntervalStep $step) {
			return $step->isStateBusy();
		});
		
		if ($busySteps) {
			usort($busySteps, function(IntervalStep $step1, IntervalStep $step2) {
				return $step1->start > $step2->start;
			});
			$busyStep = array_shift($busySteps); /** @var IntervalStep $busyStep */
			unset($busySteps);
			
			$steps = array_filter($steps, function(IntervalStep $step) use ($busyStep) {
				return $step->finish === $busyStep->start;
			});
		} else {
			usort($steps, function(IntervalStep $step1, IntervalStep $step2) {
				return $step1->start < $step2->start;
			});
		}
		
		return array_shift($steps);
	}
	
	/**
	 * Получить ID расписания, к которым относятся IntervalStep
	 *
	 * @return int
	 */
	public function getSchedule()
	{
		return $this->schedule;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function key()
	{
		return $this->currentIndex;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function next()
	{
		$this->currentIndex++;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function rewind()
	{
		$this->currentIndex = 0;
	}
	
	/**
	 * Установить ID расписания
	 *
	 * @param int $id
	 */
	private function setSchedule($id)
	{
		$this->schedule = (int) $id;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function valid()
	{
		return isset($this->steps[$this->currentIndex]);
	}
}