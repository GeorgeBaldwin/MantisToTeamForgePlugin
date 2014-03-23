<?php
$text = file_get_contents(getcwd() . "\\Plugins\ExportToTeamForge\configuration\projectList.txt");
$lines = explode("\n", $text);
$outputArrayString = "[";
$counterone = 0;
if(!isset($noJS))
	$noJS = false;
	

// Foreach will open every project/artifact file and load attributes and load them into a json string or object.
foreach ($lines as  $line) {
	$columns = split("\t", $line);
	// For each Project we also need to download the artificat list
	$artifactText = file_get_contents(getcwd() . "\\Plugins\ExportToTeamForge\\configuration\\".preg_replace( "/\r|\n/", "", $columns[1] ).".txt");
	$artifactLines = explode("\n", $artifactText);
	$artifactArray = "";
	$countertwo = 0;
	foreach ($artifactLines as  $artifactLine) {
		$artrifactColumn = split("\t", $artifactLine);
		// Now we want to get all fields associated with an artifact
		$thepath = getcwd() . "\\Plugins\ExportToTeamForge\configuration\projectList.txt";
		if($artrifactColumn[1] != "")
		{
			$fieldText = file_get_contents(getcwd() . "\\Plugins\ExportToTeamForge\\configuration\\".preg_replace( "/\r|\n/", "", $artrifactColumn[1] ).".txt");
			$fieldLines = explode("\n", $fieldText);
			$fieldArray = "";
			$counterthree = 0;
			foreach ($fieldLines as  $fieldLine) {
				$fieldColumn = split("\t", $fieldLine);
				if($counterthree > 0)
					$fieldArray = $fieldArray.',';
				$fieldArray = $fieldArray.' {"name":"'.$fieldColumn[0].'","value":"'.preg_replace( "/\r|\n/", "", $fieldColumn[1] ).'"}';
				$counterthree = 2;
			}
		}
		if(count($artrifactColumn) > 1)
		{
			if($countertwo > 0)
				$artifactArray = $artifactArray.',';
			$artifactArray = $artifactArray.' {"name":"'.$artrifactColumn[0].'","value":"'.preg_replace( "/\r|\n/", "", $artrifactColumn[1] ).'", "fields":['.$fieldArray.']}';
			$countertwo = 2;
		}
	}
	if($counterone > 0)
		$outputArrayString = $outputArrayString.','; //{"ProjectName":"'.$columns[0].'","ProjectId":"'.preg_replace( "/\r|\n/", "", $columns[1] ).'","artifacts":['.$artifactArray.']}';
	$outputArrayString = $outputArrayString.'{"ProjectName":"'.$columns[0].'","ProjectId":"'.preg_replace( "/\r|\n/", "", $columns[1] ).'","artifacts":['.$artifactArray.']}';
	$counterone = 2;
}
$outputArrayString = $outputArrayString.']';


if($noJS) {
	// If no JS.. then we will convert JS string we built into a PHP Object. The first thing we will do with it is Get all 
	// fields for a given project->artifact and pass them in to the TeamForge API.
	$encodedJSONdata = json_decode($outputArrayString);
}
else {
	echo 'var projectList = new Object();';
	echo ' projectList.selectedItems = new Object();';
	echo ' projectList.selectedItems.artifacts = new ko.observableArray();';
	echo ' projectList.selectedItems.fields = new ko.observableArray();';
	echo ' projectList.projectSelected = new ko.observable("none");';
	echo ' projectList.catSelected = new ko.observable("none");';
	echo ' projectList.fieldSelected  = new ko.observable("none");';
	echo ' projectList.projects = '.$outputArrayString.";";
}
?>