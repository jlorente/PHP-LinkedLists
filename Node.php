<?php
namespace LinkedList\Double;

class Node
{
	protected $value;
	protected $next;
	protected $prev;
	
	public function __construct($value, Node $prev = null, Node $next = null)
	{
		$this->setValue($value);
		$this->setPrev($prev);
		$this->setNext($next);
	}
	
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function setNext(Node $next = null)
	{
		$this->next = $next;
	}

	public function setPrev(Node $prev = null)
	{
		$this->prev = $prev;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function getNext()
	{
		return $this->next;
	}

	public function getPrev()
	{
		return $this->prev;
	}
	
	public function __toString()
	{
		return $this->getValue();
	}
}