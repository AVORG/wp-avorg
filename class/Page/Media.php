<?php

namespace Avorg\Page;

use Avorg\AvorgApi;
use Avorg\Page;
use Avorg\PresentationRepository;
use Avorg\Renderer;
use Avorg\WordPress;

if (!\defined('ABSPATH')) exit;

class Media extends Page
{
    /** @var AvorgApi $avorgApi */
    protected $avorgApi;

    /** @var PresentationRepository $presentationRepository */
    protected $presentationRepository;

    /** @var Renderer $twig */
    protected $twig;

    public function __construct(AvorgApi $avorgApi, PresentationRepository $presentationRepository, Renderer $twig, WordPress $wordPress)
    {
        parent::__construct($wordPress);

        $this->avorgApi = $avorgApi;
        $this->presentationRepository = $presentationRepository;
        $this->twig = $twig;
    }

	public function createMediaPage()
	{
		$mediaPageId = $this->wp->call("get_option", "avorgMediaPageId");
		$postStatus = $this->wp->call("get_post_status", $mediaPageId);
		
		if ($mediaPageId === false || $postStatus === false) {
			$id = $this->wp->call("wp_insert_post", array(
				"post_content" => "Media Detail",
				"post_title" => "Media Detail",
				"post_status" => "publish",
				"post_type" => "page"
			), true);
			
			$this->wp->call("update_option", "avorgMediaPageId", $id);
		}
		
		if ($postStatus === "trash") {
			$this->wp->call("wp_publish_post", $mediaPageId);
		}
	}
	
	public function addUi($content)
	{
		if ($this->isMediaPage()) {
			$presentationId = $this->wp->call("get_query_var", "presentation_id");
			$presentation = $this->presentationRepository->getPresentation($presentationId);
			
			$ui = $this->twig->render("organism-recording.twig", ["presentation" => $presentation], true);
			
			return $ui . $content;
		}
		
		return $content;
	}
	
	private function isMediaPage()
	{
		$mediaPageId = intval($this->wp->call("get_option", "avorgMediaPageId"), 10);
		$thisPageId = $this->wp->call("get_the_ID");
		
		return $mediaPageId === $thisPageId;
	}
	
	public function throw404($query)
	{
		try {
			$this->avorgApi->getPresentation($query->get("presentation_id"));
		} catch (\Exception $e) {
			unset($query->query_vars["page_id"]);
			$query->set_404();
			$this->wp->call("status_header", 404);
		}
	}
	
	public function setTitle($title)
	{
		$presentationId = $this->wp->call("get_query_var", "presentation_id");
		
		$presentation = $this->presentationRepository->getPresentation($presentationId);
		
		return $presentation ? "{$presentation->getTitle()} - AudioVerse" : $title;
	}
}