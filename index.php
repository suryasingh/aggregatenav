<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Welcome To Aggregate Navigator</title>
<link rel="stylesheet" href="css/bootstrap.css"/>
<link rel="stylesheet" href="css/styles.css"/>
<!-- Mobile viewport -->
<meta name="viewport" content="width=device-width, user-scalable=no">
    <!-- Some fixes for IE -->

    <!--[if lte IE 8]>
      <link rel="stylesheet" type="text/css" href="css/IE8.css">
      <script src="js/html5.js"></script>
    <![endif]-->

   <!-- Fixes for IE Ends here -->
<script src="gauge.js"></script>
</head>

<body class="home">

    <header class="login-header pull-left">
      <div class="lh-wrp">

         <div class="bp-logo pull-left">
             <h1 style="color:rgb(48, 89, 131);font-weight: 900;">Aggregate Navigator</h1>
         </div>


         <div class="clearfix"></div>

      </div>
    </header>
    <?php if (!empty($_POST["query"])) {
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
		//print_r($attr_query);
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
			//print_r($intersected_val);
			if(count($intersected_val) == count($attr_query)){
				for($i = 0;$i <count($attr_query) ;$i++ ){
					$expstr = explode(".",$attr_query[$i]);
					$childtb = $column_name1[$expstr[0]];
					$queryUser = str_replace($expstr[0],$childtb,$queryUser);
				}
				$factexpld = explode(":",$row[0]);
				$queryUser = str_replace($factexpld[1],$factexpld[0],$queryUser);
				//echo $queryUser;
				break;
			}
		}
	  }
	  ?>
      <div class="signup-wrp">

          <div class="bp-home-content">

                <div class="signup-rightpane pull-right">

                    <div class="signup-form pull-left">
						<h4 style="margin:20px 0px;" class="pull-left"><b>Base Query</b></h4>
                        <form method="post" <?php echo $_SERVER["PHP_SELF"]; ?> class="pull-left">

                          <div class="signup-option pull-left">
							   <textarea rows="6" cols="50" name="query" style="padding:5px;" placeholder="Enter your Query"><?php if(!empty($_POST["query"])){ echo $_POST["query"]; }?></textarea><br/>
                          </div>

                          <input type="submit" value="Run Query" class="signup-btn"/>

                        </form>
						<h4 style="margin:20px 0px;" class="pull-left"><b>Modified Query</b></h4>


						<div class="signup-option pull-left">
							   <textarea rows="6" cols="50" name="query" style="padding:5px;" placeholder="Modified Query"><?php if(!empty($queryUser)){ echo $queryUser; }?></textarea><br/>
                          </div>

                    </div>

                </div>

                <div class="about-leftpane pull-left">
                    <h3>Results</h3>
                    <div class="al-links pull-left">
                      <h4>1. Base Query Time: <span id="basequery"> <?php
						if(!empty($_POST["query"])){
                      		$start = microtime(true);
							$result = mysql_query($_POST["query"], $connection) or die("unable to Query");
							$time_elapsed_secs1 = microtime(true) - $start;
							echo $time_elapsed_secs1 * 1000;
							echo "ms";
						}
                      ?></span></h4>
					  <h4>2. Modified Query Time:  <span id="basequery1"><?php
						if(!empty($_POST["query"])){
                      		$start = microtime(true);
                      		$result = mysql_query($queryUser, $connection) or die("unable to Query");
							$time_elapsed_secs2 = microtime(true) - $start;
							echo $time_elapsed_secs2 * 1000;
							echo "ms";
						}
                      ?></span></h4>
					  <h4>3. Speedup: <span id="basequery2"> <?php
						if(!empty($_POST["query"])){
							echo (int)($time_elapsed_secs1 /$time_elapsed_secs2);
						}
                      ?></span></h4>
                    </div>
                </div>

                <div class="clearfix"></div>

          </div>

      </div>


<footer class="login-footer pull-left clearfix">
<div class="lf-wrp">
<br/><br/><h2 style="text-align:center;">Base Query Time in ms: <span id="basetime"></span></h2>
<canvas id="myCanvas1" width="900" height="300"></canvas><br>
</div>
      </footer><!--footer-->

	<footer class="login-footer pull-left clearfix">
	<div class="lf-wrp">
	<br/><br/><h2 style="text-align:center;">Modified Query Time in ms: <span id="modtime"></span></h2>
	<canvas id="myCanvas2" width="900" height="300"></canvas><br>
	</div>
	      </footer><!--footer-->

			<footer class="login-footer pull-left clearfix">
	         <div class="lf-wrp">

	       		<nav class="lf-nav pull-left">
	                <div class="ownership-wrp">
	                  <span class="pull-left">Under Guidance of:<h4> Dr. Navneet Goyal</h4></span>
	                </div>
	            </nav>

	            <div class="ownership pull-right">
	                <div class="ownership-wrp">
	                  <span class="pull-left">Designed and Developed By:<h4> Surya and Nishant</h4></span>
	                </div>
	            </div>
	          </div>
	 </footer><!--footer-->

	<script>
	function getNewGauge(elemId,canvId,textId){
		var opts = {
		  lines: 12, // The number of lines to draw
		  angle: 0.15, // The length of each line
		  lineWidth: 0.44, // The line thickness
		  pointer: {
		    length: 0.9, // The radius of the inner circle
		    strokeWidth: 0.035, // The rotation offset
		    color: '#000000' // Fill color
		  },
		  limitMax: 'false',   // If true, the pointer will not go past the end of the gauge
		  colorStart: '#6FADCF',   // Colors
		  colorStop: '#8FC0DA',    // just experiment with them
		  strokeColor: '#E0E0E0',   // to see which ones work best for you
		  generateGradient: true
		};
		var text = document.getElementById(elemId).innerHTML;
		var n = text.search("ms");
		var newtet = text.substring(0, n);
		document.getElementById(textId).innerHTML = newtet;
		var target = document.getElementById(canvId); // your canvas element
		var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
		gauge.maxValue = 10000; // set max gauge value
		gauge.animationSpeed = 1; // set animaton speed (32 is default value)
		gauge.set(parseInt(text.substring(0, n))); // set actual value
	}
	getNewGauge('basequery','myCanvas1','basetime');
	getNewGauge('basequery1','myCanvas2','modtime');
	</script>

</body>
</html>
