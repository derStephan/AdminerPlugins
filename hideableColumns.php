<?php

/**
 * Make Columns in Data Table hideable
 * ===================================
 * This plugin helps working with tables with a lot of colums. Clicking the header of a column while holding the ALT-key hides the column. 
 * Columns are still hidden when using the form above the table (search or order) or flipping the page. 
 * 
 * You can define columns that should not be hideable when enabling the plugin.
 * $plugins = array(
 *		new hideableColumns(array('ID','userName')) //define columns that may not be hidden. 
 * );
 * 
 * Written in vanilla JavaScript, no jquery needed.
 * 
 * Tested with Adminer 4.8.1 in FireFox 104.
 * 
 * NOTE: This is not a security feature! The full table is loaded in any case, hiding is done in Javascript. 
 *
 * @author Stephan Herrmann, https://github.com/derStephan/AdminerPlugins
 * @license MIT
 *
 */
class hideableColumns 
{
	//will hold a list of all columns that will be hidden
	private $columnsToHide=array();

	//filter columns that may not be hidden.
	public function __construct ($unHideableColums=array())
	{
		if(isset($_GET["hide"]))
		{
			//filter URL for columns to hide.
			foreach($_GET["hide"] as $columnToHide)
			{
				if(!in_array($columnToHide, $unHideableColums))				
					$this->columnsToHide[]=$columnToHide;
			}
		}
	}
	
	public function head()
	{
		//all of this is only applicable in the simple table view
		if(!isset($_GET["select"]))
			return;
		
		?>

		<script <?php echo nonce()?> type='text/javascript'>
		
		//make columns hideable
		function makeColumnsHideable()
		{
			//register click-events on Links above the columns
			for (let span of document.querySelectorAll('#table thead th span' ) )
			{	
				span.addEventListener('click', headerSpanClick);
			}
			
			<?php
			//do actual hiding for each column seperately
			foreach($this->columnsToHide as $columnToHide)
			{	
			?>
				//hide content
				for (let cell of document.querySelectorAll('#table tbody td[id$="[<?php echo $columnToHide ?>]"' ) )
				{
					cell.style.display="none";
				}
				
				//hide header
				for (let span of document.querySelectorAll('#table thead th span' ) )
				{
					if(span.innerText=="<?php echo $columnToHide ?>")
					{
						span.parentElement.parentElement.style.display="none";		
					}
				}
				
				//add column to the form above the table so this will be kept while searching or ordering
				var newHideField=document.getElementById("form")[0].cloneNode();				
				newHideField.name="hide[]";
				newHideField.value="<?php echo $columnToHide ?>";
				document.getElementById("form").appendChild(newHideField);
				
			<?php
			}
			?>
			
			
			//hide content of rows that are appended later
			//whenever something is added to the table
			document.getElementById("table").addEventListener('DOMNodeInserted', function(e)
			{
				//walk through all new rows
				for (let rows of e.target.childNodes )
				{
					//walk through all cells in all new row
					for (let cell of rows.childNodes )
					{
						<?php
						//do actual hiding for each column seperately
						foreach($this->columnsToHide as $columnToHide)
						{	
						?>
							if(cell.id.indexOf("[<?php echo $columnToHide ?>]")>0)
								cell.style.display="none";
						<?php
						}
						?>
					}
				}
			});
		}
		document.addEventListener('DOMContentLoaded', makeColumnsHideable);
		
		//react to click on Heading while holding Alt-Key.
		function headerSpanClick(e)
		{
			if(e.altKey)
			{
				e.preventDefault();
				window.location.href = '?<?php echo $_SERVER["QUERY_STRING"]?>&hide[]='+event.target.innerText;
			}
		}
		</script>
		<?php
	}
}
