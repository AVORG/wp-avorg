<?php

final class TestListShortcode extends Avorg\TestCase
{
	/** @var \Avorg\ListShortcode $listShortcode */
	protected $listShortcode;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->listShortcode = $this->factory->getListShortcode();
	}
	
	// helper functions
	
	private function assertSupportsListType($listType)
	{
		$this->listShortcode->renderShortcode(["list" => $listType]);
		
		$this->assertCalledWith($this->mockAvorgApi, "getPresentations", $listType);
	}
	
	// tests
	
	public function testExists()
	{
		$this->assertTrue(is_object($this->listShortcode));
	}
	
	public function testAddsShortcode()
	{
		$this->listShortcode->addShortcode();
		
		$this->assertWordPressFunctionCalledWith(
			"add_shortcode",
			"avorg-list",
			[$this->listShortcode, "renderShortcode"]
		);
	}
	
	public function testRenderFunction()
	{
		$entry = new stdClass();
		$entry->recordings = "item";
		$this->mockAvorgApi->setReturnValue("getPresentations", [$entry, $entry, $entry]);
		
		$this->listShortcode->renderShortcode("");
		
		$this->assertTwigTemplateRenderedWithData(
			"shortcode-list.twig",
			["recordings" => ["item", "item", "item"]]
		);
	}
	
	public function testRenderFunctionReturnsRenderedView()
	{
		$this->mockTwig->setReturnValue("render", "output");
		
		$result = $this->listShortcode->renderShortcode("");
		
		$this->assertEquals("output", $result);
	}
	
	public function testRenderFunctionDoesNotPassAlongNonsenseListName()
	{
		$this->listShortcode->renderShortcode( [ "list" => "nonsense" ] );
		
		$this->assertCalledWith( $this->mockAvorgApi, "getPresentations", null );
	}
	
	public function testRenderFunctionGetsFeaturedMessages()
	{
		$this->assertSupportsListType("featured");
	}
	
	public function testRenderFunctionGetsPopularMessages()
	{
		$this->assertSupportsListType("popular");
	}
}