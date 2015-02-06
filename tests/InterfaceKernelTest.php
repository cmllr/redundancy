<?php
	class InterfaceKernelTest extends PHPUnit_Framework_TestCase{
		//helper method
		protected static function getMethod($name) 
		{
		  //Thanks to https://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit/2798203#2798203
		  $class  = new ReflectionClass("\Redundancy\Kernel\UserKernel");
		  $method = $class->getMethod($name);
		  $method->setAccessible(true);
		  return $method;
		}
		//***********************Tests SplitFileNameAndExtension()***********************
		public function testSplitFileNameAndExtension01(){				
		  	$got = $GLOBALS["Kernel"]->InterfaceKernel->SplitFileNameAndExtension("test.png");	 			
		  	$this->assertTrue($got[0] == "test");
		  	$this->assertTrue($got[1] == ".png");
		}		
		public function testSplitFileNameAndExtension02(){				
		  	$got = $GLOBALS["Kernel"]->InterfaceKernel->SplitFileNameAndExtension("test");	 			
		  	$this->assertTrue($got[0] == "test");
		  	$this->assertTrue($got[1] == "");
		}
		public function testSplitFileNameAndExtension03(){				
		  	$got = $GLOBALS["Kernel"]->InterfaceKernel->SplitFileNameAndExtension("test..png");	 			
		  	$this->assertTrue($got[0] == "test.");
		  	$this->assertTrue($got[1] == ".png");
		}
		public function testSplitFileNameAndExtension04(){				
		  	$got = $GLOBALS["Kernel"]->InterfaceKernel->SplitFileNameAndExtension("test.tar.gz");	 			
		  	$this->assertTrue($got[0] == "test.tar");
		  	$this->assertTrue($got[1] == ".gz");
		}
		public function testGetLanguageValue01(){
			$got = $GLOBALS["Kernel"]->InterfaceKernel->GetLanguageValue("Translation_Author");
			$this->assertTrue(!is_null($got));
		}
		public function testGetGetAllLanguageValues01(){
			$got = $GLOBALS["Kernel"]->InterfaceKernel->GetAllLanguageValues();
			$this->assertTrue(count($got) != 0);
		}
		public function testGetCurrentLanguage01(){
			$got = $GLOBALS["Kernel"]->InterfaceKernel->GetCurrentLanguage();
			$expected = $GLOBALS["Kernel"]->SystemKernel->GetSetting("Program_Language")->Value; 	
			$this->assertTrue($got == $expected);
		}
		public function testSetCurrentLanguages01(){
			$GLOBALS["Kernel"]->InterfaceKernel->SetCurrentLanguage("en");
			$got = $GLOBALS["Kernel"]->InterfaceKernel->GetCurrentLanguage();
			$GLOBALS["Kernel"]->InterfaceKernel->SetCurrentLanguage($GLOBALS["Kernel"]->SystemKernel->GetSetting("Program_Language")->Value);
			$this->assertTrue($got == "en");
		}
		public function testGetInstalledLanguages()
		{
			$got = $GLOBALS["Kernel"]->InterfaceKernel->GetInstalledLanguages();
			$this->assertTrue(count($got) == 3);			
		}
		public function testGetEllipsedDisplayName01(){
			$got = $GLOBALS["Kernel"]->InterfaceKernel->GetEllipsedDisplayName("thisishort.png");
			$this->assertTrue($got[0] == "thisishort");
			$this->assertTrue($got[1] == ".png");
		}
		public function testGetEllipsedDisplayName02(){
			$got = $GLOBALS["Kernel"]->InterfaceKernel->GetEllipsedDisplayName("thisisevenlongerthantheshortone.png");
			$this->assertTrue(strlen($got[0]) == 23);
			$this->assertTrue($got[1] == ".png");
		}
		public function testGetErrorTranslationCode01(){
			$got = $GLOBALS["Kernel"]->InterfaceKernel->GetErrorCodeTranslation("R_ERR_DoesNotExist");
			$this->assertTrue($got == "R_ERR_DoesNotExist");
		}
		public function testGetErrorTranslationCode02(){
			$got = $GLOBALS["Kernel"]->InterfaceKernel->GetErrorCodeTranslation("R_ERR_12");
			$this->assertTrue($got != "R_ERR_12");
		}
		public function testConstruct(){
			$c = new \Redundancy\Kernel\InterfaceKernel(-1);
		}

	}
?>
