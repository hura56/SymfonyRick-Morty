<?php

namespace App\Controller;

use App\Service\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;

class CharacterController extends AbstractController
{
    #[Route('/', name: 'character_list')]
    public function index(Request $request, RickAndMortyApiService $apiService): Response
    {
        $page = $request->query->getInt('page', 1);
        $name = $request->query->get('name');
        $charactersData = $apiService->getCharacters($page, $name);

        // jesli user wpisze nie istniejaca postac wyswietla sie pickle rick (zamierzone)
        if (empty($charactersData)) {
            $pickleRick = [
                'name' => 'PICKLE RICK',
                'image' => 'https://rickandmortyapi.com/api/character/avatar/265.jpeg',
                'episodes' => ["PICKLE RICK"],
            ];
            return $this->render('character/list.html.twig', [
                'characters' => [$pickleRick],
                'currentPage' => $page,
                'totalPages' => 1,
                'name' => $name,
            ]);
        }

        $characters = $charactersData['results'];
        $httpClient = HttpClient::create();
        $episodeIds = [];

        foreach ($characters as $character) {
            foreach ($character['episode'] as $episodeUrl) {
                $parts = explode('/', $episodeUrl);
                $episodeId = end($parts);
                $episodeIds[$episodeId] = true;
            }
        }

        $episodeIds = array_keys($episodeIds);
        $episodeIdString = implode(',', $episodeIds);
        $episodesResponse = $httpClient->request('GET', "https://rickandmortyapi.com/api/episode/[$episodeIdString]");
        $episodesData = $episodesResponse->toArray();

        $episodes = [];
        if (isset($episodesData['id'])) {
            $episodes[$episodesData['id']] = $episodesData;
        } else {
            foreach ($episodesData as $episode) {
                $episodes[$episode['id']] = $episode;
            }
        }

        foreach ($characters as &$character) {
            $characterEpisodes = [];

            foreach ($character['episode'] as $episodeUrl) {
                $parts = explode('/', $episodeUrl);
                $episodeId = (int) end($parts);

                if (isset($episodes[$episodeId])) {
                    $episode = $episodes[$episodeId];
                    $characterEpisodes[] = $episode['episode'];
                }
            }

            $character['episodes'] = $characterEpisodes;
        }

        return $this->render('character/list.html.twig', [
            'characters' => $characters,
            'currentPage' => $page,
            'totalPages' => $charactersData['info']['pages'],
            'name' => $name,
        ]);
    }
}
