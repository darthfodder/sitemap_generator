<?php

/* XML Sitemap Generator 
 * Notes: The ideal would be to have this generate an XML sitemap every 24 hours via a cron job
 * 		   However, not all servers are configured to allow for this, so the map must be generated on the fly
 *
 */

// The Base URI for all URLs
$baseURI = "http://www.marquette.edu/library";

// Create the head information of the file
printf("<?xml version='1.0' encoding='UTF-8'?> \n");
printf("<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'> \n" );
// List of folders and files to block from the site map
$folders = array("_assets", "_css", "_js");
// List of approved file types
$exts = array("html", "shtml", "htm", "php");
// Grab all directory information
$di = new RecursiveDirectoryIterator(dirname(__FILE__));
// Loop throught all driectories and sub-directories
foreach (new RecursiveIteratorIterator($di) as $fileName => $file) {
	// Set bool vars
	$isInFolder = false;
	$isFileType = false;
	// Loop though the array of exclusions
	foreach($folders as $folder){
		// Check if the file is within the exclusions folder
		$checkFolder = stripos($fileName, $folder);
		// If yes (will be an int) then set the var to true and stop the loop
		if($checkFolder != false){
			$isInFolder = true;
			break;
		}
	}
	// Get the extention of the file
	$fileType = explode("/", $fileName);
	$fileType = end($fileType);
	$fileType = explode(".", $fileType);
	// The array should only be 2 (file and extention)
	// If it is larger, most likely this is ._filename.ext file that shouldn't be in the index
	if (count($fileType) == 2){
		// get the last element of the array
		$fileTyle = end($fileType);
		// Loop though array of approved file types
		foreach($exts as $ext){
			// Check if extention is an approved file type
			// Using a case insensitive comparison method
			if (strcasecmp($ext, $fileTyle) == 0){
				$isFileType = true;
				break;
			}
		}
	}
	
	// If the file is in an indexable folder and is a supported file type
	// then add them to the doc
	if ($isInFolder == false && $isFileType == true){
		// Get last modified date
		$modTime = filemtime($fileName);
		$modTime = date("Y-m-d", $modTime);
		// Remove server information from the file name
		$fileName = str_replace("/muweb/data/http/library/web", "", $fileName);
    	printf("<url> \n <loc>" . $baseURI . $fileName . "</loc> \n <lastmod>" . $modTime . "</lastmod> \n </url> \n");
	}
}
printf('</urlset>');
?>