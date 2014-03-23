
<?php

class SoapClientCustom extends SoapClient
{
  public function __doRequest($request, $location, $action, $version) {
		// For now we need to remove the duplicate string until we figure a way to keep it from creating in Soap Builder.
		$theRequest = str_replace('<flexFields xsi:type="ns2:SoapFieldValues"/>', '', $request);
       return parent::__doRequest($theRequest, $location, $action, $version);
  }
}

$loginWSDL = "PathToCollabnetWisdl";
$trackerWSDL = "PathToTrackerWSDL";
$trace = TRUE;
$exceptions = true;
$login_array['userName'] = 'Username';
$login_array['possword'] = 'Password';
			
try
{
	$client = new SoapClient($loginWSDL."?wsdl", array('trace' => $trace, 'exceptions' => $exceptions));
	$client->__setLocation($loginWSDL);
	$response = $client->__soapCall('login', $login_array);
	require_once( 'bug_api.php' );
	$tpl_bug = bug_get( $_GET["id"], true ); 
	$tracker_array['sessionId']= $response; // Leave blank
	$tracker_array['trackerId']=  $_GET["trackerId"]; // "tracker1029"; // This must be passed in as a query string param.	
	$tracker_array['title']= bug_format_summary( $_GET["id"], SUMMARY_FIELD );
	$tracker_array['description']= string_display_links( $tpl_bug->description ) ;
	$tracker_array['group']= ""; // leave blank
	$tracker_array['category']= "";
	$tracker_array['status']=  "Open";	
	$tracker_array['customer']= ""; // Leave blank
	$tracker_array['priority']= $_GET["priority"];
	$tracker_array['estimatedEffort']= 0;
	$tracker_array['remainingEffort']= 0;
	$tracker_array['autosumming']= "false";
	$tracker_array['points']= 0;
	$tracker_array['assignedUsername']= "";// can stay blank
	$tracker_array['releaseId']= ""; // Can stay blank
	$tracker_array['planningFolderId']= ""; // Can be blank
	$tracker_array['attachments']= ""; // leave blank
	$names = new ArrayObject();
	$Types = new ArrayObject();
	$Values = new ArrayObject();
	
	
	// Create default values firstSS
	$soapItem=new SoapVar("StepsToReproduce",XSD_STRING,"SOAP-ENC:string",null,'names');
	$names->append($soapItem);
	
	$soapItem=new SoapVar("String",XSD_STRING,"SOAP-ENC:string",null,'types');
	$Types->append($soapItem);
	
	$soapItem = new SoapVar("This is just a test",XSD_STRING,"SOAP-ENC:string",null,'values');
	$Values->append($soapItem);
	
	$noJS = true;
	require('getconfigobject.php');
	$array = array_filter($encodedJSONdata, function($item) {
		return $item->ProjectId == "proj1013";
	});
	// CHeck length if = 1 then
	$FilterdArrayObject = array_values($array)[0];
	$FilteredArtifacts = array_filter($FilterdArrayObject->artifacts, function($item) {
		return $item->value == "artf1234";
	});
	//Checkif lenght == 1 then 
	$FilterdArtifactObject = array_values($FilteredArtifacts)[0];
	
	// $
	foreach ($FilterdArtifactObject -> fields as &$field) {
		// Field Name Mantis, Field Name TeamForge
	}
	// For each item { This will implment a loop for every custom field we want to send in for a particular item.
		$soapItem=new SoapVar("StepsToReproduce",XSD_STRING,"SOAP-ENC:string",null,'names');
		$names->append($soapItem);
		
		$soapItem=new SoapVar("String",XSD_STRING,"SOAP-ENC:string",null,'types');
		$Types->append($soapItem);
		
		$soapItem = new SoapVar("This is just a test",XSD_STRING,"SOAP-ENC:string",null,'values');
		$Values->append($soapItem);
	//}
	$flexFieldArray = new ArrayObject(); 
	$flexFieldArray->append(new SoapVar($names,SOAP_ENC_OBJECT,null,null,'names'));
	$flexFieldArray->append(new SoapVar($Values,SOAP_ENC_OBJECT,null,null,'values'));
	$flexFieldArray->append(new SoapVar($Types,SOAP_ENC_OBJECT,null,null,'types'));
	$tracker_array[]= new SoapVar($flexFieldArray,SOAP_ENC_OBJECT,"tns1:SoapFieldValues",null,'flexFields');

	$trackerClient = new SoapClientCustom($trackerWSDL."?wsdl", array('trace' => $trace, 'exceptions' => $exceptions));
	$trackerClient->__setLocation($trackerWSDL);
	$trackerResponse = $trackerClient->__soapCall('createArtifact2', $tracker_array);//login($xml_array);
	//print_r($trackerResponse);
}
catch (Exception $e)
{
   echo "Error!";
   echo $e -> getMessage ();
}

?>