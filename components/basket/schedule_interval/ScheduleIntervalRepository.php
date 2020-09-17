<?php
/**
 * Репозиторий для взаимодействия с объектами полученного из БД времени
 */

namespace app\components\basket\schedule_interval;

use app\components\basket\time\Interval;

class ScheduleIntervalRepository
{
	/**
	 * @var ScheduleIntervalPersistence
	 */
	private $persistence;
	
	/**
	 * ScheduleIntervalRepository constructor.
	 *
	 * @param ScheduleIntervalPersistence $persistence
	 */
	public function __construct(ScheduleIntervalPersistence $persistence)
	{
		$this->persistence = $persistence;
	}
	
	/**
	 * Найти все подходящие объекты по атрибуту ID расписания, началу времени и его завершению
	 *
	 * @param int $schedule ID расписание
	 * @param int $start начало интервала временив секундах от начала дня
	 * @param int $finish конец интервала временив секундах от начала дня
	 *
	 * @return ScheduleInterval[]|array
	 */
	public function findByScheduleStartAndFinish($schedule, $start, $finish)
	{
		return array_filter(array_map(function (ScheduleInterval $interval) use ($schedule, $start, $finish) {
			return $interval->schedule_id == $schedule && Interval::isRangeOverlapRange($start, $finish, $interval->start, $interval->finish)
				? $interval
				: null;
		}, $this->persistence->getAll()));
	}
	
	/**
	 * Найти все подходящие объекты, которые пересекаются или граничат с искомым интервалом.
	 *
	 * @param int $start начало интервала временив секундах от начала дня
	 * @param int $finish конец интервала временив секундах от начала дня
	 *
	 * @return ScheduleInterval[]|array
	 */
	public function findOverlapByStartAndFinish($start, $finish) {
		return array_filter(array_map(function (ScheduleInterval $interval) use ($start, $finish) {
			return Interval::isRangeOverlapRange($start, $finish, $interval->start, $interval->finish)
					|| $interval->start == $finish
					|| $interval->finish == $start
				? $interval
				: null;
		}, $this->persistence->getAll()));
	}
}