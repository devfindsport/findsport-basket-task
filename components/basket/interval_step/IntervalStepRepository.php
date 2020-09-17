<?php
/**
 * Репозиторий для взаимодействия с объектами интревалов времени,
 * добавляемых пользователем в корзину, разбитых на шаги расписания
 */

namespace app\components\basket\interval_step;

class IntervalStepRepository
{
	/**
	 * @var IntervalStepPersistence
	 */
	private $persistence;
	
	/**
	 * IntervalStepRepository constructor.
	 *
	 * @param IntervalStepPersistence $persistence
	 */
	public function __construct(IntervalStepPersistence $persistence)
	{
		$this->persistence = $persistence;
	}
	
	/**
	 * Добавление интервала времени в хранилище
	 *
	 * @param IntervalStep $step
	 */
	public function add(IntervalStep $step)
	{
		$this->persistence->add($step);
	}
	
	/**
	 * Удаление интервала времени
	 *
	 * @param IntervalStep $step
	 */
	public function delete(IntervalStep $step)
	{
		$this->persistence->delete($step);
	}
	
	/**
	 * Поиск всех интервалов времени в хранилище
	 *
	 * @return IntervalStep[]
	 */
	public function findAll()
	{
		return $this->persistence->getAll();
	}
	
	/**
	 * Поиск всех интервалов времени в хранилище по его атрибутам
	 *
	 * @param array $attributes
	 *
	 * @return IntervalStep[]|array
	 *
	 * @throws \CException
	 */
	public function findAllByAttributes($attributes)
	{
		return (new \Collection($this->findAll()))->findAllByAttributes($attributes)->toArray();
	}
	
	/**
	 * Поиск интервала времени в хранилище по его атрибутам
	 *
	 * @param array $attributes
	 *
	 * @return IntervalStep|object|null
	 * @throws \CException
	 */
	public function findByAttributes($attributes)
	{
		return (new \Collection($this->findAll()))->findByAttributes($attributes);
	}
	
	/**
	 * Поиск первого интервала времени в хранилище по его атрибутам
	 *
	 * @return IntervalStep|object|null
	 * @throws \CException
	 */
	public function getFirst()
	{
		return (new \Collection($this->findAll()))->min(new \Comparator(['attribute' => 'start']));
	}
}