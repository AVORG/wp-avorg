<?php

use Avorg\DataObjectRepository\PresentationRepository;

final class TestPresentationRepository extends Avorg\TestCase
{
    /** @var PresentationRepository $plugin */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->factory->secure("Avorg\\DataObjectRepository\\PresentationRepository");
    }

    /**
     * @throws Exception
     */
    public function testReturnsRecordings()
    {
        $entry = new stdClass();
        $entry->recordings = "item";
        $this->mockAvorgApi->setReturnValue("getRecordings", [$entry]);

        $result = $this->repository->getPresentations();

        $this->assertInstanceOf("\\Avorg\\DataObject\\Recording", $result[0]);
    }

    public function testUsesUnwrappedRecordingWhenInstantiatingRecording()
    {
        $entry = [
			"presenters" => [
				[
					"photo256" => "photo_url"
				]
			]
        ];

        $entryObject = json_decode(json_encode($entry), FALSE);

        $this->mockAvorgApi->setReturnValue("getRecordings", [$entryObject]);

        $result = $this->repository->getPresentations();

        $this->assertEquals("photo_url", $result[0]->getPresenters()[0]->photo);
    }

    public function testLoadsRecordingsWithRecordingUrl()
    {
        $apiRecording = $this->arrayToObject([
			"lang" => "en",
			"id" => "1836",
			"title" => 'E.P. Daniels and True Revival'
        ]);

        $this->mockAvorgApi->setReturnValue("getRecordings", [$apiRecording]);

        $result = $this->repository->getPresentations();

        $this->assertEquals(
            "http://localhost:8080/english/sermons/recordings/1836/ep-daniels-and-true-revival.html",
            $result[0]->getUrl()
        );
    }

    public function testListRecent()
    {
        $this->repository->getPresentations("recent");

        $this->mockAvorgApi->assertMethodCalledWith("getRecordings", "");
    }

    public function testGetPlaylistPresentations()
    {
        $this->mockAvorgApi->loadPlaylist([
            "recordings" => [
                [
                    "title" => "the_title"
                ]
            ]
        ]);

        $recordings = $this->repository->getPlaylistPresentations('id');

        $this->assertEquals("the_title", $recordings[0]->title);
    }
}