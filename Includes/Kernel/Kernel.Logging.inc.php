<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * Any logging methods are located here.
	 */
	/**
	 * log_event logs an event
	 * @param $event_name the name of the event
	 * @param $method the name of the method
	 * @param $content what happened?
	 */
	function log_event($event_name,$method,$content)
	{
		if ($GLOBALS["config"]["Program_Enable_Logging"] == 1){
			$file = fopen($GLOBALS["Program_Dir"]."System.log","a+");		
			$date= date("D M j G:i:s T Y", time());
			fwrite($file, "[$date] function $method() - $event_name: $content\n");
			fclose($file);
		}
	}
?>