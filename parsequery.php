<?php
	require_once dirname(__FILE__).'/src/PHPSQLParser.php';
	require_once 'db.php';
	$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die("unable to connect to database");
	mysql_select_db(DB_NAME, $connection);
	$queryUser = $_POST['query'];
	$parser = new PHPSQLParser($queryUser, true);
	$my_parsed_query = $parser->parsed;
	//print_r($my_parsed_query);
	$GROUP_part = $my_parsed_query["GROUP"];
	$attr_query = array();
	for($i =0 ;$i<count($GROUP_part);$i++){
		array_push(	$attr_query,$GROUP_part[$i]["base_expr"]);
	}
	print_r($attr_query);
	$query = "SELECT fact_name,dim1_name,dim2_name,dim3_name FROM nmetadata ORDER BY size";
		//$query = "SHOW COLUMNS FROM product";
	$result = mysql_query($query, $connection) or die("unable to Query");
	while ($row = mysql_fetch_array($result)) {
		$column_name = array();
		$column_name1 = array();
		//print_r($row)."<br/>";
		for($i = 1; $i<4;$i++){
			$newold = explode(":",$row[$i]);
			$column_name1[$newold[1]] = $newold[0];
			$query1 = "SHOW COLUMNS FROM ".$newold[0];
			$result1 = mysql_query($query1, $connection) or die("unable to Query1");
			while ($row1 = mysql_fetch_array($result1)) {
				array_push(	$column_name,$newold[1].'.'.$row1[0]);
			}
		}
		$intersected_val=array_intersect($attr_query,$column_name);
		print_r($intersected_val);
		if(count($intersected_val) == count($attr_query)){
			for($i = 0;$i <count($attr_query) ;$i++ ){
				$expstr = explode(".",$attr_query[$i]);
				$childtb = $column_name1[$expstr[0]];
				$queryUser = str_replace($expstr[0],$childtb,$queryUser);
			}
			//echo "fact table".$row[0];
			echo $queryUser;
			break;
		}
	}

?>
