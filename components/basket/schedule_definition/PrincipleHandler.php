<?php
/**
 * Абстрактный класс для реализации паттерна цепочки обязанностей.
 *
 * Проверяемый интервал, для которого определяется расписание, проходит проверку по нескольким принципам,
 * каждый из которых возвращает идентификаторы расписаний, которые удоволетворяют условиям данного принципа.
 */

namespace app\components\basket\schedule_definition;

use app\components\basket\interval_step\IntervalStepRepository;
use app\components\basket\time\Interval;

abstract class PrincipleHandler
{
	/**
	 * @var PrincipleHandler|null
	 */
	private $successor = null;
	
	/**
	 * PrincipleHandler constructor.
	 *
	 * @param PrincipleHandler|null $handler
	 */
	public function __construct(PrincipleHandler $handler = null)
	{
		$this->successor = $handler;
	}
	
	/**
	 * Метод гарантирует, что каждый из проверяемых принципов будет вызван.
	 *
	 * @param Interval $interval
	 * @param IntervalStepRepository $repository
	 * @param array $scheduleIDs [optional] [1, 2, 3, ..., n] ID расписаний, полученных от предыдущего принципа
	 *
	 * @return array [1, 2, 3, ..., n] ID расписаний
	 */
	final public function handle(Interval $interval, IntervalStepRepository $repository, $scheduleIDs = [])
	{
		$scheduleIDs = $this->processing($interval, $repository, $scheduleIDs);
		
		if (count($scheduleIDs) > 1 && $this->successor !== null) {
			$scheduleIDs = $this->successor->handle($interval, $repository, $scheduleIDs);
		}
		
		return $scheduleIDs;
	}
	
	/**
	 * Получение ID расписаний, наиболее подходящих под правила данного принципа.
	 *
	 * @param Interval $interval
	 * @param IntervalStepRepository $repository
	 * @param array $scheduleIDs [optional] ID расписаний, полученных от предыдущего принципа
	 *
	 * @return array
	 */
	abstract protected function processing(Interval $interval, IntervalStepRepository $repository, $scheduleIDs = []);
}