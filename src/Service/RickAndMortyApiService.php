<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class RickAndMortyApiService
{
    private $client;
    private const API_BASE = 'https://rickandmortyapi.com/api';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCharacters(int $page = 1): array
    {
        $url = "https://rickandmortyapi.com/api/character?page={$page}";

        $response = $this->client->request('GET', $url);
        return $response->toArray();
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
