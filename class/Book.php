<?php

namespace Avorg;


if (!defined('ABSPATH')) exit;

class Book
{
	/** @var Router $router */
	private $router;

	private $data;

	public function __construct(Router $router)
	{
		$this->router = $router;
	}

	/**
	 * @param mixed $data
	 * @return Book
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}

	public function __isset($name)
	{
		return isset($this->data->$name);
	}

	public function __get($name)
	{
		if (!$this->__isset($name)) return null;

		return $this->data->$name;
	}

	public function getUrl()
	{
		return $this->router->buildUrl("Avorg\Page\Book\Detail", [
			"entity_id" => $this->data->id,
			"slug" => $this->router->formatStringForUrl($this->data->title) . ".html"
		]);
	}
}