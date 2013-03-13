<?php
namespace LinkedList;

use LinkedList\Double\Node;
use Exception, InvalidArgumentException, OutOfBoundsException;

require_once 'Node.php';

class Double implements \ArrayAccess, \Countable, \Iterator
{
	const IT_MODE_QUEUE = 0;
	const IT_MODE_LIFO = 0;
	
	const IT_MODE_STACK = 1;
	const IT_MODE_FIFO = 1;
	
	protected $head;
	
	protected $tail;

	protected $count = 0;
	
	protected $offset;
	
	protected $iterator;
	
	protected $iteratorMode;
	
	public function __construct($iteratorMode = self::IT_MODE_LIFO)
	{
		$this->head = new Node(null);
		$this->tail = new Node(null);
		$this->count = 0;
		
		$this->iteratorMode = $iteratorMode;
		$this->head->setNext($this->tail);
		$this->tail->setPrev($this->head);
	}
	
	public function add($value, $index = null)
	{
		$node = new Node($value);
			
		if ($index === null) {
			$cursor = $this->tail->getPrev();
		} else {
			$cursor = $this->getCursor($index);
		}
		
		$node->setPrev($cursor);
		$node->setNext($cursor->getNext());
		$cursor->getNext()->setPrev($node);
		$cursor->setNext($node);
		++$this->count;
	}
	
	public function push()
	{
		$argumentNumber = func_num_args();
		if ($argumentNumber <= 0) {
			throw new InvalidArgumentException('No arguments given');
		}
			
		for ($i = $argumentNumber - 1; $i >= 0; --$i) {
			$this->add(func_get_arg($i));
		}
	}
	
	public function pop()
	{
		try {
			$value = $this->tail->getPrev()->getValue();
			$this->remove($this->count - 1);
			return $value;
		} catch (Exception $e) {
			throw new OutOfRangeException();
		}
	}
	
	public function shift()
	{
		try {
			$value = $this->head->getNext()->getValue();
			$this->remove(0);
			return $value;
		} catch (Exception $e) {
			throw new OutOfRangeException();
		} 
	}
	
	public function unshift()
	{
		$argumentNumber = func_num_args();
		if ($argumentNumber <= 0) {
			throw new InvalidArgumentException('No arguments given');
		}
			
		for ($i = $argumentNumber - 1; $i >= 0; --$i) {
			$this->add(func_get_arg($i), 0);
		}
	}
	
	public function addHead($value)
	{
		$this->add($value, 0);
	}
	
	public function addTail($value)
	{
		$this->add($value);
	}
	
	public function remove($index)
	{
		$cursor = $this->getCursor($index + 1);
		
		$cursor->getPrev()->setNext($cursor->getNext());
		$cursor->getNext()->setPrev($cursor->getPrev());
		unset($cursor);
		--$this->count;
	}
	
	public function get($index)
	{
		return $this->getCursor($index)->getValue();
	}
	
	protected function getCursor($index)
	{
		if (!is_int($index)) {
			throw new InvalidArgumentException();
		}
		
		if ($index < 0 || $index > $this->count) {
			throw new OutOfBoundsException();
		}
		
		if ($this->count/2 > $index) {
			$cursor = $this->head;
			for ($i = 0; $i < $index; ++$i) {
				$cursor = $cursor->getNext();
			}
		} else {
			$cursor = $this->tail;
			for ($i = $this->count; $i >= $index; --$i) {
				$cursor = $cursor->getPrev();
			}
		}
		
		return $cursor;
	}

	public function size()
	{
		return $this->count;
	}
	
	public function isEmpty()
	{
		return $this->count == 0;
	}
	
	public static function fromArray($array)
	{
		if (!is_array($array)) {
			throw new InvalidArgumentException();
		}
		
		$doublyLinked = new static();
		foreach ($array as $element) {
			$doublyLinked->add($element);
		}
		return $doublyLinked;
	}
	
	public function __toString()
	{
		$string = '';
		foreach ($this as $key => $value) {
			$string .= "[$key]: $value".PHP_EOL;
		}
		return $string;
	}
	
	public function offsetExists($offset) 
	{
		return is_int($offset) && $offset >= 0 && $offset < $this->count;
	}

	public function offsetGet($offset) 
	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $value) 
	{
		return $this->add($value, $offset);
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}
	
	public function count()
	{
		return $this->count;
	}
	
	public function prev()
	{
		if ($this->iteratorMode === self::IT_MODE_QUEUE) {
			--$this->offset;
			$this->iterator = $this->iterator->getPrev();
		} else {
			++$this->offset;
			$this->iterator = $this->iterator->getNext();
		}
	}
	
	public function next()
	{
		if ($this->iteratorMode === self::IT_MODE_QUEUE) {
			++$this->offset;
			$this->iterator = $this->iterator->getNext();
		} else {
			--$this->offset;
			$this->iterator = $this->iterator->getPrev();
		}
	}
	
	public function forward()
	{
		if ($this->iteratorMode === self::IT_MODE_QUEUE) {
			$this->offset = $this->count - 1;
			$this->iterator = $this->tail->getPrev();
		} else {
			$this->offset = 0;
			$this->iterator = $this->head->getNext();
		}
	}
	
	public function rewind()
	{
		if ($this->iteratorMode === self::IT_MODE_QUEUE) {
			$this->offset = 0;
			$this->iterator = $this->head->getNext();
		} else {
			$this->offset = $this->count - 1;
			$this->iterator = $this->tail->getPrev();			
		}
	}
	
	public function current()
	{
		return $this->iterator->getValue();
	}
	
	public function key() 
	{
		return $this->offset;
	}

	public function valid() 
	{
		return $this->offset < $this->count;
	}
	
	public function getIteratorMode()
	{
		return $this->iteratorMode;
	}
	
	public function setIteratorMode($iteratorMode = self::IT_MODE_LIFO)
	{
		$reflection = new ReflectionClass($this);
		$iterators = array_keys($reflection->getConstants(), 'IT_MODE_');
		$iterators = array_intersect_key(array_flip($iterators), $reflection->getConstants());
		
		if (!in_array($iteratorMode, $iterators, true)) {
			throw new InvalidArgumentException();
		}
		
		$this->iteratorMode = $iteratorMode;
	}
}