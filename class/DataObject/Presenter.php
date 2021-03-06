<?php

namespace Avorg\DataObject;

use Avorg\DataObject;
use Avorg\DataObjectRepository\PresentationRepository;
use Avorg\Renderer;
use Avorg\Router;
use function defined;

if (!defined('ABSPATH')) exit;

class Presenter extends DataObject
{
	/** @var PresentationRepository $presentationRepository */
	private $presentationRepository;

	protected $detailClass = "Avorg\Page\Presenter\Detail";

	public function __construct(
		PresentationRepository $presentationRepository,
		Renderer $renderer,
		Router $router
	)
	{
		parent::__construct($renderer, $router);

		$this->presentationRepository = $presentationRepository;
		$this->router = $router;
	}

	protected function getDataArray()
    {
        return array_merge(parent::getDataArray(), [
            "nameReversed" => $this->getNameReversed(),
            "title" => $this->getNameReversed(),
            "photo" => $this->getPhoto(),
            "name" => $this->getName()
        ]);
    }

    public function getRecordings()
	{
		return $this->presentationRepository->getPresenterPresentations($this->getId());
	}

	public function getPhoto()
    {
        return $this->data->photo256 ?? null;
    }

	public function getName()
	{
		return trim(implode(" ", array_filter([
            $this->__get("givenName"),
            $this->__get("surname"),
            $this->__get("suffix"),
        ])));
	}

	public function getNameReversed()
	{
		$first = $this->__get("givenName");
		$last = $this->__get("surname");
		$suffix = $this->__get("suffix");

		return $suffix ? "$last $suffix, $first" : "$last, $first";
	}

	public function getTitle()
    {
        return $this->getName();
    }
}
