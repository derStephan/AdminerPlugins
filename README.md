# AdminerPlugins

Plugins for Adminer. Adminer is a great tool for day-to-day use of databases. 

https://www.adminer.org/

To include a plugin in adminer, you have to follow the steps here:
https://www.adminer.org/en/plugins/#use

And don't forget to add the plugin "plugin" because it is required to run any other plugin. RTFM!

## searchAutocomplete

Delivers Excel-like behaviour when filtering tables using the search-fields. 
After selecting a column for search using the drop-down, all values in this column are fetched using AJAX and can be selected directly in the search text-field.

When one or more columns are already set for search then all of those values will be respected when auto complete values are fetched from the database. This way, the search is refined with each selected column. 

Example: 
 - column `lastName` is selected with the value `Smith`
 - the user selects column `firstName`
 => only first names of all Smiths will be given for autocomplete 

Written in vanilla JavaScript, no jquery needed.

Tested with Adminer 4.7.5 in FireFox 70 and >5000 distinct values within one column without any performance degredation.

NOTE: Will fetch values with line feeds as well but those will be converted by the browser to single lines due to the features of the input field. So the search won't find the values in these cases. This is not this plugin's fault. 

![searchAutocomplete](https://user-images.githubusercontent.com/7764931/69607874-825a9e80-1026-11ea-9e82-1e0cfa347c42.gif)

## hideableColumns

This plugin helps working with tables with a lot of colums. Clicking the header of a column while holding the ALT-key hides the column. Columns are still hidden when using the form above the table (search or order) or flipping the page. 

You can define columns that should not be hideable when adding the plugin.
```
$plugins = array(
	new hideableColums(array('ID','userName')) //define columns that may not be hidden. 
);
```

Written in vanilla JavaScript, no jquery needed.

Tested with Adminer 4.7.5 in FireFox 70.

NOTE: This is not a security feature! The full table is loaded in any case, hiding is done in Javascript. 

![hideableColumns](https://user-images.githubusercontent.com/7764931/69607873-825a9e80-1026-11ea-9d36-39d2566c4a22.gif)

## stickyColumns

Helps you when working with very large tables. Allows you to keept important columns always in sight. 


2 ways to control this: 
1. You can define simple column names that will eighter stick to the left or to the right of the window. This approach will work if you have a lot of tables with the same structure. 

```
$plugins = array(
	new stickyColumns("ID","status") //in this example, column ID will stick to the left and column status will stick to the right. 
);
```

2. you can define sticky columns for each fully qualified table seperately:

```
$stickyColumnsLeft=array(	"information_schema.TABLES"=>"TABLE_NAME", //meaning: in database `information_schema` in table `TABLES` make column `TABLE_NAME` sticky to the left
				"mysql.user"=>"User");
$stickyColumnsRight=array(	"information_schema.TABLES"=>"CREATE_TIME",
				"mysql.proc"=>"created");	

$plugins = array(
	new stickyColumns($stickyColumnsLeft,$stickyColumnsRight) 
);
```

If you want to stick your header on top of the window, you can define that in the third parameter (bool).

Written in vanilla JavaScript, no jquery needed.

Tested with Adminer 4.7.5 in FireFox 70.

![stickyColumns](https://user-images.githubusercontent.com/7764931/69730775-f8dfc500-1128-11ea-8255-06da4a5d4a32.gif)


