
<?php
$data = json_decode(file_get_contents('php://input'));
print_r($data);
$my_parent_folder = preg_replace( '~[/\\\\][^/\\\\]*[/\\\\]$~' , DIRECTORY_SEPARATOR , getcwd()."\\" );
$rootURL = $my_parent_folder ."\\configuration\\";

$projectList = "";
foreach ($data -> projects as $object) {
	if($projectList !="")
		$projectList= $projectList."\r\n";
	$projectList= $projectList.$object -> ProjectName."\t".$object -> ProjectId;
	
	$catList = "";
	foreach ($object->artifacts as $artifact){
		if($catList !="")
			$catList= $catList."\r\n";
		$catList= $catList.$artifact -> name."\t".$artifact -> value;
		$fieldList = "";
		foreach ($artifact->fields as $field){
			if($fieldList != "")
				$fieldList= $fieldList."\r\n";
			$fieldList= $fieldList.$field -> name."\t".$field -> value;
		}
		file_put_contents($rootURL.$artifact -> value.".txt", $fieldList);
	}
	//echo $rootURL.$object -> ProjectId;
	file_put_contents($rootURL.$object -> ProjectId.".txt", $catList);
	// WRite string to file.
}
file_put_contents($rootURL."ProjectList.txt", $projectList);

//echo $projectList;

?>