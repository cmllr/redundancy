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
     * This file is the Tests for Kernel.Common.inc.php.
     */
class KernelCommonTest extends PHPUnit_Framework_TestCase
{    
    public function testgetIP()
    {     
        // Arrange
        $expected = -1;
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) == false) {
            $expected = $_SERVER['REMOTE_ADDR'];
        }
        else {
            $expected = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }           
        // Act 
        $got = getIP();  
        // Assert
        $this->assertTrue($expected != null,"The ip could not be determined for the test");
        $this->assertTrue($got != null,"getIP() returned null");
        $this->assertEquals($expected, $got);
    }
    public function testgetIP2()
    {     
        // Arrange
        $expected = -1;
        $client_ip= "";
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) == false) {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        else {
            $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if ($GLOBALS["config"]["Program_Privacy_Mask"] == 1)
        {         
            $expected = substr($client_ip, 0, 4)."[...]";
        }               
        // Act 
        $got = getIP2();        
        // Assert
        $this->assertTrue($expected != null,"The ip could not be determined for the test");
        $this->assertTrue($got != null,"getIP() returned null");
        $this->assertEquals($expected, $got);
    }
    public function testendswith()
    {     
        // Arrange
        $expected = true; 
        $haystack = "this is an horse";
        $needle = "horse";     
        // Act 
        $got = endswith($haystack, $needle);
        // Assert
        $this->assertEquals($expected, $got);
    }
    public function testgetRandomKey()
    {     
        // Arrange
        $expected = 5; 
        // Act 
        $got = getRandomKey($expected);
        $got2 = getRandomKey($expected);
        // Assert
        $this->assertTrue($got != $got2,"The keys are not unique");
        $this->assertEquals($expected, strlen($got), "The length of the random key differs");
    }       
    public function teststartsWith()
    {     
        // Arrange
        $expected = true; 
        $haystack = "this is an horse";
        $needle = "this";     
        // Act 
        $got = startsWith($haystack, $needle);
        // Assert
        $this->assertEquals($expected, $got);
    }
 public function testgetRandomPass()
    {     
        // Arrange
        $pattern = '/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{1,}$/';          
        // Act 
        $length = 5;
        $got = getRandomPass($length);
        $got2 = getRandomPass($length);
        // Assert,
        $this->assertEquals($length, strlen($got), "The length of the password differs from the requested");
        $this->assertTrue($got != $got2,"The passwords are not unique");               
        $this->assertTrue(preg_match($pattern,$got) == 1,"The password is not strong enough");
    }   
    public function testgetSingleNodeXMLDoc()
    {     
        // Arrange
        $expected = "<?xml version=\"1.0\"?>\n<value>redundancy</value>\n"; 
        $data = "redundancy"; 
        // Act 
        $got = getSingleNodeXMLDoc($data);       
        // Assert
        $this->assertEquals($expected, $got);
    }        
}
?>