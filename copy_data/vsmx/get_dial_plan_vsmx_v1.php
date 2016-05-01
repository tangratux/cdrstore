<?php
ini_set('memory_limit', '4096M');
ini_set('max_execution_time', 3000);
$startTime = microtime(true);

echo "GET_DIALING_PLAN";


//etablissement connection PDO with DevWeb
try {$dbw= new PDO("mysql:host=91.230.169.100;dbname=voipswitch_all", 'root', 'vesnet@home');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}


$query = "SELECT * FROM `voipswitch_all`.`liste_des_vsm` WHERE `traitement_auto` = 1 AND `traitement_manuel` = 1";
$res_req=$dbw->query($query);
$liste_des_vsm=$res_req->fetchAll(PDO::FETCH_BOTH);
//var_dump($liste_des_vsm);





foreach ($liste_des_vsm as $vsm)
{
  echo "<p>".$vsm['vsm_name']." : ".$vsm['ip_adresse']."</p>";

  $dbh_host = $vsm['ip_adresse'];
  $dbh_name = "voipswitch";
  $dbh_user = $vsm['mysql_user'];
  $dbh_pass = $vsm['mysql_pass'];
  $dbh = new PDO("mysql:host=$dbh_host;dbname=$dbh_name", $dbh_user, $dbh_pass);

  // Recuperation des informations du VSM
  $query = "SELECT * FROM `voipswitch`.`dialingplan` ";
  $res_req=$dbh->query($query);
  $dialplan_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
  //var_dump($dialplan_dump);

  $query = "SELECT * FROM `voipswitch`.`clienttypes` ";
  $res_req=$dbh->query($query);
  $clientType_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
  //var_dump($clientType_dump);

  $query = "SELECT * FROM `voipswitch`.`clientsip` ";
  $res_req=$dbh->query($query);
  $wholesale_clients_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
  //var_dump($wholesale_clients_dump);

  $query = "SELECT * FROM `voipswitch`.`clientsshared` ";
  $res_req=$dbh->query($query);
  $retail_clients_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
  //var_dump($retail_clients_dump);

  $query = "SELECT * FROM `voipswitch`.`gateways` ";
  $res_req=$dbh->query($query);
  $gateways_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
  //var_dump($gateways_dump);

  $query = "SELECT * FROM `voipswitch`.`gatekeepers` ";
  $res_req=$dbh->query($query);
  $gatekeepers_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
  //var_dump($gateways_dump);

  // END Recuperation des informations du VSM


  // INSERT INTO TABLES

  // Insert into clientypes_all
  $query = "DELETE IGNORE FROM `voipswitch_all`.`clienttypes_all` WHERE `vsm_name` LIKE '".$vsm['vsm_name']."' ;";
  if(($dbw->query($query)) === FALSE){echo "<p>DELETE NOK : QUERY : $query</p>"; exit("ERROR sur DELETE table `voipswitch_all`.`clienttypes_all`. Verifier l'acces à la base ou si la table existe !");}
  foreach($clientType_dump as $clientType_line)
  {
    $query = "INSERT INTO `voipswitch_all`.`clienttypes_all` (`vsm_name`, `id_client_type`, `client_type_name`)
                                                      VALUES ('".$vsm['vsm_name']."','".$clientType_line['id_client_type']."','".$clientType_line['client_type_name']."');";
    if(($dbw->query($query)) === FALSE){echo "<p>INSERT NOK : QUERY : $query</p>";}
  }
  // END Insert into clientypes_all


  // Insert into clientsip_all (WHOLESALE CLIENTS)
  $query = "DELETE IGNORE FROM `voipswitch_all`.`clientsip_all` WHERE `vsm_name` LIKE '".$vsm['vsm_name']."' ;";
  if(($dbw->query($query)) === FALSE){echo "<p>DELETE NOK : QUERY : $query</p>"; exit("ERROR sur DELETE table `voipswitch_all`.`clientsip_all`. Verifier l'acces à la base ou si la table existe !");}
  foreach($wholesale_clients_dump as $wholesale_clients_line)
  {
    // bug sur les VSM de Gigalis, le champ free_seconds n'existe pas
    if(!isset($wholesale_clients_line['free_seconds'])) {$wholesale_clients_line['free_seconds'] = 0; }
    $query = "INSERT INTO `voipswitch_all`.`clientsip_all` (
                                                              `vsm_name`,
                                                              `id_client`,
                                                              `login`,
                                                              `password`,
                                                              `type`,
                                                              `id_tariff`,
                                                              `account_state`,
                                                              `tech_prefix`,
                                                              `id_reseller`,
                                                              `type2`,
                                                              `type3`,
                                                              `id_intrastate_tariff`,
                                                              `id_currency`,
                                                              `codecs`,
                                                              `primary_codec`,
                                                              `free_seconds`,
                                                              `video_codecs`,
                                                              `video_primary_codec`,
                                                              `fax_codecs`,
                                                              `fax_primary_codec`,
                                                              `id_cli_map`,
                                                              `limit_cps`
                                                            )
                                                  VALUES    (
                                                              '".$vsm['vsm_name']."',
                                                              '".$wholesale_clients_line['id_client']."',
                                                              '".$wholesale_clients_line['login']."',
                                                              '".$wholesale_clients_line['password']."',
                                                              '".$wholesale_clients_line['type']."',
                                                              '".$wholesale_clients_line['id_tariff']."',
                                                              '".$wholesale_clients_line['account_state']."',
                                                              '".$wholesale_clients_line['tech_prefix']."',
                                                              '".$wholesale_clients_line['id_reseller']."',
                                                              '".$wholesale_clients_line['type2']."',
                                                              '".$wholesale_clients_line['type3']."',
                                                              '".$wholesale_clients_line['id_intrastate_tariff']."',
                                                              '".$wholesale_clients_line['id_currency']."',
                                                              '".$wholesale_clients_line['codecs']."',
                                                              '".$wholesale_clients_line['primary_codec']."',
                                                              '".$wholesale_clients_line['free_seconds']."',
                                                              '".$wholesale_clients_line['video_codecs']."',
                                                              '".$wholesale_clients_line['video_primary_codec']."',
                                                              '".$wholesale_clients_line['fax_codecs']."',
                                                              '".$wholesale_clients_line['fax_primary_codec']."',
                                                              '".$wholesale_clients_line['id_cli_map']."',
                                                              '".$wholesale_clients_line['limit_cps']."'
                                                            );";
    if(($dbw->query($query)) === FALSE){echo "<p>INSERT NOK : QUERY : $query</p>";}
  } // END foreach($wholesale_clients_dump as $wholesale_clients_line)

  // END Insert into clientsip_all (WHOLESALE CLIENTS)


  // Insert into clientsshared_all (RETAIL CLIENTS)
  $query = "DELETE IGNORE FROM `voipswitch_all`.`clientsshared_all` WHERE `vsm_name` LIKE '".$vsm['vsm_name']."' ;";
  if(($dbw->query($query)) === FALSE){echo "<p>DELETE NOK : QUERY : $query</p>"; exit("ERROR sur DELETE table `voipswitch_all`.`clientsshared_all`. Verifier l'acces à la base ou si la table existe !");}
  foreach($retail_clients_dump as $retail_clients_line)
  {
    // bug sur les VSM de Gigalis, le champ free_seconds n'existe pas
    if(!isset($retail_clients_line['free_seconds'])) {$retail_clients_line['free_seconds'] = 0; }
    $query = "INSERT INTO `voipswitch_all`.`clientsshared_all` (
                                                              `vsm_name`,
                                                              `id_client`,
                                                              `login`,
                                                              `password`,
                                                              `type`,
                                                              `id_tariff`,
                                                              `account_state`,
                                                              `tech_prefix`,
                                                              `id_reseller`,
                                                              `type2`,
                                                              `type3`,
                                                              `id_intrastate_tariff`,
                                                              `id_currency`,
                                                              `codecs`,
                                                              `primary_codec`,
                                                              `free_seconds`,
                                                              `id_tariff_vod`,
                                                              `video_codecs`,
                                                              `video_primary_codec`,
                                                              `fax_codecs`,
                                                              `fax_primary_codec`,
                                                              `web_password`,
                                                              `id_cli_map`
                                                            )
                                                  VALUES    (
                                                              '".$vsm['vsm_name']."',
                                                              '".$retail_clients_line['id_client']."',
                                                              '".$retail_clients_line['login']."',
                                                              '".$retail_clients_line['password']."',
                                                              '".$retail_clients_line['type']."',
                                                              '".$retail_clients_line['id_tariff']."',
                                                              '".$retail_clients_line['account_state']."',
                                                              '".$retail_clients_line['tech_prefix']."',
                                                              '".$retail_clients_line['id_reseller']."',
                                                              '".$retail_clients_line['type2']."',
                                                              '".$retail_clients_line['type3']."',
                                                              '".$retail_clients_line['id_intrastate_tariff']."',
                                                              '".$retail_clients_line['id_currency']."',
                                                              '".$retail_clients_line['codecs']."',
                                                              '".$retail_clients_line['primary_codec']."',
                                                              '".$retail_clients_line['free_seconds']."',
                                                              '".$retail_clients_line['id_tariff_vod']."',
                                                              '".$retail_clients_line['video_codecs']."',
                                                              '".$retail_clients_line['video_primary_codec']."',
                                                              '".$retail_clients_line['fax_codecs']."',
                                                              '".$retail_clients_line['fax_primary_codec']."',
                                                              '".$retail_clients_line['web_password']."',
                                                              '".$retail_clients_line['id_cli_map']."'
                                                            );";
    if(($dbw->query($query)) === FALSE){echo "<p>INSERT NOK : QUERY : $query</p>";}
  } // END foreach($retail_clients_dump as $retail_clients_line)

  // END Insert into clientsshared_all (RETAIL CLIENTS)


  // Insert into gateways_all (GATEWAYS)
  $query = "DELETE IGNORE FROM `voipswitch_all`.`gateways_all` WHERE `vsm_name` LIKE '".$vsm['vsm_name']."' ;";
  if(($dbw->query($query)) === FALSE){echo "<p>DELETE NOK : QUERY : $query</p>"; exit("ERROR sur DELETE table `voipswitch_all`.`gateways_all`. Verifier l'acces à la base ou si la table existe !");}
  foreach($gateways_dump as $gateways_line)
  {
    $query = "INSERT INTO `voipswitch_all`.`gateways_all` (
                                                              `vsm_name`,
                                                              `id_route`,
                                                              `description`,
                                                              `ip_number`,
                                                              `h323_id`,
                                                              `type`,
                                                              `call_limit`,
                                                              `id_tariff`,
                                                              `tech_prefix`,
                                                              `codecs`,
                                                              `video_codecs`,
                                                              `fax_codecs`,
                                                              `id_intrastate_tariff`,
                                                              `transport_type`
                                                            )
                                                  VALUES    (
                                                              '".$vsm['vsm_name']."',
                                                              '".$gateways_line['id_route']."',
                                                              '".$gateways_line['description']."',
                                                              '".$gateways_line['ip_number']."',
                                                              '".$gateways_line['h323_id']."',
                                                              '".$gateways_line['type']."',
                                                              '".$gateways_line['call_limit']."',
                                                              '".$gateways_line['id_tariff']."',
                                                              '".$gateways_line['tech_prefix']."',
                                                              '".$gateways_line['codecs']."',
                                                              '".$gateways_line['video_codecs']."',
                                                              '".$gateways_line['fax_codecs']."',
                                                              '".$gateways_line['id_intrastate_tariff']."',
                                                              '".$gateways_line['transport_type']."'
                                                            );";
    if(($dbw->query($query)) === FALSE){echo "<p>INSERT NOK : QUERY : $query</p>";}
  } // END foreach($gateways_dump as $gateways_line

  // END Insert into gateways_all (GATEWAYS)



  // Insert into gatekeepers_all (GATEKEEPERS)
  $query = "DELETE IGNORE FROM `voipswitch_all`.`gatekeepers_all` WHERE `vsm_name` LIKE '".$vsm['vsm_name']."' ;";
  if(($dbw->query($query)) === FALSE){echo "<p>DELETE NOK : QUERY : $query</p>"; exit("ERROR sur DELETE table `voipswitch_all`.`gatekeepers_all`. Verifier l'acces à la base ou si la table existe !");}
  foreach($gatekeepers_dump as $gatekeepers_line)
  {
    $query = "INSERT INTO `voipswitch_all`.`gatekeepers_all` (
                                                              `vsm_name`,
                                                              `id_route`,
                                                              `description`,
                                                              `ip_number`,
                                                              `h323_id`,
                                                              `e164_id`,
                                                              `ttl`,
                                                              `token`,
                                                              `type`,
                                                              `gk_name`,
                                                              `id_tariff`,
                                                              `string1`,
                                                              `call_limit`,
                                                              `tech_prefix`,
                                                              `codecs`,
                                                              `video_codecs`,
                                                              `fax_codecs`,
                                                              `authentication_name`,
                                                              `authentication_password`,
                                                              `id_intrastate_tariff`,
                                                              `transport_type`
                                                            )
                                                  VALUES    (
                                                              '".$vsm['vsm_name']."',
                                                              '".$gatekeepers_line['id_route']."',
                                                              '".$gatekeepers_line['description']."',
                                                              '".$gatekeepers_line['ip_number']."',
                                                              '".$gatekeepers_line['h323_id']."',
                                                              '".$gatekeepers_line['e164_id']."',
                                                              '".$gatekeepers_line['ttl']."',
                                                              '".$gatekeepers_line['token']."',
                                                              '".$gatekeepers_line['type']."',
                                                              '".$gatekeepers_line['gk_name']."',
                                                              '".$gatekeepers_line['id_tariff']."',
                                                              '".$gatekeepers_line['string1']."',
                                                              '".$gatekeepers_line['call_limit']."',
                                                              '".$gatekeepers_line['tech_prefix']."',
                                                              '".$gatekeepers_line['codecs']."',
                                                              '".$gatekeepers_line['video_codecs']."',
                                                              '".$gatekeepers_line['fax_codecs']."',
                                                              '".$gatekeepers_line['authentication_name']."',
                                                              '".$gatekeepers_line['authentication_password']."',
                                                              '".$gatekeepers_line['id_intrastate_tariff']."',
                                                              '".$gatekeepers_line['transport_type']."'
                                                            );";
    if(($dbw->query($query)) === FALSE){echo "<p>INSERT NOK L296 : QUERY : $query</p>";}
  } // END foreach($gatekeepers_dump as $gatekeepers_line

  // END Insert into gatekeepers_all (GATEWAYKEEPERS)


  // Insert into dialplan_all
  $i = 0;
  foreach($dialplan_dump as $dialplan_line)
  {
    $i++;

    //echo "<p>insertDialplan $i :</p>";
    //var_dump($dialplan_line);
    $query = "SELECT * FROM `voipswitch_all`.`dialingplan_all` WHERE `vsm_name` LIKE '".$vsm['vsm_name']."' AND `id_dialplan` = ".$dialplan_line['id_dialplan'].";";
    //echo "<p>$query</p>";
    $res_req=$dbw->query($query);
    $select_result_dialingplan_all=$res_req->fetchAll(PDO::FETCH_BOTH);

    // Recuperations de la route (trunk, client...)

    $real_priority = "99999";
    $trunk_sip  = "NSP";

    // Recuperation du trunk

    // MODIFICATIONS POUR VSM2, VSM3, VSM4, VSM5
    if($dialplan_line['route_type'] == 5) { $dialplan_line['route_type'] = 32;}

    $query = "SELECT `client_type_name` FROM `voipswitch_all`.`clienttypes_all` WHERE `id_client_type` = ".$dialplan_line['route_type']." ;";
    //echo "<p>$query</p>";
    $res_req=$dbw->query($query);
    $select_result_dialplan=$res_req->fetchAll(PDO::FETCH_BOTH);
    if(empty($select_result_dialplan))
      { var_dump($dialplan_line);exit("IMPOSSSIBLE DE DETERMINER LE TYPE DU TRUNK SIP. Query : $query");}
    else
      {
        if($select_result_dialplan[0][0] === "Wholesale clients") {$select_table = "gateways_all"; $select_column = "description"; $where_column = "id_route";}
        elseif($select_result_dialplan[0][0] === "GW/Proxy clients") {$select_table = "gateways_all"; $select_column = "description"; $where_column = "id_route";}
        elseif($select_result_dialplan[0][0] === "Retail clients") {$select_table = "clientsshared_all"; $select_column = "login"; $where_column = "id_client";}
        elseif($select_result_dialplan[0][0] === "Client Common" ) {$select_table = "clientsshared_all"; $select_column = "login"; $where_column = "id_client";}
        elseif($select_result_dialplan[0][0] === "GK/Registrar clients") {$select_table = "gatekeepers_all"; $select_column = "description"; $where_column = "id_route";}
        else
        {
          $caller_trunk  = "NSP";
          $caller_trunk_type = "NSP";
        }

        $trunk_type = $select_result_dialplan[0][0];
        $query = "SELECT `".$select_column."` FROM `voipswitch_all`.`".$select_table."` WHERE `".$where_column."` = ".$dialplan_line['id_route']." AND `vsm_name` LIKE '".$vsm['vsm_name']."';";
        //echo "<p style=\"color:red\">TRUNK SEARCH QUERY : $query</p>";
        $res_req=$dbw->query($query);
        $select_result=$res_req->fetchAll(PDO::FETCH_BOTH);
        //var_dump($select_result);
        if(empty($select_result)) {$trunk_sip = "DELETED";} // La GW ou le RC a ete efface depuis VSM
        else {$trunk_sip  = $select_result[0][0];}


      }

    $tranche_debut = "NSP";
    $tranche_fin = "NSP";
    $tranche_nb_sda = "0";
    $client = "NSP";
    $commentaire = "NSP";

    // END Recuperations de la route (trunk, client...)

    if(empty($select_result_dialingplan_all)) // pas d'entree dans la table dialingplan_all
    {
      //echo "<p>Nouvelle entree</p>";
      $query = "INSERT INTO `voipswitch_all`.`dialingplan_all`
                                    (
                                        `id_dialplan`,
                                        `vsm_name`,
                                        `telephone_number`,
                                        `priority`,
                                        `real_priority`,
                                        `route_type`,
                                        `tech_prefix`,
                                        `dial_as`,
                                        `id_route`,
                                        `call_type`,
                                        `type`,
                                        `from_day`,
                                        `to_day`,
                                        `from_hour`,
                                        `to_hour`,
                                        `balance_share`,
                                        `fields`,
                                        `call_limit`,
                                        `trunk_sip`,
                                        `trunk_type`,
                                        `tranche_debut`,
                                        `tranche_fin`,
                                        `tranche_nb_sda`,
                                        `client`,
                                        `commentaire`
                                    )
                          VALUES    (
                                        '".$dialplan_line['id_dialplan']."',
                                        '".$vsm['vsm_name']."',
                                        '".$dialplan_line['telephone_number']."',
                                        '".$dialplan_line['priority']."',
                                        '".$real_priority."',
                                        '".$dialplan_line['route_type']."',
                                        '".$dialplan_line['tech_prefix']."',
                                        '".$dialplan_line['dial_as']."',
                                        '".$dialplan_line['id_route']."',
                                        '".$dialplan_line['call_type']."',
                                        '".$dialplan_line['type']."',
                                        '".$dialplan_line['from_day']."',
                                        '".$dialplan_line['to_day']."',
                                        '".$dialplan_line['from_hour']."',
                                        '".$dialplan_line['to_hour']."',
                                        '".$dialplan_line['balance_share']."',
                                        '".$dialplan_line['fields']."',
                                        '".$dialplan_line['call_limit']."',
                                        '".$trunk_sip."',
                                        '".$trunk_type."',
                                        '".$tranche_debut."',
                                        '".$tranche_fin."',
                                        '".$tranche_nb_sda."',
                                        '".$client."',
                                        '".$commentaire."'
                                    )
              ;";
      //echo "<p>$query</p>";
      if(($dbw->query($query)) === FALSE){echo "<p>INSERT NOK : QUERY : $query</p>"; exit(0);}
      //else{echo "<p>INSERT OK QUERY : $query</p>";}

    }



  } // END foreach($dialplan_dump as $dialplan_line)


  // ENDInsert into dialplan_all


  // END INSERT INTO TABLES

} // END foreach ($liste_des_vsm as $vsm)
echo "<p>Time:  " . number_format(( microtime(true) - $startTime), 4) . " Seconds </p>";
echo "<h1>$i Entrees</h1>";
exit("END OF THE SCRIPT");














//etablissement connection PDO with VSM
try {$dbh= new PDO("mysql:host=194.177.42.215;dbname=voipswitch", 'readroot', '2sN7Vmr8eb');}
catch(PDOException $e){echo getcwd(); echo $e->getMessage();}



$query = "SELECT * FROM `voipswitch`.`dialingplan` ";
$res_req=$dbh->query($query);
$dialplan_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
//var_dump($dialplan_dump);


$query = "SELECT * FROM `voipswitch`.`clienttypes` ";
$res_req=$dbh->query($query);
$clientType_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
//var_dump($clientType_dump);


$query = "SELECT * FROM `voipswitch`.`clientsip` ";
$res_req=$dbh->query($query);
$wholesale_clients_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
//var_dump($wholesale_clients_dump);

$query = "SELECT * FROM `voipswitch`.`clientsshared` ";
$res_req=$dbh->query($query);
$retail_clients_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
//var_dump($retail_clients_dump);


$query = "SELECT * FROM `voipswitch`.`gateways` ";
$res_req=$dbh->query($query);
$gateways_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
//var_dump($gateways_dump);


$query = "SELECT * FROM `voipswitch_all`.`test` ";
$res_req=$dbw->query($query);
$test_dump=$res_req->fetchAll(PDO::FETCH_BOTH);
var_dump($test_dump);





foreach ($dialplan_dump as $one_line)
{
  echo "<p>--------------------------------</p>";
  foreach ($one_line as $key => $value) {
    echo "<p>$key : $value </p>";
  }
  echo "<p>--------------------------------</p>";
}

echo "<p>Time:  " . number_format(( microtime(true) - $startTime), 4) . " Seconds </p>";
echo "<h1>END OF THE SCRIPT</h1>";
?>
