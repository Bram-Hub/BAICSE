<?php

require_once('axiom_solver.php');


function eval_fol_exp( $matches )
{	
	global $axiom_solver;
		
	if ( $matches[1] == '~' )
	{
		//swap the values for negation removal
		//$check = $axiom_solver->interpret( $matches[1], $matches[2] );
		//echo "Returning: $check<P>";
		return $axiom_solver->interpret( $matches[1], $matches[2] );
	}
	//$check = $axiom_solver->interpret( $matches[2], $matches[1], $matches[3] );
	//echo "Returning: $check<P>";
	return $axiom_solver->interpret( $matches[2], $matches[1], $matches[3] );
}




//$sentence = "(((AvB)^(BvA))^(AvA))^(BvB)^(~CvD)";

function evaluate_sentence ( $sentence )
{
	global $axiom_solver;
	
	$original_string = $sentence;
	
	$original_string = preg_replace_callback("/(~)([A-Za-z])/", "eval_fol_exp", $original_string);
	
	while( (preg_match("/([A-Za-z])(\^)([A-Za-z])/",$original_string)) || 
			(preg_match("/\(([A-Za-z])(v)([A-Za-z])\)/",$original_string)) )
	{
		$original_string = preg_replace_callback("/([A-Za-z])(\^)([A-Za-z])/", 
			"eval_fol_exp", $original_string);
		$original_string = preg_replace("/\(([a-zA-Z])\)/","$1",$original_string);
		$original_string = preg_replace_callback("/\(([A-Za-z])(v)([A-Za-z])\)/",
			"eval_fol_exp",$original_string);
	}
	
	return $original_string;
}


//echo $sentence . "<P>" . $original_string . "<P>";

?>
