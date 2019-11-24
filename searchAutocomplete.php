<?php
/**
 * Autocompletion for search plugin
 * ================================
 * Delivers Excel-like behaviour when filtering tables using the search-fields. 
 * After selecting a column for search using the drop-down, all values in this column are fetched using AJAX and can be selected directly in the search text-field.
 * 
 * Written in vanilla JavaScript, no jquery needed.
 * 
 * Tested with Adminer 4.7.5 in FireFox 70 and >5000 distinct values within one column without any performance degredation.
 * 
 * NOTE: Will fetch values with line feeds as well but those will be converted by the browser to single lines due to the features of the input field. So the search won't find the values in these cases. This is not this plugin's fault.
 *
 * @author Stephan Herrmann, https://github.com/derStephan/AdminerPlugins
 * @license MIT
 *
 */
class searchAutocomplete 
{
	//react to ajax-requests with json
	public function headers() 
	{
		if(isset($_POST["getAutoCompleteData"]))
		{
			//make safe against all kinds of threats
			$column=preg_replace("/[^a-zA-Z0-9_-]/", "", $_POST["getAutoCompleteData"]);
			$table=preg_replace("/[^a-zA-Z0-9_-]/", "", $_GET["select"]);
			//to order even text-columns naturally, us this ugly hack
			$orderSQLforNaturalSort="`$column`+0<>0 DESC, `$column`+0, `$column`";		
			echo json_encode(get_vals("SELECT DISTINCT `$column` FROM `$table` ORDER BY $orderSQLforNaturalSort"));
			//stop delivering anything...
			die();
		}
			
	}
	
	public function head()
	{
		//all of this is only applicable in the simple table view
		if(!isset($_GET["select"]))
			return;
	?>
		<script <?php echo nonce()?> type='text/javascript'>
		//prepare autocomplete searchFields on page load
		document.addEventListener('DOMContentLoaded', function()
		{
			//get all search dropDowns
			var searchFieldDropDowns=document.querySelectorAll('#fieldset-search select[name$="[col]"]');
			
			//none there, just skip it
			if(searchFieldDropDowns.length==0)
				return;
			
			for (let searchFieldDropDown of searchFieldDropDowns )
			{	
				//bind event to each dropDown 
				searchFieldDropDown.addEventListener('ValueChange', populateAutocompleteDataList);
				//get all values for each drop down right away to have the same user experience as after change 
				populateAutocompleteDataList(searchFieldDropDown);
			}
			
			//disable browser-integrated autocomplete, only use the existing values from the database
			var searchFields=document.querySelectorAll('#fieldset-search input[name$="[val]"]');
			for (let searchField of searchFields )
			{
				searchField.setAttribute("autocomplete", "off");				
			}
			
			//whenever a search is started, a new empty search row is added. 
			//this will register changeEvents for any new search row
			document.getElementById("fieldset-search").addEventListener('DOMNodeInserted', function(e)
			{
				if(e.target.childNodes[0])
					e.target.childNodes[0].addEventListener('ValueChange', populateAutocompleteDataList);
			});
		});
		

		function populateAutocompleteDataList(e)
		{
			//this function is invoked in 2 cases
			//	1. on page load for each drop down field within search area
			var wasDoneDuringPageLoad=true;
			//		in this case the parameter e is an element
			var element=e;
			//the other way to invoke this functiion:
			//	2. on change of any change within drop down fields within search area
			//		in this case the parameter e is an event
			if (e.target)
			{
				element=e.target;
				wasDoneDuringPageLoad=false;
			}
			
			//stop right here, if there was no column selected
			if(e.value=="")
				return;
			
			//check if there is a datalist for the respective column
			//if not, create it and get all values from within the column
			//data will only be fetched once per column, even if the same column is selected multiple times
			if(!document.getElementById("autocompleteSource"+element.value))
			{	
				//add datalist to searchfieldset. 
				var dataList = document.createElement("datalist");
				dataList.setAttribute("id", "autocompleteSource"+element.value);
				document.getElementById("fieldset-search").appendChild(dataList);

				//get all values in the column using ajax
				var autoCompleteXHR = new XMLHttpRequest();
				autoCompleteXHR.open("POST", "", true);
				autoCompleteXHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				//submit the column to get the values for
				autoCompleteXHR.send("getAutoCompleteData="+element.value);
				autoCompleteXHR.onreadystatechange = function() 
				{
					if (this.readyState == 4 && this.status == 200) 
					{
						 // We're getting a json response so we convert it to an object
						var response = JSON.parse( this.responseText ); 

						// clear any previously loaded options in the datalist
						dataList.innerHTML = "";

						response.forEach(function(item) 
						{
							// Create a new <option> element.
							var option = document.createElement('option');
							option.value = item;

							// attach the option to the datalist element
							dataList.appendChild(option);
						});
					}
				}
			}
			
			//get name of input field that corresponds to the drop-down
			var searchFieldName=element.name.split(']')[0]+'][val]';			
			var searchField=document.getElementsByName(searchFieldName)[0];		
			
			//assign new dataList to the input field.
			searchField.setAttribute("list", "autocompleteSource"+element.value);

			//on change, put cursor into the text field. 
			//this should open the autocomplete list immediately
			if(!wasDoneDuringPageLoad)
				searchField.focus();					
		}
		
		</script>
	<?php
	
	}


}