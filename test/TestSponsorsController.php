<?php

use Avorg\RestController\DataObjects\Sponsors;

final class TestSponsorsController extends Avorg\TestCase
{
    /** @var Sponsors $controller */
    protected $controller;

    private $controllerName = "Sponsors";
    private $route = "/sponsors";
    private $apiMethod = "getSponsors";

    /**
     * @throws ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = $this->factory->secure(
            "Avorg\\RestController\\DataObjects\\{$this->controllerName}");
    }

    public function testsRegistersRoute()
    {
        $this->controller->registerRoutes();

        $this->mockWordPress->assertRestRouteRegistered($this->route);
    }

    public function testGetsEntities()
    {
        $this->controller->handleGet(new WP_REST_Request());

        $this->mockAvorgApi->assertMethodCalled($this->apiMethod);
    }

    public function testUsesStartParam()
    {
        $this->controller->handleGet(new WP_REST_Request(['start' => 25]));

        $this->mockAvorgApi->assertMethodCalledWith($this->apiMethod, null, 25);
    }

    public function testUsesSearchParam()
    {
        $this->controller->handleGet(new WP_REST_Request(['search' => 'term']));

        $this->mockAvorgApi->assertMethodCalledWith($this->apiMethod, 'term', null);
    }
}