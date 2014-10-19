<?php
	class UpdateKernelTest extends PHPUnit_Framework_TestCase{
		//helper method
		protected static function getMethod($name) 
		{
		  //Thanks to https://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit/2798203#2798203
		  $class  = new ReflectionClass("\Redundancy\Kernel\UserKernel");
		  $method = $class->getMethod($name);
		  $method->setAccessible(true);
		  return $method;
		}
		//***********************Tests IsUpdateAvailable()***********************
		public function testIsUpdateAvailable01(){				
			$remote = array(
				"major" => 1,
				"minor" => 9,
				"patch" => 15,
				"branch" =>"test",
				"stage" => "test",
				"update" => 0
			);
			$local = array(
				"major" => 1,
				"minor" => 9,
				"patch" => 14,
				"branch" =>"test",
				"stage" => "test",
				"update" => 0
			);
		  	$got = $GLOBALS["Kernel"]->UpdateKernel->IsUpdateAvailable($remote,$local);	
		  	$this->assertTrue($got);
		}	
		public function testIsUpdateAvailable02(){				
			$remote = array(
				"major" => 1,
				"minor" => 9,
				"patch" => 15,
				"branch" =>"test",
				"stage" => "test",
				"update" => 0
			);
			$local = array(
				"major" => 1,
				"minor" => 8,
				"patch" => 15,
				"branch" =>"test",
				"stage" => "test",
				"update" => 0
			);
		  	$got = $GLOBALS["Kernel"]->UpdateKernel->IsUpdateAvailable($remote,$local);	
		  	$this->assertTrue($got);
		}	
		public function testIsUpdateAvailable03(){				
			$remote = array(
				"major" => 0,
				"minor" => 9,
				"patch" => 15,
				"branch" =>"test",
				"stage" => "test",
				"update" => 0
			);
			$local = array(
				"major" => 1,
				"minor" => 8,
				"patch" => 15,
				"branch" =>"test",
				"stage" => "test",
				"update" => 0
			);
		  	$got = $GLOBALS["Kernel"]->UpdateKernel->IsUpdateAvailable($remote,$local);	
		  	$this->assertTrue($got);
		}
		public function testIsUpdateAvailable04(){				
			$remote = array(
				"major" => 1,
				"minor" => 8,
				"patch" => 15,
				"branch" =>"test",
				"stage" => "test",
				"update" => 0
			);
			$local = array(
				"major" => 1,
				"minor" => 8,
				"patch" => 15,
				"branch" =>"test",
				"stage" => "test",
				"update" => 0
			);
		  	$got = $GLOBALS["Kernel"]->UpdateKernel->IsUpdateAvailable($remote,$local);	
		  	$this->assertFalse($got);
		}
	}
?>
