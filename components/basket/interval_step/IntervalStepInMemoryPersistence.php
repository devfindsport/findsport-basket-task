<?php
/**
 * Хранилище данных для интервалов времени, основанных на шаге расписаний
 */

namespace app\components\basket\interval_step;

class IntervalStepInMemoryPersistence implements IntervalStepPersistence
{
	/**
	 * @var IntervalStep[]
	 */
	private $data = [];
	
	/**
	 * {@inheritdoc}
	 */
	public function add(IntervalStep $step)
	{
		$this->data[] = $step;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getAll()
	{
		return $this->data;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function delete(IntervalStep $step)
	{
		$this->data = array_filter($this->data, function (IntervalStep $item) use ($step) {
			return !($item->schedule_id == $step->schedule_id && $item->start == $step->start && $item->finish == $step->finish);
		});
	}
}