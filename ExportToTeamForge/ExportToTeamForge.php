<?php

require_once( config_get( 'class_path' ) . 'MantisFormattingPlugin.class.php' );

class ExportToTeamForgePlugin extends MantisPlugin {
    function register() {
        $this->name = 'ExportToTeamForge';    # Proper name of plugin
        $this->description = 'This Component will allow the export to team forge button to show. This also includes jquery, jqueryui, and knockoutjs';    # Short description of the plugin
        $this->page = 'config';           # Default plugin page

        $this->version = '1.0';     # Plugin version string
        $this->requires = array(    # Plugin dependencies, array of basename => version pairs
            'MantisCore' => '1.2.0',  #   Should always depend on an appropriate version of MantisBT
            );

        $this->author = 'George Baldwin & Phillip Lackey';         # Author/team name
        $this->contact = 'BaldwinF.com';        # Author/team e-mail address
        $this->url = '';            # Support webpage
    }
	
	 /**
	 * Default plugin configuration.
	 */
	function config() {
		return array(
			'process_text'		=> ON,
			'process_urls'		=> ON,
			'process_buglinks'	=> ON,
			'process_vcslinks'	=> ON,
		);
	}
	
	
	function hooks() {
		return array(
			'EVENT_LAYOUT_RESOURCES' => 'resources',
			'EVENT_VIEW_BUG_DETAILS' => 'ViewBugDetails'
		);
	}

	/**
	 * Create the resource link to load the jQuery library.
	 */
	function resources( $p_event ) {
		$ReturnValue = '<script type="text/javascript" src="'.plugin_file( 'js/jquery-min.js' ).'"></script>
						<script type="text/javascript" src="'.plugin_file( 'js/jquery-ui-min.js' ).'"></script>
						<script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
						<link rel="stylesheet" href="'.plugin_file( 'jquery-ui.css' ).'" />
						<script type="text/javascript">jQuery.noConflict();</script>
						<script type="text/javascript" src="'.plugin_file( 'js/knockout-3.js' ).'"></script>
						<script type="text/javascript" src="'.plugin_file( 'js/knockoutmapping.js' ).'"></script>
						<link rel="stylesheet" href="'.plugin_file( 'sass/custom.css' ).'" />
						<link rel="stylesheet" href="'.plugin_file( 'sass/custom.css' ).'" />';
		return $ReturnValue;
	}
	function ViewBugDetails($p_event, $bugId){
		echo '<button class="AddToTeamForge">Add To Team Forge</button>';
		
			require_once( 'bug_api.php' );
			$tpl_bug = bug_get( $bugId, true );
			$description =  string_display_links( $tpl_bug->description ) ;
			$summary =  bug_format_summary( $bugId, SUMMARY_FIELD );
			//print_r($tpl_bug);
		

		echo '  <style> 
			.customFieldListDiv {
				float: right;
				position: absolute;
				top: 20px;
				right: 40px;
			}
			</style>
		';
		echo ' <script>';
		require('pages/getconfigobject.php'); // This gets the JSON Object that holds configuration
		
		echo 'var projectListObservable ; 
			jQuery(document).ready(function(){
				projectListObservable = ko.mapping.fromJS(projectList);
				jQuery( ".AddToTeamForge" ).click(function(){
					 jQuery( "#dialog-form" ).dialog( "open" ) ;
				});
				
				 jQuery( "#dialog-form" ).dialog({
				   buttons: [{ 
							  text: "Export Defect", 
							  click: function() { 
								ExportThisDefect();
							  }
						   },{ 
							  text: "Cancel", 
							  click: function() { 
								  jQuery( this ).dialog( "close" ); 
							  }
						   }
				   ], 
				  width: 700,
				  height: 600,
				  autoOpen: true, // Change this to false when done testing.
				  modal: true
				});
				
				for (var i=0;i<projectList.projects.length;i++)
				{ 
					var option = document.createElement("option");
					option.text = projectList.projects[i].ProjectName;
					option.value = projectList.projects[i].ProjectId;
					var select = document.getElementById("selectedProjectId");
					select.appendChild(option);
				}
				
				jQuery("#selectedCat").on("change", function (e) {
					var optionSelected = jQuery("option:selected", this);
					var valueSelected = this.value;
					var fieldArray = jQuery.grep(projectList.selectedItems.artifacts[0].artifacts, function(e){ return e.value == valueSelected; });
					projectListObservable.selectedItems.fields(fieldArray[0].fields);
					projectListObservable.catSelected("block")
				});
				
				jQuery("#selectedProjectId").on("change", function (e) {
					var optionSelected = jQuery("option:selected", this);
					var valueSelected = this.value;
					
					var artifactArray = jQuery.grep(projectList.projects, function(e){ return e.ProjectId == valueSelected; });
					projectList.selectedItems.artifacts = artifactArray;
					if(artifactArray.length == 1)
					{
						jQuery("#selectedCat").empty().append("<option>Select Category</option>");
						for (var i=0;i<artifactArray[0].artifacts.length;i++)
						{ 
							var option = document.createElement("option");
							option.text = artifactArray[0].artifacts[i].name;
							option.value = artifactArray[0].artifacts[i].value;
							var select = document.getElementById("selectedCat");
							select.appendChild(option);
						}
					}
					else
						jQuery("#selectedCat").empty().append("<option>Select Category</option>");
					projectListObservable.catSelected("block");
				});
				ko.applyBindings(projectListObservable);
			});
			// On select of project.. we get artiface list by doing this: 
			function ExportThisDefect(){
				// here is the AJAX request that will call to our apecial API
				//ajax to :
				var projectedIdSelected = jQuery(".selectedProjectId").val();
				var selectedPriority = jQuery(".prioritySet").val();
				var component = new Object();
				component.name = "George";
				jQuery.ajax({
					type: "POST",
					url: "'. plugin_page( "CreateDefect" ).'&id='.$bugId.'&trackerId=" + projectedIdSelected  + "&priority=" + selectedPriority,
					data: JSON.stringify(component),
					success:function(result){
						  jQuery("#dialog-form").dialog("close") ;
					}
				});   
			}
			
			</script>
			<div id="dialog-form" title="Export Bug To Teamforge">
			  <form>
			  <fieldset>
				<label for="name">Bug Name:</label></br>
				<span name = "bugsummary">'.$summary.'</span>
				</br>
				</br>
				<label for="name">Select Project:</label></br>
				<select id = "selectedProjectId" class="selectedProjectId"><option>Select Project</option> </select>
				</br>
				</br>
				<label for="selectedCat">Select Category:</label></br>
				<select id = "selectedCat" class="selectedCat"><option>Select Category</option> </select>
				</br>
				</br>
				<label for="email">Append Note To Description:</label>
				</br>
				<textarea type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all"></textarea>
				</br>
				</br>
				<label for="priority">Priority:</label></br>
				<select class="prioritySet">
					<option>Select Priority</option> 
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
				</select>
				<div class="customFieldListDiv" data-bind="style: { display: catSelected  }" >
					<b>Fields to be sent over for selected Category:</b>
					<ul data-bind="foreach: selectedItems.fields" name="custFieldListUL" id="custFieldListUL">
						<li class="ui-state-default">
							<span style="float:right" class="ui-icon-closethick glyphicon glyphicon-remove-circle"></span> <span data-bind="text: name, attr: { selectedID: value }"> </span>
						</li>
					</ul>
				</div>
				<div class="customFieldListDiv" data-bind="if: catSelected === "block" >
					<b>Please select a project & category<b/>
				</div>
			  </fieldset>
			  </form>
			</div>
			';
	}
	
}

?>