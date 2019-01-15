<?php  
//Filename:	RSSolver.php 
//Purpose:	Automatically check axiom systems for consistency 
//Author:	Dan McAloon 
//Copyright:	2005 Dan McAloon   

require_once('axiom_table.php');

class axiom_solver 
{
	private $_and;
	private $_or;
	private $_not;
	private $_initial_not;	//not cycles more than once, this stores the not array with all As
	private $_initial_and;	//ditto
	private $_initial_or;
	private $_max;
	private $_increment_and;
	
	
	public function __construct($max)
	{
		$this->_max = $this->set_max($max);
		//based on the max variable, initialize the $and and $or arrays with their axiom table objects
		$val1 = "A";
		$val2 = "A";
		$temporary = $this->increment_max();
		//echo "Val1: $val1 <br> Val2: $val2 <br>Max: {$this->_max}<P>";
		while ( $val2 != $temporary )
		{
			//$new_and = new axiom_table($val1, $val2, 'A');
			$this->_and[$val1 . $val2] = "A";
			//$new_or = new axiom_table($val1, $val2, 'A');
			$this->_or[$val1 . $val2] = "A";
			if ( $val1 == $val2 )
			{
				$new_not = new axiom_table($val1, $val2, 'A');
				$this->_not[$val1 . $val1] = "A";
			}
			
			$val1 = $this->increment_value($val1);
			if ( $val1 == 'A' )
			{
				$val2 = $this->increment_value ( $val2 );
				if ( $val2 == 'A' )
				{
					$val2 = $temporary;
				}
			}
		}
		$this->_initial_not = $this->_not;
		$this->_initial_and = $this->_and;
		$this->_initial_or = $this->_or;
	}
	
	
	//when called, increments all "tables" and returns:
	//true if the tables were incremented successfully
	//false if the tables were maxed out in any way.
	public function increment_arrays()
	{
		$check = false;
		while ( $check === false )
		{
			$check = $this->increment_arrs();
			if ( $check === false )
			{
				return false;
			}
		}
		return true;
	}
	
	
	//increments all three arrays and returns true unless they are at the end position, then false.
	private function increment_arrs()
	{
		$this->_not = $this->increment_arr($this->_not );
		if ( $this->_not === false )
		{
			$this->_not = $this->reset_not();
			$this->_and = $this->increment_arr( $this->_and );
			if ( $this->_and === false )
			{
				$this->_and = $this->reset_and();
				$this->_or = $this->increment_arr( $this->_or );
				if ( $this->_or === false )
				{
					return false;
				}
			}
		}
		return true;
		
	}
	
	public function check_implication()
	{
		//check to see if implication was invalidated
		//find a place in the OR table where ~P v Q is A, and ~P is A
		//so loop through the not tables, take the result, find all instances of
		//that result in the or table where the result is A
		foreach ($this->_not as $not_key => $not) 
		{
			//if the current or result is A
			if ( $not_key == 'AA' )
			{
				//loop through the or table finding the result of the not on var1
				//as the first arg, and "A" as the result, check to see if "A"
				//is var 2
				foreach ($this->_or as $or_key => $or) 
				{
					if ( ( $or_key[0] == $not ) && ( $or == 'A' ) 
						&& ( $or_key[1] != 'A' ) )
					{
						//echo "<h2>Invalid Tables:<P></h2>\n\n";
						//$this->test_print();
						//echo "The table is invalid when \$not->var1 == {$not->var1} and \$or->result == {$or->result} but \$or->var2 == {$or->var2}\n<P>\n";
						return false;
					}
				}
			}
		}
		
		return true;
	}
	
	
	//when passed an array, increment_arr increments the array by 1, returns false if the max is reached
	private function increment_arr( &$arr/*, $index*/ )
	{
		
		foreach ( $arr AS $key => $value )
		{
			$arr[$key] = $this->increment_value($value);
			if ( $arr[$key] != "A" )
			{
				break;
			}
		}
		$check =  $this->check_table($arr);
		if ( $check === false )
		{
			return false;
		}
		else 
		{
			return $arr;
		}
	}
	
	
	
	
	//checks the passed array to see if all the variables are the "max" variable. 
	//returns true if they are not
	private function check_table( $array )
	{
		foreach ( $array AS $value )
		{
			if ( $value != $this->_max )
			{
				return true;
			}
		}
		return false;
	}
	
	//resets all result values in the array to A
	private function reset_not ()
	{
		return $this->_initial_not;
	}
	
	private function reset_and()
	{
		return $this->_initial_and;
	}
		
	private function reset_or()
	{
		return $this->_initial_or;
	}
	
	//passed a single value, returns the next value, or false if this value equals the max
	private function increment_value ( $val )
	{
		if ( $val == $this->_max )
		{
			return "A";
		}
		else 
		{
			return ++$val;
		}
	}
	
	//returns the next letter relative to the current maximum, for generating the original lists
	private function increment_max()
	{
		$check = ($this->_max + 1);
		if ( $check == 'K' )
		{
			return 'A';
		}
		else 
		{
			return $check;
		}
	}
	
	
	//when passed two values and an operator, returns the result
	public function interpret( $oper, $val_a, $val_b = '' )
	{
		if ( $oper == '^' )
		{
			return $this->_and[$val_a . $val_b];
			/*$temp = count( $this->_and );
			for( $p = 0; $p < $temp; $p++ )
			{
				if ( ( $this->_and[$p]->var1 == $val_a ) && ( $this->_and[$p]->var2 == $val_b ) )
				{
					return $this->_and[$p]->result;
				}
			}*/
		}
		elseif ( $oper == 'v' )
		{
			return $this->_or[$val_a . $val_b];
			/*$temp = count( $this->_or );
			for ( $p = 0; $p < $temp; $p++ )
			{
				if ( ( $this->_or[$p]->var1 == $val_a ) && ( $this->_or[$p]->var2 == $val_b ) )
				{
					return $this->_or[$p]->result;
				}
			}*/
		}
		elseif ( $oper == '~' )
		{
			return $this->_not[$val_a . $val_a];
			/*$temp = count( $this->_not );
			for ( $p = 0; $p < $temp; $p++ )
			{
				if ( $this->_not[$p]->var1 == $val_a )
				{
					return $this->_not[$p]->result;
				}
			}*/
		}
	}
		
	
	//prints out the contents of the two tables
	public function test_print()
	{
		echo "\n<center><h3>AND Table</h3>";
		echo "\n<table border='3'><tr>";
		echo "<th><center><b>P</b></center></th>";
		echo "<th><center><b>Q</b></center></th>";
		echo "<th><center><b>P & Q</b></center></th>";
		echo "</tr>";
		foreach ($this->_and as $and_key => $and) 
		{
			echo "<tr>";
			echo "<td><center>";
			echo $and_key[0];
			echo "</center></td>";
			echo "<td><center>";
			echo $and_key[1];
			echo "</center></td>";
			echo "<td><center>";
			echo $and;
			echo "</center></td>";
			echo "</tr>";
		}
		echo "</table><P><P>";
		
		
		echo "\n<h3>OR Table</h3>";
		echo "\n<table border='3'><tr>";
		echo "<th><center><b>P</b></center></th>";
		echo "<th><center><b>Q</b></center></th>";
		echo "<th><center><b>P | Q</b></center></th>";
		echo "</tr>";
		foreach ($this->_or as $or_key => $or) 
		{
			echo "<tr>";
			echo "<td><center>";
			echo $or_key[0];
			echo "</center></td>";
			echo "<td><center>";
			echo $or_key[1];
			echo "</center></td>";
			echo "<td><center>";
			echo $or;
			echo "</center></td>";
			echo "</tr>";
		}
		echo "</table><P><P>";
		
		echo "\n<h3>NOT Table</h3>";
		echo "\n<table border='3'><tr>";
		echo "<th><center><b>P</b></center></th>";
		echo "<th><center><b>~P</b></center></th>";
		echo "</tr>";
		foreach ($this->_not as $not_key => $not) 
		{
			echo "<tr>";
			echo "<td><center>";
			echo $not_key[0];
			echo "</center></td>";
			echo "<td><center>";
			echo $not;
			echo "</center></td>";
			echo "</tr>";
		}
		echo "</table>\n";
	}
			
	
	private function set_max($count)
	{
		switch ($count) 
		{
			case 1:
				return 'A';
				break;
			case 2:
				return 'B';
				break;
			case 3:
				return 'C';
				break;
			case 4:
				return 'D';
				break;
			case 5:
				return 'E';
				break;
			case 6:
				return 'F';
				break;
			case 7:
				return 'G';
				break;
			case 8:
				return 'H';
				break;
			case 9:
				return 'I';
				break;
			case 10:
				return 'J';
				break;
			default:
				return false;
				break;
		}
	}
		
}
?>