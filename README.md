# Export from Scuttle - Import to Shaarli

An export script for scuttle to HTML and a script to import the generated Bookmark HTML to Shaarli. 

For years I am using [Scuttle](https://github.com/scronide/scuttle), it is one of the tools I used on a daily basis. 
However, while using it steadily produced a [big error log](https://github.com/scronide/scuttle/issues/11) of several MB. Since it seems not active anymore and errors not fixed, there was the need to use another free bookmarking software. 

Recently I have found the script [Shaarli](https://github.com/sebsauvage/Shaarli) and am very happy about it. It is as fast as scuttle, does not need a database (stores entries in file /data/datastore.php) and does not produce any errors! 

By the way, I do not want to use a public bookmarking service because they would possess all my private notes within the bookmarks. Furthermore, none of the services I found could import bookmarks from scuttle. 

## The missing Feature: Export & Import

It is very helpful that Shaarli can [import HTML export from SemanticScuttle](https://github.com/shaarli/Shaarli/wiki/Backup%2C-restore%2C-import-and-export#import-links-from), in other words: SemanticScuttle has an HTML exporter. On the other hand, the older **Scuttle has no HTML export**. 

That's why I combined the scripts available to create an exporter and importer. 

## Export from Scuttle (Bookmarks to HTML)

1. Edit the file `scuttle_export_html.php` in the `scuttle` folder and enter your database credentials 
2. Upload the file `scuttle_export_html.php` in your `scuttle` folder on your server
3. Open the path to the file in your browser and receive the HTML output
4. Download the HTML file. 
   **Important:** Go to "View source code" in your browser, then copy and paste the original output. When saving the HTML directly, most browsers modify the output and the import will not work. 
   
## Import the HTML bookmarks into Shaarli 

1. Make a backup of your files, you never know.
2. Upload the files `shaarli/index.php`, `shaarli/NetscapeBookmarkParser.php` and `shaarli/NetscapeBookmarkUtils.php` to your server. This will override the existing ones (make a backup, then you can bring them back after the import)
3. Go to your shaarli installation and click on `Tools` and then `Import: Import Netscape html bookmarks (as exported from Firefox, Chrome, Opera, delicious...)`. Or just open the URL `shaarli/?do=import`
4. Select your HTML file. Modify import settings as you wish. Hit the `Import` button.
5. You should see a success message. Done.

I really hope this helps more people like me keeping their own bookmark service alive!


## Tips

Shaarli also has a [Chrome extension](https://chrome.google.com/webstore/detail/shiny-shaarli/hajdfkmbdmadjmmpkkbbcnllepomekin?hl=en). 
