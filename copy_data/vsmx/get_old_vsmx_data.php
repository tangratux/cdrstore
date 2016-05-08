<?php
ini_set('memory_limit', '4096M');
ini_set('max_execution_time', 3000);
$startTime = microtime(true);
function echoP($message) {  echo "<p>$message</p>";  }

echo "<h1>GET_OLD_VSM_CALLS</h1>";


// VARIABLES
$no_inserts = 0; // 0 -> les inserts sont executes
                 // 1 -> il n'y a pas de modifications des bases

$compteur_dbu = 1; // compteur pour le switch des differentes connexions à la DB
$inserted_query = 0;


// DATES
// UNIX timestamps

//etablissement connection PDO with C170 pour le tracking des journées
try {$db_vesnet= new PDO("mysql:host=10.10.10.111;dbname=vesnet", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}

$query = " SELECT DISTINCT `date` FROM `track_data_copy` WHERE `nb_cdr_inserted`> 0 AND `time_needed` > 0 ORDER BY `date` ASC LIMIT 1;";
$res_req=$db_vesnet->query($query);
$smaller_date=$res_req->fetchAll(PDO::FETCH_BOTH);
if(empty($smaller_date))
  {
    $journee = getdate(time());
    $journee= $journee['year']."-".$journee['mon']."-".$journee['mday'] ;
  }
else
  {
    $smaller_date = $smaller_date[0][0];
    $journee = date_create("$smaller_date");
    date_sub($journee, date_interval_create_from_date_string('1 day'));
    $journee= date_format($journee, 'Y-m-d');
  }

$content=file_get_contents("http://10.10.10.111/CDRstore/copy_data/vsmx/get_calls_vsmx_v3.php?journee=$journee");

echo "Get Latest CSVs : "; echo  $content;




// Configuration manuelle de la date !
//$journee =   '2015-11-02';


echo "<p>$journee</p>"; //exit(0);









?>
