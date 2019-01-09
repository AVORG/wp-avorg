<?php

final class TestLocalization extends Avorg\TestCase
{
	/** @var \Avorg\Localization $localization */
	protected $localization;
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->localization = $this->factory->get("Localization");
	}
	
	public function test__iFunctionExsits()
	{
		$this->assertTrue(method_exists($this->localization, "i__"));
	}
	
	public function testRegistersLanguageAdditionMethod()
	{
		$this->assertWordPressFunctionCalledWith(
			"add_action",
			"init",
			[$this->localization, "loadLanguages"]
		);
	}
	
	public function testLoadLanguagesCallsTextDomainLoadingFunction()
	{
		$this->localization->loadLanguages();
		
		$this->assertWordPressFunctionCalledWith(
			"load_plugin_textdomain",
			$this->textDomain,
			false,
			"wp-avorg-plugin/languages"
		);
	}
	
	public function testCallsDoubleUnderscoresWithDomain()
	{
		$this->localization->i__("to translate");
		
		$this->assertWordPressFunctionCalledWith("__", "to translate", $this->textDomain);
	}
	
	public function testCallsUnderscoreEnWithDomain()
	{
		$this->localization->_n("cat","cats",3);
		
		$this->assertWordPressFunctionCalledWith("_n", "cat", "cats", 3, $this->textDomain);
	}
}