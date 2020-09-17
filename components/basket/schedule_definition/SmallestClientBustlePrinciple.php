<?php
/**
 * Принцип наименьшей беготни клиента.
 *
 * В первую очередь администрации нормальных площадок важно, чтобы клиент остался довольным после посещения их объектов.
 * Если мы заставим клиента каждые полчаса (или каждый час) бегать между несколькими площадками, когда рядом будет
 * пустовать ещё одна площадка, то клиент останется недоволен. Поэтому самый главный принцип – ищем свободные интервалы,
 * которые можно объединить. То есть пытаемся добавить интервал целиком, а если не получается, то пытаемся
 * это сделать с наименьшим количеством склеек.
 */

namespace app\components\basket\schedule_definition;

use app\components\basket\time\Interval;
use app\components\basket\interval_step\IntervalStepRepository;

class SmallestClientBustlePrinciple extends PrincipleHandler
{
	/**
	 * SmallestClientBustlePrinciple constructor.
	 *
	 * @param Interval $interval
	 * @param PrincipleHandler|null $handler
	 */
	public function __construct(PrincipleHandler $handler = null)
	{
		parent::__construct($handler);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function processing(Interval $interval, IntervalStepRepository $repository, $scheduleIDs = [])
	{
		// Введите здесь свой код
	}
}