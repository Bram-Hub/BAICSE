<?php

/**
Takes an array of strings (or a single string) sFOL statements and returns an array
of all unique varaibles
*/
function get_vars( $strings )
{
	$matches = array();
	$strings = is_array($strings)?implode('',$strings):$strings;
	preg_match_all("/[A-Z]/",$strings,$matches);
	return array_values(array_unique($matches[0]));
	
}


//print_r( get_vars('QPQQQQQQRRRSVTEWKLJNOIDFEENFNMDIEOSDZX') );





?>