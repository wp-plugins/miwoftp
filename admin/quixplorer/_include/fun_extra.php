<?php
/*
* @package		MiwoFTP
* @copyright	Copyright (C) 2009-2014 Miwisoft, LLC. All rights reserved.
* @license		GNU General Public License version 2 or later
*
*/

// no direct access
defined('ABSPATH') or die('MIWI');

//------------------------------------------------------------------------------
// THESE ARE NUMEROUS HELPER FUNCTIONS FOR THE OTHER INCLUDE FILES
//------------------------------------------------------------------------------
function make_link($_action,$_dir,$_item=NULL,$_order=NULL,$_srt=NULL,$_lang=NULL) {
	// make link to next page
	if($_action=="" || $_action==NULL) $_action="list";
	if($_dir=="") $_dir=NULL;
	if($_item=="") $_item=NULL;
	if($_order==NULL) $_order=$GLOBALS["order"];
	if($_srt==NULL) $_srt=$GLOBALS["srt"];
	if($_lang==NULL) $_lang=(isset($GLOBALS["lang"])?$GLOBALS["lang"]:NULL);
	
	$link=$GLOBALS["script_name"]."?page=miwoftp&option=com_miwoftp&action=".$_action;
	
	/*Detect if the site has SSL enabled and switch all links to https --- Added by Shane Gadsby <shane.gadsby@usq.edu.au> || https://github.com/schme16*/	
	if (force_ssl_admin()) {
		$link = str_replace("http://", "https://", $link, $temp = 1); 
	}
	
	if($_dir!=NULL) $link.="&dir=".urlencode($_dir);
	if($_item!=NULL) $link.="&item=".urlencode($_item);
	if($_order!=NULL) $link.="&order=".$_order;
	if($_srt!=NULL) $link.="&srt=".$_srt;
	if($_lang!=NULL) $link.="&lang=".$_lang;
	
	return $link;
}
//------------------------------------------------------------------------------
function get_abs_dir($dir) {			// get absolute path
	$abs_dir=$GLOBALS["home_dir"];
	if($dir!="") $abs_dir.="/".$dir;
	return $abs_dir;
}
//------------------------------------------------------------------------------
function get_abs_item($dir, $item) {		// get absolute file+path
	return get_abs_dir($dir)."/".$item;
}
//------------------------------------------------------------------------------
function get_rel_item($dir,$item) {		// get file relative from home
	if($dir!="") return $dir."/".$item;
	else return $item;
}
//------------------------------------------------------------------------------
function get_is_file($dir, $item) {		// can this file be edited?
	return @is_file(get_abs_item($dir,$item));
}
//------------------------------------------------------------------------------
function get_is_dir($dir, $item) {		// is this a directory?
	return @is_dir(get_abs_item($dir,$item));
}
//------------------------------------------------------------------------------
function parse_file_type($dir,$item) {		// parsed file type (d / l / -)
	$abs_item = get_abs_item($dir, $item);
	if(@is_dir($abs_item)) return "d";
	if(@is_link($abs_item)) return "l";
	return "-";
}
//------------------------------------------------------------------------------
function get_file_perms($dir,$item) {		// file permissions
	return @decoct(@fileperms(get_abs_item($dir,$item)) & 0777);
}
//------------------------------------------------------------------------------
function parse_file_perms($mode) {		// parsed file permisions
	if(strlen($mode)<3) return "---------";
	$parsed_mode="";
	for($i=0;$i<3;$i++) {
		// read
		if(($mode{$i} & 04)) $parsed_mode .= "r";
		else $parsed_mode .= "-";
		// write
		if(($mode{$i} & 02)) $parsed_mode .= "w";
		else $parsed_mode .= "-";
		// execute
		if(($mode{$i} & 01)) $parsed_mode .= "x";
		else $parsed_mode .= "-";
	}
	return $parsed_mode;
}
//------------------------------------------------------------------------------
function get_file_size($dir, $item) {		// file size
	return @filesize(get_abs_item($dir, $item));
}
//------------------------------------------------------------------------------
function parse_file_size($size) {		// parsed file size
	if($size >= 1073741824) {
		$size = round($size / 1073741824 * 100) / 100 . " GB";
	} elseif($size >= 1048576) {
		$size = round($size / 1048576 * 100) / 100 . " MB";
	} elseif($size >= 1024) {
		$size = round($size / 1024 * 100) / 100 . " KB";
	} else $size = $size . " Bytes";
	if($size==0) $size="-";

	return $size;
}
//------------------------------------------------------------------------------
function get_file_date($dir, $item) {		// file date
	return @filemtime(get_abs_item($dir, $item));
}
//------------------------------------------------------------------------------
function parse_file_date($date) {		// parsed file date
	return @date($GLOBALS["date_fmt"],$date);
}
//------------------------------------------------------------------------------
function get_is_image($dir, $item) {		// is this file an image?
	if(!get_is_file($dir, $item)) return false;
	return preg_match('/'.$GLOBALS["images_ext"].'/i', $item);
}
//-----------------------------------------------------------------------------
function get_is_editable($dir, $item) {		// is this file editable?
	if(!get_is_file($dir, $item)) return false;
	foreach($GLOBALS["editable_ext"] as $pat) if(preg_match('/'.$pat.'/i', $item)) return true;
	return false;
}
//-----------------------------------------------------------------------------
function get_is_unzipable($dir, $item) {		// is this file editable?
	if(!get_is_file($dir, $item)) return false;
	foreach($GLOBALS["unzipable_ext"] as $pat) if(preg_match('/'.$pat.'/i', $item)) return true;
	return false;
}
//-----------------------------------------------------------------------------
function get_mime_type($dir, $item, $query) {	// get file's mimetype
	if(get_is_dir($dir, $item)) {			// directory
		$mime_type	= $GLOBALS["super_mimes"]["dir"][0];
		$image		= $GLOBALS["super_mimes"]["dir"][1];
		
		if($query=="img") return $image;
		else return $mime_type;
	}
				// mime_type
	foreach($GLOBALS["used_mime_types"] as $mime) {
		list($desc,$img,$ext,$type)	= $mime;
		if(preg_match('/'.$ext.'/i', $item)) {
			$mime_type	= $desc;
			$image		= $img;
			if($query=="img"){ return $image;}
			else if($query=="ext"){ return $type;}
			else return $mime_type;
			
			
		}
	}
	
	if((function_exists("is_executable") &&
		@is_executable(get_abs_item($dir,$item))) ||
		preg_match('/'.$GLOBALS["super_mimes"]["exe"][2].'/i', $item))		
	{						// executable
		$mime_type	= $GLOBALS["super_mimes"]["exe"][0];
		$image		= $GLOBALS["super_mimes"]["exe"][1];
	} else {					// unknown file
		$mime_type	= $GLOBALS["super_mimes"]["file"][0];
		$image		= $GLOBALS["super_mimes"]["file"][1];
	}
	
	if($query=="img") return $image;
	else return $mime_type;
}
//------------------------------------------------------------------------------
function get_show_item($dir, $item) {		// show this file?
	if($item == "." || $item == ".." ||
		(substr($item,0,1)=="." && $GLOBALS["show_hidden"]==false)) return false;
		
	if($GLOBALS["no_access"]!="" && preg_match('/'.$GLOBALS["no_access"].'/i', $item)) return false;
	
	if($GLOBALS["show_hidden"]==false) {
		$dirs=explode("/",$dir);
		foreach($dirs as $i) if(substr($i,0,1)==".") return false;
	}
	
	return true;
}
//------------------------------------------------------------------------------
/*function copy_dir($source,$dest) {		// copy dir
	$ok = true;
	
	if(!@mkdir($dest,0777)) return false;
	if(($handle=@opendir($source))===false) show_error(basename($source).": ".$GLOBALS["error_msg"]["opendir"]);
	
	while(($file=readdir($handle))!==false) {
		if(($file==".." || $file==".")) continue;
		
		$new_source = $source."/".$file;
		$new_dest = $dest."/".$file;
		if(@is_dir($new_source)) {
			$ok=copy_dir($new_source,$new_dest);
		} else {
			$ok=@copy($new_source,$new_dest);
		}
	}
	closedir($handle);
	return $ok;
}*/
//------------------------------------------------------------------------------
function remove($item) {			// remove file / dir
	$ok = true;
	if(@is_link($item) || @is_file($item)) $ok=@unlink($item);
	elseif(@is_dir($item)) {
		if(($handle=@opendir($item))===false) show_error(basename($item).": ".$GLOBALS["error_msg"]["opendir"]);

		while(($file=readdir($handle))!==false) {
			if(($file==".." || $file==".")) continue;
			
			$new_item = $item."/".$file;
			if(!@file_exists($new_item)) show_error(basename($item).": ".$GLOBALS["error_msg"]["readdir"]);
			//if(!get_show_item($item, $new_item)) continue;
			
			if(@is_dir($new_item)) {
				$ok=remove($new_item);
			} else {
				$ok=@unlink($new_item);
			}
		}
		
		closedir($handle);
		$ok=@rmdir($item);
	}
	return $ok;
}
//------------------------------------------------------------------------------
function get_max_file_size() {			// get php max_upload_file_size
	$max = get_cfg_var("upload_max_filesize");
	if(preg_match("/G$/i",$max)) {
		$max = substr($max,0,-1);
		$max = round($max*1073741824);
	} elseif(preg_match("/M$/i",$max)) {
		$max = substr($max,0,-1);
		$max = round($max*1048576);
	} elseif(preg_match("/K$/i",$max)) {
		$max = substr($max,0,-1);
		$max = round($max*1024);
	}
	
	return $max;
}
//------------------------------------------------------------------------------
function down_home($abs_dir) {			// dir deeper than home?
	$real_home = @realpath($GLOBALS["home_dir"]);
	$real_dir = @realpath($abs_dir);
	
	if($real_home===false || $real_dir===false) {
		if(preg_match("/\\.\\./i",$abs_dir)) return false;
	} else if(strcmp($real_home,@substr($real_dir, 0, strlen($real_home)))) {
		return false;
	}
	
	return true;
}
//------------------------------------------------------------------------------
function id_browser() {
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$u_browser = '';
	
	if (preg_match('/MSIE/i', $u_agent)) {
		$u_browser = "IE";
	}
	elseif (preg_match('/Firefox/i', $u_agent)) {
		$u_browser = "MOZILLA";
	}
	elseif (preg_match('/Opera/i', $u_agent)) {
		$u_browser = "OPERA";
	}
	elseif (preg_match('/OmniWeb/i', $u_agent)) {
		$u_browser = "OMNIWEB";
	}
	elseif (preg_match('/Konqueror/i', $u_agent)) {
		$u_browser = "KONQUEROR";
	} else {
		$u_browser = 'OTHER';
	}
	
	return $u_browser;
}
//------------------------------------------------------------------------------
?>
