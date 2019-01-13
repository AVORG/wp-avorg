<?php

namespace Avorg;

require_once(dirname(__FILE__) . '/MockFactory.php');

abstract class TestCase extends \PHPUnit\Framework\TestCase {
	/* Mock Objects */
	
	/** @var AvorgApi $mockAvorgApi */
	protected $mockAvorgApi;
	
	/** @var Php $mockPhp */
	protected $mockPhp;
	
	/** @var Twig|StubTwig $mockTwig */
	protected $mockTwig;
	
	/** @var WordPress|StubWordPress $mockWordPress */
	protected $mockWordPress;
	
	/* Helper Fields */
	
	/** @var MockFactory $objectMocker */
	protected $objectMocker;
	
	/** @var Factory $factory */
	protected $factory;
	
	protected $textDomain = "wp-avorg-plugin";
	
	protected function setUp()
	{
		define( "ABSPATH", "/" );
		
		$this->objectMocker = new MockFactory();
		$this->factory = new Factory();

		$this->factory->injectObjects(
			$this->mockAvorgApi = $this->objectMocker->buildMock("AvorgApi"),
			$this->mockPhp = $this->objectMocker->buildMock("Php"),
			$this->mockTwig = new StubTwig($this),
			$this->mockWordPress = new StubWordPress($this)
		);
	}
	
	protected function output( $data ) {
		fwrite(STDERR, print_r("\n" . var_export($data, true) . "\n", TRUE));
	}
	
	/* Assertions */
	protected function assertCalled( $mock, $method ) {
		$mockName = get_class( $mock );
		$error = "Failed asserting that $mockName->$method() was called.";
		$this->assertNotEmpty( $mock->getCalls( $method ), $error );
	}
	
	protected function assertNotCalled( $mock, $method ) {
		$mockName = get_class( $mock );
		$error = "Failed asserting that $mockName->$method() was not called.";
		$this->assertEmpty( $mock->getCalls( $method ), $error );
	}
	
	protected function assertCalledWith( $mock, $method, ...$arguments ) {
		$calls = $mock->getCalls( $method );
		$mockName = get_class( $mock );
		
		$this->assertMethodExists( $mockName, $method, $calls );
		
//		$error = $this->makeHaystackError( $mockName, $method, $arguments, $calls, "was" );
		$error = "Disabled Error Message";

		$this->assertTrue( in_array( $arguments, $calls, TRUE ), $error );
	}
	
	protected function assertNotCalledWith( $mock, $method, ...$arguments ) {
		$calls = $mock->getCalls( $method );
		$mockName = get_class( $mock );
		
		$this->assertMethodExists( $mockName, $method, $calls );
		
		$error = $this->makeHaystackError( $mockName, $method, $arguments, $calls, "was not" );
		
		$this->assertFalse( in_array( $arguments, $calls, TRUE ), $error );
	}
	
	private function assertMethodExists( $mockName, $method, $calls ) {
		$nullError = "$mockName->$method() does not exist.";
		$this->assertNotNull( $calls, $nullError );
	}
	
	private function makeHaystackError( $mockName, $method, $arguments, $calls, $wasOrWasNot ) {
		$failureMessage = "Failed asserting that $mockName->$method() $wasOrWasNot called with specified args.";
		
		try {
			$errorLines = [
				$failureMessage,
				"Needle:",
				var_export( $arguments, TRUE ),
				"Haystack:",
				var_export( $calls, TRUE )
			];
		} catch ( \Exception $e ) {
			$errorLines = [
				$failureMessage,
				"Failed to export needle and haystack:",
				$e->getMessage()
			];
		}
		
		return implode( "\r\n\r\n", $errorLines );
	}
	
	protected function assertAnyCallMatches($mock, $method, $callback, $errorMessage = "" ) {
		$calls = $mock->getCalls( $method );
		
		$result = array_reduce( $calls, $callback, FALSE );
		
		$this->assertTrue( $result, $errorMessage );
	}

    /**
     * @param $array
     * @return mixed
     */
    protected function convertArrayToObjectRecursively($array)
    {
        return json_decode(json_encode($array), FALSE);
    }
}