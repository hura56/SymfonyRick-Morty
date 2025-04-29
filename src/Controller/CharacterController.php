<?php

namespace App\Controller;

use App\Service\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CharacterController extends AbstractController
{
    #[Route('/characters', name: 'character_list')]
    public function index(Request $request, RickAndMortyApiService $apiService): Response
    {
        $page = $request->query->getInt('page',1);
        
        $charactersData = $apiService->getCharacters($page);

        return $this->render('character/list.html.twig', [
            'characters' => $charactersData['results'],
            'currentPage' => $page,
            'totalPages' => $charactersData['info']['pages'],
        ]);
    }
}
