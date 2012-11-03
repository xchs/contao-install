<?php

	/**
	 * Contao Open Source CMS
	 *
	 * Contao Download Script
	 * Gets the current stable Contao release and runs the Contao install tool
	 *
	 * @package   Contao
	 * @link      http://git.io/contao-core
	 * @author    xchs <http://git.io/xchs>
	 * @copyright xchs 2012
	 */

	// Get the current stable release from SourceForge (http://sourceforge.net/projects/contao/files/latest/download)
	shell_exec("curl -s -L http://install.contao.org | tar -xzp");
	
	// Save the subfolder name of the unzip directory
  $folder = trim(shell_exec("ls -d contao-*"));

	// Remove some unwanted files and folders
	// shell_exec("rm -rf $folder/.gitignore $folder/README.md $folder/.tx");
	
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
	
?>