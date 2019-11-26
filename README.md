# AdminerPlugins

Plugins for Adminer. Adminer is a great tool for day-to-day use of databases. 

https://www.adminer.org/

To include a plugin in adminer, you have to follow the steps here:
https://www.adminer.org/en/plugins/#use

And don't forget to add the plugin "plugin" because it is required to run any other plugin. RTFM!

## searchAutocomplete

Delivers Excel-like behaviour when filtering tables using the search-fields. 
After selecting a column for search using the drop-down, all values in this column are fetched using AJAX and can be selected directly in the search text-field.

Written in vanilla JavaScript, no jquery needed.

Tested with Adminer 4.7.5 in FireFox 70 and >5000 distinct values within one column without any performance degredation.

NOTE: Will fetch values with line feeds as well but those will be converted by the browser to single lines due to the features of the input field. So the search won't find the values in these cases. This is not this plugin's fault. 

![searchAutocomplete](https://user-images.githubusercontent.com/7764931/69607874-825a9e80-1026-11ea-9e82-1e0cfa347c42.gif)





## hideableColumns

This plugin helps working with tables with a lot of colums. Clicking the header of a column while holding the ALT-key hides the column. Hiding is kept when flipping the page. 

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
