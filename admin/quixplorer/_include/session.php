<?php
/*
* @package		MiwoFTP
* @copyright	Copyright (C) 2009-2014 Miwisoft, LLC. All rights reserved.
* @license		GNU General Public License version 2 or later
*
*/

// no direct access
defined('ABSPATH') or die('MIWI');

/**
	This function allows access to session variables
*/
function session_get ($name)
{
	$user = $GLOBALS['__SESSION']["s_user"];
	if (!isset($GLOBALS['__SESSION']))
		return;

	if (!isset($GLOBALS['__SESSION'][$name]))
		return;
	
	return $GLOBALS['__SESSION'][$name];
}

?>
