<?php

namespace Avorg\DataObject;

use Avorg\DataObject;
use Avorg\DataObjectRepository\BibleChapterRepository;
use Avorg\Renderer;
use Avorg\Router;
use function defined;
use Exception;
use natlib\Stub;

if (!defined('ABSPATH')) exit;

class BibleBook extends DataObject
{
	/** @var BibleChapterRepository $bibleChapterRepository */
	private $bibleChapterRepository;

	public function __construct(
	    BibleChapterRepository $bibleChapterRepository,
        Renderer $renderer,
        Router $router
    )
	{
		parent::__construct($renderer, $router);

		$this->bibleChapterRepository = $bibleChapterRepository;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function toArray()
	{
		return array_merge(parent::toArray(), [
			"chapters" => $this->getChapterArrays()
		]);
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	private function getChapterArrays()
	{
		return array_map(function (DataObject\Recording\BibleChapter $chapter) {
			return $chapter->toArray();
		}, $this->getChapters());
	}

	/**
	 * @throws Exception
	 */
	public function getChapters()
	{
		return $this->bibleChapterRepository->getChapters(
			$this->dam_id, $this->book_id, $this->testament);
	}
}