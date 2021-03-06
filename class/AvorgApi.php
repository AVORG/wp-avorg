<?php

namespace Avorg;

use Exception;
use Psr\Http\Message\ResponseInterface;
use function defined;

if (!defined('ABSPATH')) exit;

class AvorgApi
{
    /** @var Guzzle $guzzle */
    private $guzzle;

    public function __construct(Guzzle $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * @param $email
     * @param $password
     * @return mixed
     */
    public function logIn($email, $password)
    {
        try {
            return $this->postNew("auth/login", [
                'email' => $email,
                'password' => $password
            ])->data;
        } catch (Exception $e) {
            return False;
        }
    }

    /**
     * @param $email
     * @param $password
     * @param $password2
     * @param $language
     * @return bool|array
     * @throws Exception
     */
    public function register($email, $password, $password2, $language)
    {
        $response = $this->postNew(
            "auth/signup",
            [
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password2,
                'language' => $language
            ],
            False
        );

        if ($response->status_code >= 200 && $response->status_code < 300) {
            return True;
        }

        return get_object_vars($response->errors);
    }

    /**
     * @param $catalogId
     * @param $userId
     * @param $sessionToken
     * @param string $catalog
     * @return bool
     * @throws Exception
     */
    public function addFavorite($catalogId, $userId, $sessionToken, $catalog = 'recording')
    {
        try {
            $this->postOld("favorite", [
                "userId" => $userId,
                "sessionToken" => $sessionToken,
                "catalogId" => $catalogId,
                "catalog" => $catalog
            ]);

            return True;
        } catch (Exception $e) {
            return False;
        }
    }

    /**
     * @param $userId
     * @param $sessionToken
     * @return array
     * @throws Exception
     */
    public function getFavorites($userId, $sessionToken)
    {
        /* TODO: Stop passing userId && sessionToken via query string */
        $result = $this->getResult("favorite", [
            'userId' => $userId,
            'sessionToken' => $sessionToken
        ]);

        return [
            'presenter' => $this->extractFavoritePresenters($result),
            'recording' => $this->extractFavoriteRecordings($result)
        ];
    }

    private function extractFavoritePresenters($result)
    {
        return $this->extractFavoriteEntities(
            $result,
            'presenter',
            'presenters'
        );
    }

    private function extractFavoriteRecordings($result)
    {
        return $this->extractFavoriteEntities(
            $result,
            'recording',
            'recordings'
        );
    }

    private function extractFavoriteEntities($result, $listKey, $itemKey)
    {
        if (!array_key_exists($listKey, $result)) return [];

        $favoriteIds = array_keys(get_object_vars($result->$listKey));

        return array_map(function ($id) use ($result, $listKey, $itemKey) {
            $item = $result->$listKey->$id[0]->$itemKey;
            $item->favoriteId = $id;

            return $item;
        }, $favoriteIds);
    }

    /**
     * @param $catalogId
     * @param $userId
     * @param $sessionToken
     * @param string $catalog
     * @return bool
     * @throws Exception
     */
    public function unFavorite($catalogId, $userId, $sessionToken, $catalog = 'recording')
    {
        try {
            $favorite = $this->findFavorite($catalogId, $userId, $sessionToken, $catalog);

            $this->deleteFavorite($favorite->favoriteId, $userId, $sessionToken);

            return True;
        } catch (Exception $e) {
            return False;
        }
    }

    /**
     * @param $catalogId
     * @param $userId
     * @param $sessionToken
     * @param string $catalog
     * @return bool
     * @throws Exception
     */
    public function isFavorited($catalogId, $userId, $sessionToken, $catalog = 'recording')
    {
        $favorite = $this->findFavorite($catalogId, $userId, $sessionToken, $catalog);

        return $favorite !== null;
    }

    /**
     * @param $catalogId
     * @param $userId
     * @param $sessionToken
     * @param $catalog
     * @return mixed
     * @throws Exception
     */
    private function findFavorite($catalogId, $userId, $sessionToken, $catalog)
    {
        $favorites = $this->getFavorites($userId, $sessionToken);

        if (!array_key_exists($catalog, $favorites)) return null;

        $matches = array_filter($favorites[$catalog], function ($item) use ($catalogId) {
            return intval($item->id) === intval($catalogId);
        });

        return empty($matches) ? null : reset($matches);
    }

    /**
     * @param $favoriteId
     * @param $userId
     * @param $sessionToken
     * @throws Exception
     */
    public function deleteFavorite($favoriteId, $userId, $sessionToken)
    {
        /* TODO: Stop passing userId && sessionToken via query string */
        $this->deleteOld('favorite', [
            "id" => [$favoriteId],
            "userId" => $userId,
            "clearAll" => 0,
            "sessionToken" => $sessionToken
        ]);
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function getOneSeries($id)
    {
        if (!is_numeric($id)) return false;

        $response = $this->getResult("series/$id");

        return $response[0]->series;
    }

    /**
     * @param null $search
     * @param null $start
     * @return array
     * @throws Exception
     */
    public function getAllSeries($search = null, $start = null)
    {
        $result = $this->getResult("series", [
            'search' => $search,
            'start' => $start
        ]);

        return array_map(function ($item) {
            return $item->series;
        }, $result);
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function getSponsor($id)
    {
        if (!is_numeric($id)) return false;

        $response = $this->getResult("sponsors/$id");

        return $response[0]->sponsors;
    }

    /**
     * @param null $search
     * @param null $start
     * @return array
     * @throws Exception
     */
    public function getSponsors($search = null, $start = null)
    {
        $result = $this->getResult("sponsors", [
            'search' => $search,
            'start' => $start
        ]);

        return array_map(function ($item) {
            return $item->sponsors;
        }, $result);
    }

    /**
     * @param null $search
     * @param null $start
     * @return array
     * @throws Exception
     */
    public function getConferences($search = null, $start = null)
    {
        $result = $this->getResult("conferences", [
            'search' => $search,
            'start' => $start
        ]);

        return array_map(function ($item) {
            return $item->conferences;
        }, $result);
    }

    /**
     * @param null $search
     * @param null $start
     * @return array
     * @throws Exception
     */
    public function getStories($search = null, $start = null)
    {
        $result = $this->getResult("audiobooks", [
            'story' => 1,
            'search' => $search,
            'start' => $start
        ]);

        return array_map(function ($item) {
            return $item->audiobooks;
        }, $result);
    }

    /**
     * @param $bibleId
     * @param $bookId
     * @param $testamentId
     * @return array
     * @throws Exception
     */
    public function getBibleChapters($bibleId, $bookId, $testamentId)
    {
        return (array)$this->getResult("audiobibles/books/$bookId", [
            'volume' => $bibleId,
            'testament' => $testamentId
        ]);
    }

    /**
     * @param $bible_id
     * @return array
     * @throws Exception
     */
    public function getBibleBooks($bible_id)
    {
        return (array)$this->getResult("audiobibles/$bible_id");
    }

    /**
     * @param null $search
     * @return array
     * @throws Exception
     */
    public function getBibles($search = null)
    {
        return (array)$this->getResult("audiobibles");
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function getTopic($id)
    {
        if (!is_numeric($id)) return false;

        $response = $this->getResult("topics/$id");

        return $response[0]->topics;
    }

    /**
     * @param null $search
     * @param null $start
     * @return array
     * @throws Exception
     */
    public function getTopics($search = null, $start = null)
    {
        $result = $this->getResult("topics", [
            'search' => $search,
            'start' => $start
        ]);

        return array_map(function ($item) {
            return $item->topics;
        }, $result);
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getBook($id)
    {
        if (!is_numeric($id)) return false;

        $response = $this->getResult("audiobooks/$id");

        return $response[0]->audiobooks;
    }

    /**
     * @param null $search
     * @param null $start
     * @return array
     * @throws Exception
     */
    public function getBooks($search = null, $start = null)
    {
        $result = $this->getResult("audiobooks", [
            'search' => $search,
            'start' => $start
        ]);

        return array_map(function ($item) {
            return $item->audiobooks;
        }, $result);
    }

    /**
     * @param null $search
     * @param null $start
     * @return array|object
     * @throws Exception
     */
    public function getPlaylists($search = null, $start = null)
    {
        return $this->getResult("playlist", [
            'search' => $search,
            'start' => $start
        ]);
    }

    /**
     * @param $userId
     * @param $sessionToken
     * @param null $search
     * @param null $start
     * @return mixed
     * @throws Exception
     */
    public function getPlaylistsByUser($userId, $sessionToken, $search = null, $start = null)
    {
        return $this->getResult("playlist", [
            'userId' => $userId,
            'sessionToken' => $sessionToken,
            'search' => $search,
            'start' => $start
        ]);
    }

    /**
     * @param $id
     * @return array|bool|object
     * @throws Exception
     */
    public function getPlaylist($id)
    {
        if (!is_numeric($id)) return false;

        return $this->getResult("playlist/$id");
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function getPresenter($id)
    {
        if (!is_numeric($id)) return false;

        $response = $this->getResult("presenters/{$id}");

        return $response[0]->presenters;
    }

    /**
     * @param null $search
     * @param null $start
     * @return mixed
     * @throws Exception
     */
    public function getPresenters($search = null, $start = null)
    {
        $result = $this->getResult("presenters", [
            'search' => $search,
            'start' => $start
        ]);

        return array_map(function ($item) {
            return $item->presenters;
        }, $result);
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function getRecording($id)
    {
        if (!is_numeric($id)) return false;

        $response = $this->getResult("recordings/{$id}");

        return $response[0]->recordings;
    }

    /**
     * @param string $list
     * @return array
     * @throws Exception
     */
    public function getRecordings($list = "")
    {
        $endpoint = trim("recordings/$list", "/");

        return $this->getRecordingsResponse($endpoint);
    }

    /**
     * @param $topicId
     * @return array
     * @throws Exception
     */
    public function getTopicRecordings($topicId)
    {
        return $this->getRecordingsResponse("recordings/topic/$topicId");
    }

    /**
     * @param $presenterId
     * @return bool|array
     * @throws Exception
     */
    public function getPresenterRecordings($presenterId)
    {
        if (!is_numeric($presenterId)) return false;

        return $this->getRecordingsResponse("recordings/presenter/$presenterId");
    }

    /**
     * @param $sponsorId
     * @return bool|null
     * @throws Exception
     */
    public function getSponsorRecordings($sponsorId)
    {
        if (!is_numeric($sponsorId)) return false;

        return $this->getRecordingsResponse("recordings/sponsor/$sponsorId");
    }

    /**
     * @param $conferenceId
     * @return bool|array
     * @throws Exception
     */
    public function getConferenceRecordings($conferenceId)
    {
        if (!is_numeric($conferenceId)) return false;

        return $this->getRecordingsResponse("recordings/conference/$conferenceId");
    }

    /**
     * @param $bookId
     * @return bool|array
     * @throws Exception
     */
    public function getBookRecordings($bookId)
    {
        if (!is_numeric($bookId)) return false;

        return $this->getRecordingsResponse("recordings/audiobook/$bookId");
    }

    /**
     * @param $seriesId
     * @return bool|array
     * @throws Exception
     */
    public function getSeriesRecordings($seriesId)
    {
        if (!is_numeric($seriesId)) return false;

        return $this->getRecordingsResponse("recordings/series/$seriesId");
    }

    /**
     * @param $endpoint
     * @return null
     * @throws Exception
     */
    private function getRecordingsResponse($endpoint)
    {
        return array_map(function ($entry) {
            return $entry->recordings;
        }, $this->getResult($endpoint));
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    private function getResult(string $endpoint, array $data = [])
    {
        return $this->getOld($endpoint, $data)->result;
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return mixed|ResponseInterface
     * @throws Exception
     */
    private function getOld(string $endpoint, array $data = [])
    {
        return $this->guzzle->handleOld('GET', $endpoint, [
            'query' => $data
        ]);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    private function postOld(string $endpoint, array $data = [])
    {
        return $this->guzzle->handleOld('POST', $endpoint, [
            'form_params' => $data
        ]);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    private function deleteOld(string $endpoint, array $data = [])
    {
        return $this->guzzle->handleOld('DELETE', $endpoint, [
            'query' => $data
        ]);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @param bool $httpErrors
     * @return mixed
     * @throws Exception
     */
    private function postNew(string $endpoint, array $data = [], bool $httpErrors = True)
    {
        return $this->guzzle->handleNew('POST', $endpoint, [
            'form_params' => $data,
            'http_errors' => $httpErrors
        ]);
    }
}