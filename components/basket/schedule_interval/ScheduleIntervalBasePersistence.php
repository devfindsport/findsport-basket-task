<?php
/**
 * Общий лкасс для хранилища данных интервалов времени
 */

namespace app\components\basket\schedule_interval;

abstract class ScheduleIntervalBasePersistence implements ScheduleIntervalPersistence
{
	/**
	 * @var ScheduleInterval[]
	 */
	private $data = [];
	
	/**
	 * {@inheritdoc}
	 */
	public function add(ScheduleInterval $interval)
	{
		$this->data[] = $interval;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getAll()
	{
		return $this->data;
	}
}