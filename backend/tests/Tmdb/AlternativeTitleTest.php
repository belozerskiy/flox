<?php

  use App\AlternativeTitle;
  use App\Http\Controllers\ItemController;
  use App\Item;
  use App\Services\Storage;
  use App\Services\TMDB;
  use GuzzleHttp\Client;
  use GuzzleHttp\Handler\MockHandler;
  use GuzzleHttp\HandlerStack;
  use Illuminate\Foundation\Testing\DatabaseMigrations;
  use Illuminate\Support\Facades\Input;
  use GuzzleHttp\Psr7\Response;

  class AlternativeTitleTest extends TestCase {

    use DatabaseMigrations;

    /** @test */
    public function it_can_store_alternative_titles_for_movies()
    {
      $tmdbMock = $this->createTmdb($this->fixtureAlternativeTitleMovie);
      $movie = $this->getMovie();

      $item = new Item();
      $item->addAlternativeTitles($movie, $tmdbMock);

      $this->assertCount(4, AlternativeTitle::all());

      $this->seeInDatabase('alternative_titles', [
        'title' => 'Disney Pixar Finding Nemo'
      ]);
    }

    /** @test */
    public function it_can_store_alternative_titles_for_tv_shows()
    {
      $tmdbMock = $this->createTmdb($this->fixtureAlternativeTitleTv);
      $tv = $this->getTv();

      $item = new Item();
      $item->addAlternativeTitles($tv, $tmdbMock);

      $this->assertCount(3, AlternativeTitle::all());

      $this->seeInDatabase('alternative_titles', [
        'title' => 'DBZ'
      ]);
    }

    private function createTmdb($fixture)
    {
      $mock = new MockHandler([
        new Response(200, [
          'Content-Type' => 'application/json',
          'X-RateLimit-Remaining' => [40],
        ], $fixture),
      ]);

      $handler = HandlerStack::create($mock);
      $client = new Client(['handler' => $handler]);

      return new TMDB($client);
    }
  }
