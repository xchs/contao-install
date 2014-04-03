#!/usr/bin/env php
<?php

/**
 * Contao Open Source CMS
 *
 * Contao Download Script
 * Gets the current stable Contao release and runs the Contao install tool
 * Gets a certain GitHub repository branch if specified as (URL) parameter
 *
 * @package   Contao
 * @link      http://git.io/contao-core
 * @author    xchs <http://git.io/xchs>
 * @copyright xchs 2014
 */


// Check for a given URL parameter to switch between GitHub repository branches
if (isset($argv[1]) || (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != ''))
{
	// Check for the first CLI argument
	if (isset($argv[1]))
	{
		$strBranch = $argv[1];
	}
	else
	{
		// Sanatize for security, remove anything but 0-9,a-z,-_,
		$strBranch = preg_replace("/[^0-9a-z\-_,.\/]+/i", "", $_SERVER['QUERY_STRING']);
	}

	// Get the repository branch from GitHub
	shell_exec("curl -s -L https://github.com/contao/core/tarball/" . $strBranch . " > download.tar.gz");
}
else
{
	// Get the current stable release from GitHub
	shell_exec("curl -s -L http://download.contao.org > download.tar.gz");
}


// Check if the download file exists and if it is greater than 1 MB
if (file_exists('download.tar.gz') && filesize('download.tar.gz') > 1048576)
{
	// Extract the download archive
	shell_exec("tar -xzpf download.tar.gz");

	// Save the subfolder name of the unzip directory
	$folder = trim(shell_exec("ls -d *core-*"));

  // Check if there has been saved the unzip folder name
  if (!empty($folder))
  {
    // Remove some unwanted files and folders
    // shell_exec("rm -rf $folder/.gitattributes $folder/.gitignore $folder/README.md $folder/.tx");

    // Move subfolder contents into current directory
    shell_exec("rsync -a $folder/* ./");

    // Make sure to move also hidden files and folders
    shell_exec("rsync -a $folder/.[a-z]* ./");

    // Enable the ".htaccess" file
    shell_exec("cp .htaccess.default .htaccess");

    // Remove the unzip directory
    shell_exec("rm -rf $folder");

    // Remove the download archive
    shell_exec("rm download.tar.gz");

    // Remove the download script
    shell_exec("rm contao-install.php");

    // Redirect to the Contao install tool
    Header("Location: contao/install.php");
  }
  else
  {
    die("\n   WARNING: Unzip directory not found or wrong folder name!\n\n");
  }
}
else
{
  if (file_exists('download.tar.gz'))
  {
    // Remove the download archive
    shell_exec("rm download.tar.gz");
  }
    
  die("\n   WARNING: Download file not found or uncomplete. Check for the right GitHub repository branch name!\n\n");
}