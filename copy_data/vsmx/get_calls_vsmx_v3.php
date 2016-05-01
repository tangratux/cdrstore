<?php
ini_set('memory_limit', '6096M');
ini_set('max_execution_time', 3000);
$startTime = microtime(true);
function echoP($message) {  echo "<p>$message</p>";  }

echo "<h1>GET_CALLS</h1>";

echoP("Change log : ");
echoP(" - Modifications pour accepter VSM5 ");
echoP(" - Modifications pour tourner toutes les 15 minutes ");
echoP(" - Ajout de l'option NO INSERT");

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


echo "$journee"; //exit(0);

//etablissement connection PDO with DevWeb pour les SELECT
try {$dbw= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}

//etablissement connection PDO with DevWeb pour les REPLACE
try {$dbr= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}

//etablissement 8 connection PDO with DevWeb for UPDATES
try {$dbu1= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu2= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu3= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu4= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu5= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu6= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu7= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu8= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu9= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu10= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu11= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu12= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu13= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu14= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu15= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu16= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu17= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu18= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu19= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}
try {$dbu20= new PDO("mysql:host=10.10.10.111;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}





// Recuperation des apppels par vsm

$query = "SELECT * FROM `voipswitch_all`.`liste_des_vsm` WHERE `traitement_auto` = 1 AND `traitement_manuel` = 1";
$res_req=$dbw->query($query);
$liste_des_vsm=$res_req->fetchAll(PDO::FETCH_BOTH);
//var_dump($liste_des_vsm);
$j=0;
foreach ($liste_des_vsm as $vsm) // Boucle pour chaque VSM
{
  $j++;
  $inserted_query = 0;
  //if($j > 2){ exit("j = $j") ;}
  echo "<p>".$vsm['vsm_name']." : ".$vsm['ip_adresse']."</p>";
  $dbh_host = $vsm['ip_adresse'];
  $dbh_name = "voipswitch";
  $dbh_user = $vsm['mysql_user'];
  $dbh_pass = $vsm['mysql_pass'];
  $dbh = new PDO("mysql:host=$dbh_host;dbname=$dbh_name", $dbh_user, $dbh_pass);

    // Recuperation des calls du VSM
  $query = "SELECT * FROM `voipswitch`.`calls` WHERE `call_start` BETWEEN '$journee 00:00:00' AND '$journee 23:59:59'  ;";
  $res_req=$dbh->query($query);
  $calls_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
  //echo "<p>$query</p>";var_dump($calls_dump);exit(0);

    // END Recuperation des calls du VSM


  // INSERT INTO TABLES


  // Insert into calls_all
  $i = 0;
  foreach ($calls_dump as $calls_line)
  {
    $i++;
    //if($i>10) die("10");
    // Recuperations de la route (trunk, client...)

    $caller_trunk  = "NSP";
    $called_trunk  = "NSP";

    // Recuperation du caller trunk
    // MODIFICATIONS POUR VSM2, VSM3, VSM4, VSM5
    if($calls_line['client_type'] == 5) { $calls_line['client_type'] = 32;  }
    $query = "SELECT `client_type_name` FROM `voipswitch_all`.`clienttypes_all` WHERE `id_client_type` = ".$calls_line['client_type']." ;";
    $res_req=$dbw->query($query);
    $select_result_trunk=$res_req->fetchAll(PDO::FETCH_BOTH);
    if(empty($select_result_trunk))
      { var_dump($calls_line);exit("IMPOSSSIBLE DE DETERMINER LE TYPE DU TRUNK SIP. Query : $query");}
    else
      {
        if($select_result_trunk[0][0] === "Wholesale clients") {$select_table = "clientsip_all"; $select_column = "login"; $where_column = "id_client";}
        elseif($select_result_trunk[0][0] === "GW/Proxy clients") {$select_table = "clientsip_all"; $select_column = "login"; $where_column = "id_client";}
        elseif($select_result_trunk[0][0] === "Retail clients") {$select_table = "clientsshared_all"; $select_column = "login"; $where_column = "id_client";}
        elseif($select_result_trunk[0][0] === "GK/Registrar clients") {$select_table = "gatekeepers_all"; $select_column = "description"; $where_column = "id_route"; }
        else
        {
          $caller_trunk  = "NSP";
          $caller_trunk_type = "NSP";
        }

        $caller_trunk_type = $select_result_trunk[0][0];
        $query = "SELECT `".$select_column."` FROM `voipswitch_all`.`".$select_table."` WHERE `".$where_column."` = ".$calls_line['id_client']." AND `vsm_name` LIKE '".$vsm['vsm_name']."';";
        $res_req=$dbw->query($query);
        $select_result=$res_req->fetchAll(PDO::FETCH_BOTH);
        if(empty($select_result)) {$caller_trunk = "DELETED";} // La GW ou le RC a ete efface depuis VSM
        else {$caller_trunk  = $select_result[0][0];}
      }
    // END Recuperation du caller trunk

    // Recuperation du called trunk

    // MODIFICATIONS POUR VSM2, VSM3, VSM4, VSM5
    if($calls_line['route_type'] == 5) { $calls_line['route_type'] = 32;}
    $query = "SELECT `client_type_name` FROM `voipswitch_all`.`clienttypes_all` WHERE `id_client_type` = ".$calls_line['route_type']." ;";
    $res_req=$dbw->query($query);
    $select_result_trunk=$res_req->fetchAll(PDO::FETCH_BOTH);
    if(empty($select_result_trunk))
    { var_dump($calls_line);exit("IMPOSSSIBLE DE DETERMINER LE TYPE DU TRUNK SIP. Query : $query");}
    else
      {
        if($select_result_trunk[0][0] === "Wholesale clients") {$select_table = "gateways_all"; $select_column = "description"; $where_column = "id_route";}
        elseif($select_result_trunk[0][0] === "GW/Proxy clients") {$select_table = "gateways_all"; $select_column = "description"; $where_column = "id_route";}
        elseif($select_result_trunk[0][0] === "Retail clients") {$select_table = "clientsshared_all"; $select_column = "login"; $where_column = "id_client";}
        elseif($select_result_trunk[0][0] === "GK/Registrar clients") {$select_table = "gatekeepers_all"; $select_column = "description"; $where_column = "id_route";}
        else
        {
          $called_trunk  = "NSP";
          $called_trunk_type = "NSP";
        }

        $called_trunk_type = $select_result_trunk[0][0];
        $query = "SELECT `".$select_column."` FROM `voipswitch_all`.`".$select_table."` WHERE `".$where_column."` = ".$calls_line['id_route']." AND `vsm_name` LIKE '".$vsm['vsm_name']."';";
        $res_req=$dbw->query($query);
        $select_result=$res_req->fetchAll(PDO::FETCH_BOTH);
        if(empty($select_result)) {$called_trunk = "DELETED";} // La GW ou le RC a ete efface depuis VSM
        else {$called_trunk  = $select_result[0][0];}
      }
    // END Recuperation du called trunk

    $query = "REPLACE INTO `voipswitch_all`.`calls_all`
                                ( `id_call_all`,
                                  `vsm_name`,
                                  `caller_trunk`,
                                  `called_trunk`,
                                  `id_call`,
                                  `id_client`,
                                  `ip_number`,
                                  `caller_id`,
                                  `called_number`,
                                  `call_start`,
                                  `call_end`,
                                  `route_type`,
                                  `id_tariff`,
                                  `cost`,
                                  `duration`,
                                  `tariff_prefix`,
                                  `client_type`,
                                  `id_route`,
                                  `pdd`,
                                  `costR1`,
                                  `costR2`,
                                  `costR3`,
                                  `costD`,
                                  `id_reseller`,
                                  `tariffdesc`,
                                  `id_cc`,
                                  `ratio`,
                                  `client_pdd`,
                                  `orig_call_id`,
                                  `term_call_id`,
                                  `id_callback_call`,
                                  `id_cn`,
                                  `dialing_plan_prefix`,
                                  `call_rate`,
                                  `tariff_data`,
                                  `effective_duration`,
                                  `dtmf`,
                                  `call_data`
                                )
                      VALUES    (
                                  :id_call_all,
                                  :vsm_name,
                                  :caller_trunk,
                                  :called_trunk,
                                  :id_call,
                                  :id_client,
                                  :ip_number,
                                  :caller_id,
                                  :called_number,
                                  :call_start,
                                  :call_end,
                                  :route_type,
                                  :id_tariff,
                                  :cost,
                                  :duration,
                                  :tariff_prefix,
                                  :client_type,
                                  :id_route,
                                  :pdd,
                                  :costR1,
                                  :costR2,
                                  :costR3,
                                  :costD,
                                  :id_reseller,
                                  :tariffdesc,
                                  :id_cc,
                                  :ratio,
                                  :client_pdd,
                                  :orig_call_id,
                                  :term_call_id,
                                  :id_callback_call,
                                  :id_cn,
                                  :dialing_plan_prefix,
                                  :call_rate,
                                  :tariff_data,
                                  :effective_duration,
                                  :dtmf,
                                  :call_data
                                )
          ;";

    // ORIGINAL //$res_update = $dbr->prepare($query);
    switch ($compteur_dbu)
    {
      case 1:
        $res_update = $dbu1->prepare($query);
        break;
      case 2:
        $res_update = $dbu2->prepare($query);
        break;
      case 3:
        $res_update = $dbu3->prepare($query);
        break;
      case 4:
        $res_update = $dbu4->prepare($query);
        break;
      case 5:
        $res_update = $dbu5->prepare($query);
        break;
      case 6:
        $res_update = $dbu6->prepare($query);
        break;
      case 7:
        $res_update = $dbu7->prepare($query);
        break;
      case 8:
        $res_update = $dbu8->prepare($query);
        break;
      case 9:
        $res_update = $dbu9->prepare($query);
        break;
      case 10:
        $res_update = $dbu10->prepare($query);
        break;
      case 11:
        $res_update = $dbu11->prepare($query);
        break;
      case 12:
        $res_update = $dbu12->prepare($query);
        break;
      case 13:
        $res_update = $dbu13->prepare($query);
        break;
      case 14:
        $res_update = $dbu14->prepare($query);
        break;
      case 15:
        $res_update = $dbu15->prepare($query);
        break;
      case 16:
        $res_update = $dbu16->prepare($query);
        break;
      case 17:
        $res_update = $dbu17->prepare($query);
        break;
      case 18:
        $res_update = $dbu18->prepare($query);
        break;
      case 19:
        $res_update = $dbu19->prepare($query);
        break;
      case 20:
        $res_update = $dbu20->prepare($query);
        break;
      case 21:
        $compteur_dbu = 0;
        $res_update = $dbu5->prepare($query);
        break;
      default:
        $compteur_dbu = 0;
        $res_update = $dbu15->prepare($query);
    }
    $compteur_dbu++;




    $res_update->bindParam(':id_call_all', $calls_line['id_call_all']);
    $res_update->bindParam(':vsm_name', $vsm['vsm_name']);
    $res_update->bindParam(':caller_trunk', $caller_trunk);
    $res_update->bindParam(':called_trunk', $called_trunk );
    $res_update->bindParam(':id_call', $calls_line['id_call']);
    $res_update->bindParam(':id_client', $calls_line['id_client']);
    $res_update->bindParam(':ip_number', $calls_line['ip_number']);
    $res_update->bindParam(':caller_id', $calls_line['caller_id']);
    $res_update->bindParam(':called_number', $calls_line['called_number']);
    $res_update->bindParam(':call_start', $calls_line['call_start']);
    $res_update->bindParam(':call_end', $calls_line['call_end']);
    $res_update->bindParam(':route_type', $calls_line['route_type']);
    $res_update->bindParam(':id_tariff', $calls_line['id_tariff']);
    $res_update->bindParam(':cost', $calls_line['cost']);
    $res_update->bindParam(':duration', $calls_line['duration']);
    $res_update->bindParam(':tariff_prefix', $calls_line['tariff_prefix']);
    $res_update->bindParam(':client_type', $calls_line['client_type']);
    $res_update->bindParam(':id_route', $calls_line['id_route']);
    $res_update->bindParam(':pdd', $calls_line['pdd']);
    $res_update->bindParam(':costR1', $calls_line['costR1']);
    $res_update->bindParam(':costR2', $calls_line['costR2']);
    $res_update->bindParam(':costR3', $calls_line['costR3']);
    $res_update->bindParam(':costD', $calls_line['costD']);
    $res_update->bindParam(':id_reseller', $calls_line['id_reseller']);
    $res_update->bindParam(':tariffdesc', $calls_line['tariffdesc']);
    $res_update->bindParam(':id_cc', $calls_line['id_cc']);
    $res_update->bindParam(':ratio', $calls_line['ratio']);
    $res_update->bindParam(':client_pdd', $calls_line['client_pdd']);
    $res_update->bindParam(':orig_call_id', $calls_line['orig_call_id']);
    $res_update->bindParam(':term_call_id', $calls_line['term_call_id']);
    $res_update->bindParam(':id_callback_call', $calls_line['id_callback_call']);
    $res_update->bindParam(':id_cn', $calls_line['id_cn']);
    $res_update->bindParam(':dialing_plan_prefix', $calls_line['dialing_plan_prefix']);
    $res_update->bindParam(':call_rate', $calls_line['call_rate']);
    $res_update->bindParam(':tariff_data', $calls_line['tariff_data']);
    $res_update->bindParam(':effective_duration', $calls_line['effective_duration']);
    $res_update->bindParam(':dtmf', $calls_line['dtmf']);
    $res_update->bindParam(':call_data', $calls_line['call_data']);
    if($no_inserts === 0)
      {
        $res_update->execute();
        $inserted_query++;
      }
    // echo "<p>";
    // $res_update->debugDumpParams();
    // echo "</p>";
  } // END foreach ($calls_dump as $calls_line)
  //echo "juste ici : $i tours"; exit("j = $j");

  echo "<p>Inserted Queries : $inserted_query</p>";
} // END foreach ($liste_des_vsm as $vsm) Boucle pour chaque VSM

echo "<p>Time:  " . number_format(( microtime(true) - $startTime), 4) . " Seconds </p>";
echo "<h1>END OF THE SCRIPT</h1>";
exit(5);
?>
