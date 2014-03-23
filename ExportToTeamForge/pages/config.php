<?php
html_page_top( );

?>

<script>

<?php require('getconfigobject.php'); // This gets the JSON Object that holds configuration?>
 
var projectListObservable = ko.mapping.fromJS(projectList);
jQuery(document).ready(function () {
	
	jQuery("#ProjectAdd").click(function() {
		var proj = new Object();
			proj.ProjectName = jQuery("#InputProjectName").val();
			proj.ProjectId   =	jQuery("#InputProjectId").val();
			proj.artifacts = new Array();
			projectListObservable.projects.push(ko.mapping.fromJS(proj));
	});
	
	jQuery("#CatAdd").click(function() {
		var catItem = new Object();
			catItem.name = jQuery("#InputCatName").val();
			catItem.value   =	jQuery("#InputCatId").val();
			catItem.fields = new Array();
			projectListObservable.selectedItems.artifacts.push(ko.mapping.fromJS(catItem));	
	});
	
	jQuery("#FieldAdd").click(function() {
		var fieldItem = new Object();
			fieldItem.name = jQuery("#InputFieldName").val();
			fieldItem.value   =	jQuery("#InputFieldId").val();
			projectListObservable.selectedItems.fields.push(ko.mapping.fromJS(fieldItem));
	});
	
	ko.applyBindings(projectListObservable);
	jQuery( "#projectlistUL" ).selectable({
	selecting: function (event, ui) {
        jQuery(event.target).children('.ui-selecting').not(':first').removeClass('ui-selecting');
		 var currentProject = ko.contextFor(jQuery(ui.selecting).get(0));
		 if(currentProject != null)
			projectList.projectSelected("");
		projectListObservable.selectedItems.artifacts(currentProject.$data.artifacts())	
		projectList.catSelected("none");
    }
	});	
	jQuery( "#catlistUL" ).selectable({
	selecting: function (event, ui) {
        jQuery(event.target).children('.ui-selecting').not(':first').removeClass('ui-selecting');
		 var currentCat = ko.contextFor(jQuery(ui.selecting).get(0));
		 if(currentCat != null)
			projectList.catSelected("");
		 projectListObservable.selectedItems.fields(currentCat.$data.fields())
    }
	});	
	
	jQuery( "#custFieldListUL" ).selectable({
	selecting: function (event, ui) {
        jQuery(event.target).children('.ui-selecting').not(':first').removeClass('ui-selecting');
		 
		 
    }
	});	
	
	
	jQuery("#projectlistUL li").on( "click", function() {
	  alert("Test");
	  jQuery(this).addClass("selected").siblings().removeClass("selected");
	 
	});
	jQuery("#catlistUL li").click(function() {
	  jQuery(this).addClass("selected").siblings().removeClass("selected");
	  //var currentProject = ko.contextFor(jQuery(this).get(0));
	  //projectListObservable.selectedItems.artifacts(currentProject.$data.artifacts())
	});

	jQuery('.saveConfig').on('click', function() {
		jQuery.ajax({
			type: "POST",
			url:  "<?php echo $HOME ?>Plugins/ExportToTeamForge/pages/saveConfig.php",
			data: ko.mapping.toJSON(projectListObservable),
			success:function(result){
				
			}
		});  
	});
	
	//projectListObservable.projectSelected(false);
	//projectListObservable.catSelected(false);
	//projectListObservable.fieldSelected(false);
});
</script>
<style>
.ConfigBody{
height: 420px;
}
.ProjectListDiv {
	float:left;
	width: 32%;
	height: 400px;
}
.CatListDiv {
	float:left;
	width: 32%;
	height: 400px;
}
.FieldsDiv {
	float:left;
	width: 32%;
	height: 400px;
}


</style>
<br/>

<div class="ConfigBody container">
	<div class="ProjectListDiv col-md-4"><b>Project List:</b>
		<ul data-bind="foreach: projects" id="projectlistUL">
			<li class="ui-state-default">
				<span style="float:right" class="ui-icon-closethick glyphicon glyphicon-remove-circle"></span> <span data-bind="text: ProjectName, attr: { selectedID: ProjectId }"> </span>
			</li> 
		</ul>	
		<div class="field-actions">
			<input id="InputProjectName"  size="15" maxlength="25"  type="text"/ placeholder="Project Name"><input id="InputProjectId" type="text"/ size="8" maxlength="8" placeholder="Project Id">
			<button id="ProjectAdd" class="btn btn-default">Add</button>
		</div>
	</div>
	<div class="CatListDiv col-md-4"  data-bind="style: { display: projectSelected  }"><b>Cat List:</b>
		<ul data-bind="foreach: selectedItems.artifacts()" id="catlistUL">
			<li class="ui-state-default">
				<span style="float:right" class="ui-icon-closethick glyphicon glyphicon-remove-circle"></span> <span data-bind="text: name, attr: { selectedID: value }"> </span>
			</li>
		</ul>	
		<div class="field-actions">
			<input id="InputCatName"  size="10" maxlength="15"  type="text"/ placeholder="Cat Name"><input id="InputCatId" type="text"/ size="8" maxlength="8" placeholder="Cat Id">
			<button id="CatAdd" class="btn btn-default">Add</button>
		</div>
	</div>
	<div class="FieldsDiv col-md-4"   data-bind="style: { display: catSelected  }" ><b>Default Fields:</b></br>
		<label for="fieldname">Summary</label>
		<span name="fieldname" id="fieldname" value="">->Title</span><br>
		<label for="fieldDescription">Decription</label>
		<span name="fieldDescription" id="fieldDescription" value="">->Description</span>	<br>
		<label for="fieldSource">Source</label>
		<span name="fieldSource" id="fieldSource" value="">->Mantis</span>			
		</br></br>
		<label for="custFieldListUL"><b>Custom Fields:</b></label>
		<ul data-bind="foreach: selectedItems.fields" name="custFieldListUL" id="custFieldListUL">
			<li class="ui-state-default">
				<span style="float:right" class="ui-icon-closethick glyphicon glyphicon-remove-circle"></span> <span data-bind="text: name, attr: { selectedID: value }"> </span>
			</li>
		</ul>
		<div class="field-actions">
			<input id="InputFieldName" type="text"/ placeholder="Field Name"><input id="InputFieldId" type="text"/ size="8" maxlength="8" placeholder="Cat Id">
			<button id="FieldAdd" class="btn btn-default">Add</button>
		</div>
	</div>
</div>
<?php echo form_security_field( 'plugin_Example_config_update' ) ?>
	<center><button class="saveConfig">Save Config</button></center>
<?php
html_page_bottom();
?>