<?php
/**
 * Интерфейс для работы с хранилищем данных интервалов времени
 */

namespace app\components\basket\schedule_interval;

interface ScheduleIntervalPersistence
{
	/**
	 * Добавление интервала времени в хранилище
	 *
	 * @param ScheduleInterval $interval
	 *
	 * @return void
	 */
	public function add(ScheduleInterval $interval);
	
	/**
	 * Получение всех интервалов времени в хранилище
	 *
	 * @return ScheduleInterval[]
	 */
	public function getAll();
}