#!/usr/bin/env php
<?php

/**
 * Contao Open Source CMS
 *
 * Contao Download Script
 * Gets the current stable Contao release and runs the Contao install tool
 * Gets a certain GitHub repository branch if specified as URL parameter
 *
 * @package   Contao
 * @link      http://git.io/contao-core
 * @author    xchs <http://git.io/xchs>
 * @copyright xchs 2012
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

	// Get the respective repository branch from GitHub
	shell_exec("curl -s -L https://github.com/contao/core/tarball/" . $strBranch . " | tar -xzp");

	// Save the subfolder name of the unzip directory
	$folder = trim(shell_exec("ls -d contao-core-*"));
}
else
{
	// Get the current stable release from SourceForge (http://sourceforge.net/projects/contao/files/latest/download) and extract the tar archive (tarball)
	shell_exec("curl -s -L http://install.contao.org | tar -xzp");

	// Save the subfolder name of the unzip directory
	$folder = trim(shell_exec("ls -d contao-[0-9]*"));
}


// Remove some unwanted files and folders
// shell_exec("rm -rf $folder/.gitattributes $folder/.gitignore $folder/README.md $folder/.tx");

// Move subfolder contents into current directory
shell_exec("mv $folder/* ./");

// Make sure to move also hidden files and folders
shell_exec("mv $folder/.[a-z]* ./");

// Enable the ".htaccess" file
shell_exec("cp .htaccess.default .htaccess");

// Remove the unzip directory
shell_exec("rm -rf $folder");

// Remove the download script
shell_exec("rm contao-install.php");

// Redirect to the Contao install tool
Header("Location: contao/install.php");
