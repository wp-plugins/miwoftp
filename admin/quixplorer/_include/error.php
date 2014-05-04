<?php
/*
* @package		MiwoFTP
* @copyright	Copyright (C) 2009-2014 Miwisoft, LLC. All rights reserved.
* @license		GNU General Public License version 2 or later
*
*/

// no direct access
defined('ABSPATH') or die('MIWI');


//require_once MPATH_MIWOFTP_QX."/_include/header.php";

/**
    show error-message and terminate
 */
function show_error($error,$extra=NULL)
{
    // we do not know whether the language module was already loaded
    $errmsg = isset($GLOBALS["error_msg"]) ? $GLOBALS["error_msg"]["error"] : "ERROR";
    $backmsg = isset($GLOBALS["error_msg"]) ? $GLOBALS["error_msg"]["back"] : "BACK";

	show_header($errmsg);
    ?>
	<center>
        <h2><?php echo $errmsg ?></h2>
        <?php echo $error ?>
        <h3> <a href="javascript:window.history.back()"><?php echo $backmsg ?></a><h3>
        <?php if ($extra != NULL) echo " - " . $extra; ?>
    </center>
    <?php
    /*show_footer();*/ exit;
}
?>
