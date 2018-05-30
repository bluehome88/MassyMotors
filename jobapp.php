<?php

try {
   $srv = mysql_connect("209.61.166.106","hilofood","P@55word");
   if ($srv){
       mysql_select_db("hilofood_webdata");
   } else {
       die('Connection error');
   }
} catch (Exception $e){
    
}

if(!empty($_POST)){	
    $data = $_POST;		$updates = array();

	$tblDef = "";

	$tblVals = "";

	foreach ($data as $key=>$value){

		if (substr($key, 0, 4) == "frm_"){

			$value = mysql_escape_string(trim($value));

	

			if(empty($value)) continue;

	

			$tblHdr = substr($key, 4);

							if($tblHdr == "job_alerts") $value = 1;			

			if(strlen($value) > 0){

				$updates[$tblHdr] = $value;

				$tblDef .= "`".$tblHdr."`,";

				$tblVals .= '"'.$value.'",';

			}

		}

	}		//$tblDef = substr($tblDef, 0,strlen($tblDef)-1);

	//$tblVals = substr($tblVals, 0,strlen($tblVals)-1);	

	// HANDLE CV File

	if(!empty($_FILES)){

		try {

			$portalUploads = realpath(__DIR__)."/data-portal/library/Hilo/_upload/";
				

			$tempFile = $_FILES["frm_cv"]["tmp_name"];

			$newName = date("Y-m-d H:i:s")."_".$_FILES["frm_cv"]["name"];

				

			move_uploaded_file($tempFile,$portalUploads . $newName);

				

			$tblDef .= "`cv`";

			$tblVals .= "\"$newName\"";

		} catch(Exception $e){

			

		}

	} else {

		$tblDef = substr($tblDef, 0,strlen($tblDef)-1);

		$tblVals = substr($tblVals, 0,strlen($tblVals)-1);

	}			
	$query = "INSERT INTO `web_hr_applications` (".$tblDef.") VALUES(".$tblVals.")";

		$result = mysql_query($query);

	if(!$result){

		$error = "We could not submit your info at this time. Please try again or contact Customer Services. ";

	} else {		$error = "Your application has been successfully submitted.";	}} else {
	    $error = 'No data received';
	}
	
	die($error);
	?>