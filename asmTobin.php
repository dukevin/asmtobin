<textarea rows="25" cols="25" style="margin:0 auto">
<?php
	$INPUT = $_POST['intxt'];
	//$OUTPUT = "instruct.txt";
	$conversion = array(
		'add' => "1001",
		'sub' => "1010",
		'set' => "0111",
		'store' => "0111",
		'load' => "0101",
		'branchifzero' => "000100000",
		'branchifpos' => "000110000",
		'branchifnooverflow' => "000010000",
		'jump' => "000000000",
		'compare' => "1011",
		'increment' => "1100",
		'shiftleft' => "1101",
		'done' => "011111111",
		'testtopbits' => "1110",
		'deref' => "0100"
	);
	if(!isset($INPUT) || empty($INPUT))
		err("Nothing was supplied");
	$out = array();
	$lnum = 0;
	$lines = explode(PHP_EOL, $INPUT);
	foreach($lines as $line)
	{
		if(empty($line)) continue;
		$lnum++;
		$line = explode(" ", trim($line));
		$tmp = array_filter($line,"iscmt");
		if(!empty($tmp))
			$line = array_filter(array_splice($line, 0, key($tmp)));
		if(empty($line)||$line[0] == '#'||empty($line[0]))
			continue;
		if(is_array($line[0]))
			$line = implode(" ",$line[0]);
		if(sizeof($line) == 3)
		{
			if($line[0] == "@")
				$out[] = "1".readreg($line[1], 4).readreg($line[1], 4);
			else
				err("Invalid args to '{$line[0]}', only @ supports 2 args. On line $lnum (".implode(" ",$line).")");
		}
		if(sizeof($line) == 2)
		{
			if($line[0] == '@')
				$out[] = "1".readreg($line[1], 8);
			else 
			{
				if(!isset($conversion[strtolower($line[0])])) 
				{
					if(($line[0][0])=="@")
						err("Expected 1 or 2 args to '@' but got ".(sizeof($line)-1)." on line: ".$lnum. ": ".implode(" ",$line));
					err("Invalid instruction '".$line[0]."' on line $lnum: ".implode(" ",$line));
				}
				if(strlen($conversion[strtolower($line[0])]) == 9)
					err($line[0]." expects no arguments but got 1 on line $lnum (".implode(" ",$line).")");
				$out[] = "0".$conversion[strtolower($line[0])].readreg($line[1], 4);
			}	
		}
		if(sizeof($line) == 1)
		{
			if(strlen($conversion[strtolower($line[0])]) != 9)
				err("Missing args to ".$line[0]." on line $lnum (".implode(" ",$line).")");
			$out[] = $conversion[strtolower($line[0])];
		}
		$result[] = implode(" ",$line)." -> ".$out[sizeof($out)-1];
	}
	//file_put_contents("$OUTPUT", print_r(implode("",$out), true));
	//echo "Outputted binary to $OUTPUT containing:\n";
	echo implode("",$out);
?>
</textarea>
<table style="font-family:courier;font-size:10pt">
<th style="text-align:left">Assembly</th><th style="text-align:left">Binary conversion</th>
<?php
foreach($result as $r)
{
	$s = explode(" -> ", $r);
	?>
	<tr><td><?=$s[0]?></td><td><?=$s[1]?></td></tr>
<?php
}

?>
</table>

<?php
function iscmt($e) {
  return strpos($e, "#") !== false;
}
function err($s) {
	die("</textarea><br>Assembly Bug: $s");
}
function readreg($str, $bits) 
{
	global $lnum;
	if($str[0] == '$' && $str[1] == 'r')
	{
		$val = substr($str, 2);
		return sprintf("%0{$bits}s",decbin($val));
	}
	else if($str[0] == '$' && $str[1] == '0')
		return "1110";
	else if(is_numeric($str))
		if($str < 0)
			return substr(decbin($str),$bits*-1);
		else
			return sprintf("%0{$bits}s",decbin($str));
	else if($str == "x")
		return "1111";
	else err("Expected register \$rx or int but got '$str' on line $lnum");
}
?>