<?php
/**
 * Простая фабрика для создания объектов ScheduleInterval
 */

namespace app\components\basket\schedule_interval;

class ScheduleIntervalFactory
{
	/**
	 * Создание интервала занятого времени в расписании площадки
	 *
	 * @param int $scheduleID ID расписания площадки
	 * @param int $offsetStart Время начала интервала в секундах от начала дня
	 * @param int $offsetFinish Время завершения интервала в секундах от начала дня
	 * @param int $weight Занятая доля на площадке интервалом
	 *
	 * @return ScheduleInterval
	 */
	public function create($scheduleID, $offsetStart, $offsetFinish, $weight)
	{
		return new ScheduleInterval((int) $scheduleID, (int) $offsetStart, (int) $offsetFinish, (int) $weight);
	}
}