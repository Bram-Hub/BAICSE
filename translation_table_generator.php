<?php


//translation_table_generator.php
//used for generating translation tables based on a passed array of elements

class translation_table_generator
{
	
	private $_max;
	private $_variables;
	private $_add_array;		//the next array to be added to the return array

	
	public function __construct()
	{
	}
	
	public function generate_translation_tables ( $variables )
	{
		$this->_variables = $variables;
		//first, the number of variables determines the size of the table.
		//maximum number of variables is 10
		$count = count ( $variables );
		if ( $count >= 10 )
		{
			return false;
		}
		$this->_max = $this->set_max($count);
		
		
		//start with an array of "A" values for each variable in the variables array
		$base_translation = array ();
		foreach ($variables as $value) 
		{
			$base_translation[$value] = "A";
		}
		
		//print_r ( $base_translation );
		//echo "<P>";
		
		$this->_add_array = $base_translation;
		
		//start the return array with the base translation:
		$final_array = array();
		$final_array[] = $base_translation;
		
		$check = true;
		
		//increment the base translation until they are all the maximum value
		while ( $check !== false )
		{
			$base_translation = $this->increment_array( $base_translation, 0 );
			if ( $base_translation === false )
			{
				$check = false;
			}
			else 
			{
				$final_array[] = $base_translation;
			}
		}
		
		
		return $final_array;
	}
	
	private function set_max($count)
	{
		switch ($count) 
		{
			case 1:
				return "A";
				break;
			case 2:
				return "B";
				break;
			case 3:
				return "C";
				break;
			case 4:
				return "D";
				break;
			case 5:
				return "E";
				break;
			case 6:
				return "F";
				break;
			case 7:
				return "G";
				break;
			case 8:
				return "H";
				break;
			case 9:
				return "I";
				break;
			case 10:
				return "J";
				break;
			default:
				return false;
				break;
		}
	}
	
	
	
	private function increment_array ( $array, $index )
	{
		//first, make sure that the index is not beyond the scope of the variables array
		if ( $index >= count ( $array ) )
		{
			return false;
		}
		
		//now, check to see what the current value is
		if ( $array[$this->_variables[$index]] == "A" )
		{
			$array[$this->_variables[$index]] = "B";
		}
		elseif ( $array[$this->_variables[$index]] == "B" )	
		{
			if ( $this->_max == "B" )
			{
				$array[$this->_variables[$index]] = "A";
				return $this->increment_array( $array, ($index + 1) );
			}
			$array[$this->_variables[$index]] = "C";
		}
		elseif ( $array[$this->_variables[$index]] == "C" )	
		{
			if ( $this->_max == "C" )
			{
				$array[$this->_variables[$index]] = "A";
				return $this->increment_array( $array, ($index + 1) );
			}
			$array[$this->_variables[$index]] = "D";
		}
		elseif ( $array[$this->_variables[$index]] == "D" )	
		{
			if ( $this->_max == "D" )
			{
				$array[$this->_variables[$index]] = "A";
				return $this->increment_array( $array, ($index + 1) );
			}
			$array[$this->_variables[$index]] = "E";
		}
		elseif ( $array[$this->_variables[$index]] == "E" )	
		{
			if ( $this->_max == "E" )
			{
				$array[$this->_variables[$index]] = "A";
				return $this->increment_array( $array, ($index + 1) );
			}
			$array[$this->_variables[$index]] = "F";
		}
		elseif ( $array[$this->_variables[$index]] == "F" )	
		{
			if ( $this->_max == "F" )
			{
				$array[$this->_variables[$index]] = "A";
				return $this->increment_array( $array, ($index + 1) );
			}
			$array[$this->_variables[$index]] = "G";
		}
		elseif ( $array[$this->_variables[$index]] == "G" )	
		{
			if ( $this->_max == "G" )
			{
				$array[$this->_variables[$index]] = "A";
				return $this->increment_array( $array, ($index + 1) );
			}
			$array[$this->_variables[$index]] = "H";
		}
		elseif ( $array[$this->_variables[$index]] == "H" )	
		{
			if ( $this->_max == "H" )
			{
				$array[$this->_variables[$index]] = "A";
				return $this->increment_array( $array, ($index + 1) );
			}
			$array[$this->_variables[$index]] = "I";
		}
		elseif ( $array[$this->_variables[$index]] == "I" )	
		{
			if ( $this->_max == "I" )
			{
				$array[$this->_variables[$index]] = "A";
				return $this->increment_array( $array, ($index + 1) );
			}
			$array[$this->_variables[$index]] = "C";
		}
		else   //"J"
		{
			$array[$this->_variables[$index]] = "A";
			return $this->increment_array( $array, ($index + 1) );
		}
		return $array;
	}


}

?>