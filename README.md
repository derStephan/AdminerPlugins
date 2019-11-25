# AdminerPlugins

Plugins for Adminer. Adminer is a great tool for day-to-day use of databases. 

https://www.adminer.org/

To include a plugin in adminer, you have to follow the steps here:
https://www.adminer.org/de/plugins/#use

And don't forget to add the plugin "plugin" because it is required to run any other plugin. RTFM!

## searchAutocomplete

Delivers Excel-like behaviour when filtering tables using the search-fields. 
After selecting a column for search using the drop-down, all values in this column are fetched using AJAX and can be selected directly in the search text-field.

Written in vanilla JavaScript, no jquery needed.

Tested with Adminer 4.7.5 in FireFox 70 and >5000 distinct values within one column without any performance degredation.

NOTE: Will fetch values with line feeds as well but those will be converted by the browser to single lines due to the features of the input field. So the search won't find the values in these cases. This is not this plugin's fault. 

![grafik](https://user-images.githubusercontent.com/7764931/69531722-a52c7a80-0f74-11ea-82a8-4a35f58940ec.png)
