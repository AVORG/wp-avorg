<?php


namespace Avorg\Page\Bible;

use Avorg\DataObject;
use Avorg\DataObject\Bible;
use Avorg\DataObjectRepository\BibleRepository;
use Avorg\Page;
use Avorg\Renderer;
use Avorg\Router;
use Avorg\WordPress;
use function defined;
use Exception;

if (!defined('ABSPATH')) exit;

class Detail extends Page
{
	/** @var BibleRepository $bibleRepository */
	private $bibleRepository;

	protected $defaultPageTitle = "Bible";
	protected $twigTemplate = "page-bible.twig";

	public function __construct(
		BibleRepository $bibleRepository,
		Renderer $renderer,
		Router $router,
		WordPress $wp
	)
	{
		parent::__construct($renderer, $router, $wp);

		$this->bibleRepository = $bibleRepository;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	protected function getPageData()
	{
		return [
			"bible" => $this->getEntity()
		];
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	protected function getTitle()
	{
		$bible = $this->getEntity();

		return $bible ? $bible->name : null;
	}

	/**
	 * @return DataObject
	 * @throws Exception
	 */
	private function getEntity()
	{
		return $this->bibleRepository->getBible($this->getEntityId());
	}

	protected function getEntityId()
	{
		return $this->wp->get_query_var("version") .
			$this->wp->get_query_var("drama");
	}
}