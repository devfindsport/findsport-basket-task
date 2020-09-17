<?php
/**
 * Принцип добрых соседей
 *
 * Если мы находим два и больше свободных расписаний, на которые мы можем добавить время (с одинаковым числом склеек),
 * то при выборе расписания мы руководствуемся тем, есть ли занятое время рядом. Этот принцип необходим для того,
 * чтобы равномерно занимать расписания, тем самым минимизируя вероятность беготни клиента между объектами.
 * В первую очередь мы смотрим расписание, на котором есть граничащее время слева и справа, потом слева, потом справа.
 */

namespace app\components\basket\schedule_definition;

use app\components\basket\interval_step\IntervalStepRepository;
use app\components\basket\time\Interval;

class GoodNeighborsPrinciple extends PrincipleHandler
{
	/**
	 * {@inheritdoc}
	 */
	protected function processing(Interval $interval, IntervalStepRepository $repository, $scheduleIDs = [])
	{
		// Введите здесь свой код
	}
}