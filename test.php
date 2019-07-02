<?php
	$array = array('a','b','c','d');
	#$array = array_values(array_diff($array, array("c",'b')));
	array_push($array,"blue","yellow");
	$name = 'joel';
	echo $name,$name;
	foreach($array as $arr)
	{	
		if ($arr == 'blue')
		{
			echo "Found";
			break;
		}
		print_r($arr);
		echo '<br>';
	}
?>
