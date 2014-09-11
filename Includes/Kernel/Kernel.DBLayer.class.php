<?php	
	/**
	* Kernel.DBLayer.class.php
	*/	
	namespace Redundancy\Kernel;		
	use Doctrine\Common\ClassLoader;
	use Doctrine\DBAL;
	use Doctrine\Common;
	/**
	 * This file contains the database abstraction layer, the framework Doctrine is used.
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
	class DBLayer{
		/**
		* The current class instance (singleton)
		*/
		private static $instance;
		/**
		* The currently open database connection.
		*/
		private $connection;
		/**
		* private constructor. Nothing to tell
		*/
		private function __construct(){}
		/**
		* Returns the current instance of the database layer.	
		* @return the current database layer instance
		*/
		public static function GetInstance(){
			if (!isset(self::$instance)){
				self::$instance = new DBLayer();
				self::$instance->Setup();
			}
			return self::$instance;
		}
		/**
		* Sets up the database environment
		* @todo Move the settings in own file!
		* @todo several config settings!
		*/
		private function Setup(){
			try{
				$classLoader = new ClassLoader('Doctrine\DBAL', __REDUNDANCY_ROOT__."Lib/Doctrine");
				$commonLoader = new ClassLoader('Doctrine\Common', __REDUNDANCY_ROOT__."Lib/Doctrine");
				$classLoader->register();
				$commonLoader->register();
				$config = new \Doctrine\DBAL\Configuration();
				//TODO: Make this settings variable
				$connectionParams = array(
				    'dbname' => 'Lenticularis',
				    'user' => 'root',
				    'password' => '',
				    'host' => 'localhost',
				    'driver' => 'pdo_mysql',
				);
				$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
				$this->connection = $conn;		
			}catch(PDOException $e){
				echo "upps";
				die(Errors::DataBaseError);	
			}				
		}
		/**
		* Return the current connection 
		* @return \Doctrine\DBAL\Connection the current connection		
		*/
		public function GetConnection(){
			return $this->connection;
		}
		/**
		* Runs an select query on the database
		* @param $sql the sql query to run
		* @return array|null containing the results
		*/
		public function RunSelect($sql){
			$result = null;
			try{
				if (!isset($this->connection)){
					$this->Setup();
				}	
				$stmt = $this->GetConnection()->query($sql);				
				while ($row = $stmt->fetch()) {			    
				    $result[] = $row;
				}
				return $result;
			}catch(Exception $e){
				return null;
			}			
		}
		/**
		* Update data in the database
		* @param $sql the sql query to run
		* @return integer the amount of affected rows
		*/
		public function RunUpdate($sql){
			try{
				if (!isset($this->connection)){
					$this->Setup();
				}	
				$count =  $this->GetConnection()->executeUpdate($sql);
				return $count;
			}catch(Exception $e){
				return null;
			}
		}
		/**
		* Delete data from the database
		* @param $sql the sql query to run
		* @return \Doctrine\DBAL\Query an data object containing the query
		*/
		public function RunDelete($sql){
			try{
				if (!isset($this->connection)){
					$this->Setup();
				}
				$result =  $this->GetConnection()->query($sql);
				return $result;
			}catch(Exception $e){
				return null;
			}
		}
		/**
		* Insert data to db
		* @param $sql the sql query to run
		* @return \Doctrine\DBAL\Query an data object containing the query
		*/
		function RunInsert($sql){
			try{
				if (!isset($this->connection)){
					$this->Setup();
				}
				$result =  $this->GetConnection()->query($sql);
				return $result;	
			}catch(Exception $e){
				return null;
			}	
		}
		/**
		* Escapes a string like mysqli_real_escape_string
		* @param $string the param to escape
		* @param $quotemarks determines if the escaped string should replace '' with nothing
		* @return string the escaped string
		*/
		public function EscapeString($string,$quotemarks){
			if (!isset($this->connection)){
				$this->Setup();
			}
			if ($quotemarks == false)
				return $this->GetConnection()->quote($string, \PDO::PARAM_STR);	
			else
				return str_replace("\"","",str_replace("'","",$this->GetConnection()->quote($string, \PDO::PARAM_STR)));	
		}
	}
?>
