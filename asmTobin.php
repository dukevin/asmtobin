<textarea rows="25" cols="25" style="margin:0 auto">
<?php
	$INPUT = $_POST['intxt'];
	//$OUTPUT = "instruct.txt";
	$conversion = array(
		'add' => "01001",
		'sub' => "01010",
		'set' => "00111",
		'store' => "00111",
		'load' => "00101",
		'branchifzero' => "00010",
		'branchifpos' => "00011",
		'branchifnooverflow' => "00001",
		'jump' => "00000",
		'compare' => "01011",
		'increment' => "01100",
		'shiftleft' => "01101",
		'done' => "01111",
		'testtopbits' => "01110"
	);
	if(!isset($INPUT) || empty($INPUT))
		die("Nothing was supplied");
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
		if($line[0]=='@')
		{
			// if(sizeof($line) == 1 && isset($line[1])) 
			// {
			// 	$line[1] = str_replace("@","",$line[0]);
			// 	$line[0] = "@";
			// }
			if(sizeof($line) == 2)
				$out[] = "1".(string)sprintf("%08b", decbin($line[1]));
			else if(sizeof($line) == 3) 
			{
				if($line[2]=="x")
					$suf = "";
				else
					$suf = (string)sprintf("%04b", decbin($line[2]));
				$out[] = "1".(string)sprintf("%04b", decbin($line[1]).$suf);
			}
			else
				die("Expects 1 or 2 args to '@' but got ".(sizeof($line)-1)." on line ".$lnum. ": ".implode(" ",$line));
		}
		else
		{
			if(!isset($conversion[strtolower($line[0])])) {
				if(($line[0][0])=="@")
					die("Expects 1 or 2 args to '@' but got ".(sizeof($line)-1)." on line: ".$lnum. ": ".implode(" ",$line));
				die("Invalid instruction '".$line[0]."' on line $lnum: ".implode(" ",$line));
			}
			if(sizeof($line) == 3) 
			{
				if($line[2]=="x")
					$suff2 = "";
				else
					$suff2 = sprintf("%04b", decbin($line[2]));
				$suff = (string)sprintf("%04b", decbin($line[1]).$suff2);
			}
			if(sizeof($line) == 2)
				$suff = (string)sprintf("%04b", decbin($line[1]));
			else
				$suff = $line[0] == 'done' ? '1111' : '0000';
			$out[] = '0'.$conversion[strtolower($line[0])].$suff;
		}
		$result[] = implode(" ",$line)." -> ".$out[sizeof($out)-1];
	}
	//file_put_contents("$OUTPUT", print_r(implode("",$out), true));
	//echo "Outputted binary to $OUTPUT containing:\n";
	echo implode("",$out);
?>
</textarea>
<pre>
<?php echo implode("\n",$result); ?>
</pre>

<?php
function iscmt($e) {
  return strpos($e, "#") !== false;
}
?>