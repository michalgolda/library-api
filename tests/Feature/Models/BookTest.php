<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $response = $this->get('/api/books');

        $response->assertStatus(200);
    }

    public function test_index_pagination(): void
    {
        $response = $this->get('/api/books?page=1');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
        ]);
    }

    public function test_show(): void
    {
        $response = $this->get('/api/books/1');
        $response->assertStatus(200);
    }
}
