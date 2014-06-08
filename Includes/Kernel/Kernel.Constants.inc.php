<?php
	/**
	* Kernel.Constants.inc.php
	*/
	namespace Redundancy\Classes;
	/**
	 * This file contains a kind of enumerations for having global Errors, Messages etc.
	 * @license
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
	 * @author  squarerootfury <me@0fury.de>
	 **/
	class Errors{
		const ModuleMissing = 1;
		const MethodMissing = 2;
		const UserOrEmailAlreadyGiven = 3;
		const RoleNotFound = 4;
		const MultipleUserAccountsFound = 5;
		const DataBaseError = 6;
		const PasswordOrUserNameWrong = 7;
		const TokenGenerationFailed = 8;
	} 
?>
