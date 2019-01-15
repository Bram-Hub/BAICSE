<?php

class axiom_table
{
	public $var1;
	public $var2;
	public $result;
	
	public function __construct ( $pvar1, $pvar2, $presult )
	{
		$this->var1 = $pvar1;
		$this->var2 = $pvar2;
		$this->result = $presult;
	}
	
	public function increment($max)
	{
		if ( $this->result == $max )
		{
			$this->result = "A";
			return "A";
		}
		else 
		{
			$this->result++;
			return $this->result;
		}
	}
}
?>