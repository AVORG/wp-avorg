<?php

namespace Avorg\Page\Presenter;

use Avorg\AvorgApi;
use Avorg\Page;
use Avorg\Renderer;
use Avorg\RouteFactory;
use Avorg\WordPress;

if (!\defined('ABSPATH')) exit;

class Listing extends Page
{
	/** @var AvorgApi $api */
	private $api;

	protected $defaultPageTitle = "Presenters";
	protected $defaultPageContent = "Presenters";
	protected $twigTemplate = "page-presenters.twig";
	protected $routeFormat = "{ language }/sermons/presenters[/{ letter }]";

	public function __construct(AvorgApi $api, Renderer $renderer, RouteFactory $routeFactory, WordPress $wp)
	{
		parent::__construct($renderer, $routeFactory, $wp);

		$this->setPageIdOptionName();

		$this->api = $api;
	}

	public function throw404($query)
	{
		// TODO: Implement throw404() method.
	}

	protected function getData()
	{
		$this->wp->get_query_var("letter");

		return [
			"presenters" => $this->api->getPresenters() ?: []
		];
	}
}