<?php
/*
Plugin Name: Bouwstenen voor de hechting
Description: Vragenlijst, visualisatie en rapportage voor bouwstenen voor de hechting
Version:     1.0
Authors:     HanSei: Erik Jan de Wilde / Konsili: Ivar Hoekstra
 */

ini_set('display_errors', 'On');
//$naam = plugin_dir_path(__FILE__) . "/models/bouwstenen-db.php";
//include_once $naam;

$requestData = false;

if($_POST) {

	if (array_key_exists("client_id",$_POST)){
		$data['client_id'] =$_POST["client_id"];
		$data['werksoort'] =$_POST["werksoort"];
		echo json_encode($data);
		exit;
		//echo 'hallo daar';
		//$requestData = $_POST;
		//AIT_ajax($requestData);	
	}

}

function AIT_ajax($requestData) 
{
	$WpDb = new BsDb();
	//$PpibUser = new PpibUser(get_current_user_id());
	
	if($requestData) 
	{
		echo "okidoki";
		// process received data, create input obj for WpDb->setSurveyResponse()
		$incoming = json_decode(json_encode($requestData['json'])); 
		$dataToSore = (object)[];
		$dataToSore = $incoming->allData->meta;
		$dataToSore->itemid = $incoming->currentItem;
		$dataToSore->value = $incoming->allData->responses->{'itemId'.$incoming->currentItem};
		// store in db
		$storeResult = $WpDb->setSurveyResponse($dataToSore);
		// give response
		//echo json_encode(array('ppib-rest-error'=>$dataToSore));
		if(!$storeResult) {echo json_encode(array('ppib-rest-error'=>'Response niet (goed) opgeslagen in database.'));}
		else {echo json_encode($dataToSore);}
	} 
	else 
	{
		echo "Geen data ontvangen.";
	}
} 