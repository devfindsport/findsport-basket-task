<?php
/**
 * Интерфейс для хранилища данных о добавляемых интервалах времени,
 * основанных на шаге расписания
 */

namespace app\components\basket\interval_step;

interface IntervalStepPersistence
{
	/**
	 * Добавление интервала времени в хранилище
	 *
	 * @param IntervalStep $step
	 *
	 * @return void
	 */
	public function add(IntervalStep $step);
	
	/**
	 * Удаление интервала времени в хранилище
	 *
	 * @param IntervalStep $step
	 *
	 * @return void
	 */
	public function delete(IntervalStep $step);
	
	/**
	 * Получить все интервалы времени из хранилища
	 *
	 * @return IntervalStep[]
	 */
	public function getAll();
}