<?php

//main_solver.php
//the main script which interacts between the user interface and the helper functions


require_once('unique_vars.php');
require_once('translation_table_generator.php');
require_once('solve.php');

//first, gather the sentences
$sentences = explode( "\r\n", $_POST['sentences'] );

//$sentences = array ( "(~PvP)^(~PvP)" , "((~Pv~Q)vP)", "((P^~Q)v((Q^R)v(~R^~P)))");
	
//echo "<center><H1>Beginning processing...</h1></center>\n";

$unique_variables = get_vars($sentences);

$translation_generator = new translation_table_generator();
$translation_table = $translation_generator->generate_translation_tables($unique_variables);


$axiom_solver = new axiom_solver(count( $unique_variables ));

//generate an array of the "solved" sentences, so we don't print out 1000 records for each axiom
$solved_sentences = array();
	
for ( $p = 0; $p < count($sentences); $p++ )
{
	$solved_sentences[$sentences[$p]] = false;
}


//loop through all the possible truth tables
while ( $axiom_solver->increment_arrays() !== false )
{	
	//check to see if all the solved values are true
	if ( check_solved($solved_sentences) )
	{
		break;
	}
	
	
	$results = array();
	$tauts = array();
		
	$count = count($sentences);
	
	for ( $p = 0; $p < $count; $p++ )
	{
		$results[$sentences[$p]] = array();
		$tauts[$sentences[$p]] = true;
	}
	
	foreach ( $translation_table AS $row )
	{
		$character = array_keys($row);
		//echo "Characters:<br>\n";
		//print_r( $character );
		$replacement = array_values($row);
		//echo "\n<P>To be replaced by:<br>\n";
		//print_r( $replacement );
		$strings = $sentences;
		
		/*foreach ( $strings AS $key => $sentence )
		{
			for ( $p = 0; $p < count($replacement); $p++ )
			{
				echo "\n<P>Replacing {$character[$p]} with {$replacement[$p]} in sentence $sentence<P>\n";
				str_replace($character[$p],$replacement[$p],$sentence);
			}
		}*/
		
		$strings = str_replace($character, $replacement, $strings);
		
		//echo "\n<P>New Sentences:<br>\n";
		//print_r($strings);
		//echo "\n<P>\n";
		
		//strings is now the array of all the sentences with the substitutions completed
		//solve each sentence, pop the result onto the end of the results table
		foreach ($strings as $string) 
		{
			//count the un-tautaologies, if more than 1 at any point, break immediately
			$count_tauts = 0;
			foreach ($tauts as $taut) 
			{
				if ( $taut === false )
				{
					$count_tauts++;
				}
			}
			if ( $count_tauts > 1 )
			{
				break;
			}
			$reversed = array_flip($strings);
			$sentence = $sentences[$reversed[$string]];
			$result = evaluate_sentence($string);
			if ( $result != "A" )
			{
				$tauts[$sentence] = false;
			}
			$results[$sentence][] = $result;
		}
	}
	
	//outside of the translation table loop, check for A-tautologies
	//if one of the taut values is false and all the rest are true, print the result
	$num_tauts = 0;
	$taut_sentence = "";
	foreach ($tauts as $sentence => $taut) 
	{
		if ( $taut == true )
		{
			$num_tauts++;
		}
		else 
		{
			$taut_sentence = $sentence;
		}
	}
	
	if ( ($num_tauts == (count ($sentences) - 1)) && ($solved_sentences[$taut_sentence] === false) )
	{
		//check implication
		if ( $axiom_solver->check_implication() )
		{
			$solved_sentences[$taut_sentence] = true;
			echo "<center><h2>The sentence:<br>$taut_sentence<br>Has been found to be independent using the tables:<p></h2>";
			$axiom_solver->test_print();
		}
	}
}

//here, echo the unsolved tables
echo "<center><h2>The following sentences could not be proven independent:</h2><br>";
echo "<table border='3'>";
foreach ($solved_sentences as $sentence => $solved) 
{
	if ( $solved == false )
	{
		echo "<tr><td>$sentence</td></tr>";
	}
}
echo "</table>";


function check_solved($solved_sentences)
{
	foreach ($solved_sentences as $solved) 
	{
		if ( $solved === false )
		{
			return false;
		}	
	}
	return true;
}


?>