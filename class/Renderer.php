<?php

namespace Avorg;

use Exception;
use natlib\Factory;

if (!\defined('ABSPATH')) exit;

class Renderer
{
	private $factory;
	private $twig;
	
	public function __construct(Factory $factory, Twig $twig)
	{
		$this->factory = $factory;
		$this->twig = $twig;
	}
	
	public function renderNotice($type, $message, $url = null)
	{
		$data = ["type" => $type, "message" => $message];

		if ($url) {
			$data['url'] = $url;
		}

		$this->render("molecule-notice.twig", $data);
	}
	
	public function render($template, $data = [], $shouldReturn = false)
	{
		/** @var TwigGlobal $twigGlobal */
		$twigGlobal = $this->factory->make("Avorg\\TwigGlobal");
		$twigGlobal->setData($data);
		$data = ["_GET" => $_GET, "_POST" => $_POST, "avorg" => $twigGlobal];
		$output = $this->twig->render($template, $data);

		if ($shouldReturn) {
			return $output;
		} else {
			echo $output;
		}
	}
}