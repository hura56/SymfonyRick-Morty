<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class RickAndMortyApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCharacters(int $page = 1, ?string $name = null): array
    {
        $url = "https://rickandmortyapi.com/api/character?page={$page}";

        if ($name) {
            $url .= "&name=" . urlencode($name);
        }
        try {
            $response = $this->client->request('GET', $url);
            return $response->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getEpisodeNames(array $episodeUrls): array
    {
        $episodes = [];

        foreach ($episodeUrls as $url) {
            $response = $this->client->request('GET', $url);
            $episodes[] = $response->toArray()['name'];
        }

        return $episodes;
    }
}
