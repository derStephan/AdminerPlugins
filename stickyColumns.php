<?php

/**
 * Make columns and header sticky
 * ==============================
 * Helps you when working with very large tables. Allows you to keept important columns always in sight. 
 *
 * 
 * 2 ways to control this: 
 * 1. You can define simple column names that will eighter stick to the left or to the right of the window. This approach will work if you have a lot of tables with the same structure. 
 *
 * 		$plugins = array(
 *			new stickyColumns("ID","status") //in this example, column ID will stick to the left and column status will stick to the right. 
 * 		);
 * 
 * 2. you can define sticky columns for each fully qualified table seperately:
 *
 *		$stickyColumnsLeft=array(	"information_schema.TABLES"=>"TABLE_NAME",
 *									"mysql.user"=>"User");
 *		$stickyColumnsRight=array(	"information_schema.TABLES"=>"CREATE_TIME",
 *									"mysql.proc"=>"created");	
 * 
 * 		$plugins = array(
 *			new stickyColumns(stickyColumnsLeft,stickyColumnsRight) 
 * 		);
 *
 * If you want to stick your header on top of the window, you can define that in the third parameter (bool).
 *
 * Written in vanilla JavaScript, no jquery needed.
 * 
 * Tested with Adminer 4.7.5 in FireFox 70 and >5000 distinct values within one column without any performance degredation.
 *
 * @author Stephan Herrmann, https://github.com/derStephan/AdminerPlugins
 * @license MIT
 *
 */
class stickyColumns 
{
	private $stickyColumnLeft;
	private $stickyColumnRight;
	private $stickyHeader;
	
	/**
	 * 
	 * @param mixed $stickyColumnLeft if not empty, make this column sticky to the left border of the window. Give an array here to set this for multiple tables. 
	 * @param mixed $stickyColumnRight if not empty, make this column sticky to the right. Give an array here to set this for multiple tables. 
	 * @param bool $stickyHeader if true, make header sticky as well
	 */
	public function __construct ($stickyColumnLeft="",$stickyColumnRight="",$stickyHeader=true)
	{
		$fullyQualifiedTableName=$_GET["db"].".".$_GET["select"];
		if(is_array($stickyColumnLeft))
			$this->stickyColumnLeft=@$stickyColumnLeft[$fullyQualifiedTableName];
		else
			$this->stickyColumnLeft=$stickyColumnLeft;
		
		if(is_array($stickyColumnRight))
			$this->stickyColumnRight=@$stickyColumnRight[$fullyQualifiedTableName];
		else
			$this->stickyColumnRight=$stickyColumnRight;
		
		$this->stickyHeader=$stickyHeader;
	}
	
	
	public function head()
	{
		//all of this is only applicable in the simple table view
		if(!isset($_GET["select"]))
			return;
		
		echo "<style>";
		echo "#table {border-collapse: inherit;}";
		echo ".footer {z-index:4;}";
		echo ".js .checkable .checked td,.js .checkable .checked th {white-space: pre-wrap !important;overflow: unset;}";
		//make left column sticky. Works only for data rows, not for headers
		if($this->stickyColumnLeft!="")
			echo "#table tbody td[id$='[{$this->stickyColumnLeft}]']{position:sticky;left:0px;z-index:2;border-right: 1px solid;} ";
		//make right column sticky. Works only for data rows, not for headers
		if($this->stickyColumnRight!="")
			echo "#table tbody td[id$='[{$this->stickyColumnRight}]']{position:sticky;right:0px;z-index:2;border-left: 1px solid;} ";
		//make header sticky. 
		if($this->stickyHeader)	
			echo "#table thead {overflow: visible; position:sticky;top: 0;z-index:3;}";	
		echo "</style>";
		
		?>

		<script <?php echo nonce()?> type='text/javascript'>
		//make column headers of table sticky on the left and right.
		//Due to lack of any ID in thead, this has to be done in JS
		function makeColumnHeadersSticky()
		{
			if(!document.getElementById('table'))
				return;
			
			//walk through all cells in table header
			for (let header of document.getElementById('table').tHead.childNodes[0].cells )
			{
				//check if column name matches the configured column
				if(header.childNodes[1].childNodes[0].innerHTML=='<?php echo $this->stickyColumnLeft ?>')
				{
					//set the styles
					header.style.position="sticky";
					header.style.left="0px";
					header.style.zIndex="4";
				}

				if(header.childNodes[1].childNodes[0].innerHTML=='<?php echo $this->stickyColumnRight?>')
				{
					header.style.position="sticky";
					header.style.right="0px";
					header.style.zIndex="4";
				}
			};
			

			//if a cell does not have any background, it will be shown as transparent
			//to prevent this, set the background-color of the body for all transparent cells.
			//this should work for all themes that do not have a background-image. 
			var bodyBackground=getComputedStyle(document.body).backgroundColor;
			for (let cell  of document.querySelectorAll("#table tbody td" ) )
			{
				if(getComputedStyle(cell).backgroundColor=="rgba(0, 0, 0, 0)")
					cell.style.backgroundColor=bodyBackground;
			}
		}
		document.addEventListener('DOMContentLoaded', makeColumnHeadersSticky);
		</script>
		<?php 
	}
}
