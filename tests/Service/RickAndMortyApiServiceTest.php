<?php

namespace App\Tests\Service;

use App\Service\RickAndMortyApiService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RickAndMortyApiServiceTest extends TestCase
{
    public function testGetCharactersSuccess()
    {
        /** @var HttpClientInterface|\PHPUnit\Framework\MockObject\MockObject $mockClient */
        $mockClient = $this->createMock(HttpClientInterface::class);

        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://rickandmortyapi.com/api/character?page=1')
            ->willReturn($mockResponse);

        $mockResponse->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'results' => [
                    ['id' => 1, 'name' => 'Rick Sanchez'],
                    ['id' => 2, 'name' => 'Morty Smith'],
                ]
            ]);

        $apiService = new RickAndMortyApiService($mockClient);

        $characters = $apiService->getCharacters();

        $this->assertCount(2, $characters['results']);
        $this->assertEquals('Rick Sanchez', $characters['results'][0]['name']);
        $this->assertEquals('Morty Smith', $characters['results'][1]['name']);
    }

    public function testGetCharactersFailure()
    {
        /** @var HttpClientInterface|\PHPUnit\Framework\MockObject\MockObject $mockClient */
        $mockClient = $this->createMock(HttpClientInterface::class);

        $mockClient->method('request')
            ->willThrowException(new \Exception('API error'));

        $apiService = new RickAndMortyApiService($mockClient);

        $characters = $apiService->getCharacters();

        $this->assertEmpty($characters);
    }
}
