<?php
	class KernelTest extends PHPUnit_Framework_TestCase{
		//helper method
		protected static function getMethod($name) 
		{
		  //Thanks to https://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit/2798203#2798203
		  $class  = new ReflectionClass("\Redundancy\Kernel\Kernel");
		  $method = $class->getMethod($name);
		  $method->setAccessible(true);
		  return $method;
		}
		public function testConstruct(){
			$c = new \Redundancy\Kernel\Kernel();
		}
		public function testGetVersion(){
			$got = $GLOBALS["Kernel"]->GetVersion();
			$this->assertTrue(!is_null($got));
		}
		public function testGetShortenedVersion(){
			$old = $GLOBALS["Kernel"]->GetVersion();
			$GLOBALS["Kernel"]->Version = "1.2.3-testing-state-4";
			$got = $GLOBALS["Kernel"]->GetShortenedVersion();
			$this->assertTrue($got == "1.2.3.4");
			$GLOBALS["Kernel"]->Version = $old;
		}
		public function testGetConfigValue(){

		}
		public function testSetHTTPHeader01(){
			$m = self::getMethod('SetHTTPHeader');
			$got = $m->invokeArgs($GLOBALS["Kernel"], array(400));
			$this->assertTrue($got == 400);
		}
		public function testSetHTTPHeader02(){
			$m = self::getMethod('SetHTTPHeader');
			$got = $m->invokeArgs($GLOBALS["Kernel"], array("sauerkraut"));
			$this->assertTrue($got == -1);
		}
		public function testIsJson01(){
			$got = $GLOBALS["Kernel"]->isJson("['true']");
			$this->assertTrue($got);
		}
		public function testGetAppName(){
			$got = $GLOBALS["Kernel"]->GetAppName();
			$this->assertTrue($got == "Redundancy");
		}

	}
?>
