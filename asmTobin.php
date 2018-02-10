<?php
	$INPUT = $_POST['in'];
	//$OUTPUT = "instruct.txt";
	$conversion = array(
		'add' => 01001,
		'sub' => 01010,
		'set' => 00111,
		'store' => 00111,
		'load' => 00101,
		'brachifzero' => 00010,
		'branchifpos' => 00011,
		'branchifnooverflow' => 00001,
		'jump' => 00000,
		'compare' => 01011,
		'increment' => 01100,
		'shiftleft' => 01101,
		'done' => 01111,
		'testtopbits' => 01110
	);
	if(!isset($INPUT))
		die("No file was supplied");
	$out = array();
	$h = fopen($INPUT, "r");
	if(!$h)
		die("Cannot open file: '$INPUT'");
	$lnum = 0;
	while(($line = fgets($h)) !== false)
	{
		$lnum++;
		$line = explode(" ", $line);
		if($line[0]=='@')
		{
			if(sizeof($line) == 3)
				$out[] = "1".sprintf("%04d", decbin($line[1]).sprintf("%04d", decbin($line[2])));
			else if(sizeof($line) == 2)
				$out[] = "1".sprintf("%08d", decbin($line[1]));
			else
				die("Invalid args to '@' on line: ".$lnum);
		}
		else
		{
			if(!isset($conversion[$line[0]]))
				die("Invalid instruction '".$line[0]."' on line $lnum");
			if(sizeof($line) == 2)
				$suff = sprintf("%04d", decbin($line[1]));
			else
				$suff = $line[0] == 'done' ? '1111' : '0000';
			$out[] = '0'.$conversion[$line[0]].$suff;
		}
	}
	//file_put_contents("$OUTPUT", print_r(implode("",$out), true));
	//echo "Outputted binary to $OUTPUT containing:\n";
	echo implode("",$out);
?>