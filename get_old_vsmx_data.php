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

if(isset($_GET['rattrappe_retard']))
{
  $journee = getdate((time()-86400));
}
else
{
  $journee = getdate(time());
  $journee= $journee['year']."-".$journee['mon']."-".$journee['mday'];
}


if(isset($_GET['journee']))
{
  $journee = $_GET['journee'];
}


// Configuration manuelle de la date !
//$journee =   '2015-11-02';


echo "<p>$journee</p>"; //exit(0);




?>
