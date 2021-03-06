<?php

namespace Avorg;

use Exception;
use Twig\Markup;
use function defined;

if (!defined('ABSPATH')) exit;

class TwigGlobal
{
	/** @var Localization $localization */
	private $localization;

	/** @var Router $router */
	private $router;

	/** @var ScriptFactory $scriptFactory */
	private $scriptFactory;

	/** @var Session $session */
	private $session;
	
	/** @var WordPress $wp */
	private $wp;
	
	private $data = [];
	
	public function __construct(
		Localization $localization,
		Router $router,
		ScriptFactory $scriptFactory,
		Session $session,
		WordPress $wordPress
	)
	{
		$this->localization = $localization;
		$this->router = $router;
		$this->scriptFactory = $scriptFactory;
		$this->session = $session;
		$this->wp = $wordPress;

		$this->data["query"] = $this->wp->get_all_query_vars();

        $this->addSessionData();
    }

	public function __get($name)
	{
		return $this->__isset($name) ? $this->data[$name] : null;
	}

    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }
	
	public function i__($string)
	{
		return $this->localization->i__($string);
	}
	
	public function _n($single, $plural, $number)
	{
		return $this->localization->_n($single, $plural, $number);
	}
	
	public function setData($data)
	{
		$this->data = array_merge($this->data, (array) $data);
		return $this;
	}

	public function getLanguage()
	{
		return $this->router->getRequestLanguage();
	}

	public function getRequestUri()
	{
		return $this->router->getFullRequestUri();
	}

	public function getRequestPath()
	{
		return $this->router->getRequestPath();
	}

	/**
	 * @param $path
	 * @return Markup
	 * @throws Exception
	 */
	public function loadScript($path)
	{
		$preparedData = $this->prepareDataForScript();

		$this->scriptFactory->getScript("script/$path")->setData($preparedData)->enqueue();

		return new Markup("<p>Attempted to load script $path</p>", "UTF-8");
	}

	private function prepareDataForScript()
	{
		return $this->array_map_recursive(function($leaf) {
			$isRecodable = is_object($leaf) &&
				method_exists($leaf, "toArray");

			return $isRecodable ? $leaf->toArray() : $leaf;
		}, $this->data);
	}

	private function array_map_recursive($callback, $array)
	{
		// https://stackoverflow.com/a/39637749/937377

		$func = function ($item) use (&$func, &$callback) {
			return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
		};

		return array_map($func, $array);
	}

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    private function addSessionData(): void
    {
        $this->data["session"] = [
            "email" => $this->session->email
        ];
    }
}