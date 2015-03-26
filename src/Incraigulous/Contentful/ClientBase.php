<?php
namespace Incraigulous\Contentful;

use GuzzleHttp;
use Incraigulous\Contentful\ClientInterface;

abstract class ClientBase implements ClientInterface {

    protected $client;
    private $spaceId;
    private $accessToken;
    protected $endpointBase;

    function __construct($spaceId, $accessToken) {
        $this->spaceId = $spaceId;
        $this->accessToken = $accessToken;
        $this->client = new GuzzleHttp\Client();
    }

    /**
     * Get the Guzzle Client.
     * @return GuzzleHttp\Client
     */
    function getClient() {
        return $this->client;
    }

    /**
     * Format the authorization header.
     * @return string
     */
    function getBearer() {
        return ' Bearer ' . $this->accessToken;
    }

    /**
     * Get the endpoint.
     * @return string
     */
    function getEndpoint() {
        return $this->endpointBase . $this->spaceId;
    }

    /**
     * Make a get request.
     * @param $resource
     * @param array $query
     * @return mixed
     */
    function get($resource, $query = array()) {
        return $this->client->get($this->build_url($resource, $query), [
            'headers' => [
                'Authorization' => $this->getBearer()
            ]
        ])->json();
    }

    /**
     * Build the query URL.
     * @param $resource
     * @param $query
     * @return string
     */
    function build_url($resource, $query) {
        $url = $this->getEndpoint();
        if ($resource) $url .= '/' . $resource;
        if (!empty($query)) $url .= '?' . http_build_query($query);
        return $url;
    }
}