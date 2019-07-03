<?php


namespace Avorg\Page\Story;

use Avorg\DataObjectRepository\StoryRepository;
use Avorg\Page;
use Avorg\Renderer;
use Avorg\WordPress;
use function defined;
use Exception;

if (!defined('ABSPATH')) exit;

class Listing extends Page
{
	/** @var StoryRepository $storyRepository */
	private $storyRepository;

	protected $defaultPageTitle = "Stories";
	protected $defaultPageContent = "Stories";
	protected $twigTemplate = "page-stories.twig";

	public function __construct(
		Renderer $renderer,
		StoryRepository $storyRepository,
		WordPress $wp
	)
	{
		parent::__construct($renderer, $wp);

		return $this->storyRepository = $storyRepository;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	protected function getData()
	{
		return [
			"stories" => $this->storyRepository->getStories()
		];
	}

	protected function getTitle()
	{
		// TODO: Implement getTitle() method.
	}
}