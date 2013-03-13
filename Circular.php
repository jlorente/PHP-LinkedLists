<?php
namespace LinkedList\Double;
use LinkedList\Double;

require_once 'Double.php';

class Circular extends Double
{
	const IT_MODE_RIGHT = 0;
	const IT_MODE_LEFT = 1;
	
	public function __construct($iteratorMode = self::IT_MODE_RIGHT)
	{
		parent::__construct();
		
		$this->iteratorMode = $iteratorMode;
		$this->head->setPrev($this->tail);
		$this->tail->setNext($this->head);
	}

	public function next()
	{
		parent::next();
		if (!$this->valid()) {
			$this->rewind();
		}
	}
	
	public function prev()
	{
		parent::prev();
		if (!$this->valid()) {
			$this->forward();
		}
	}
}