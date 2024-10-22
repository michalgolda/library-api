<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $response = $this->get('/api/customers');

        $response->assertStatus(200);
    }

    public function test_store(): void
    {
        $response = $this->post('/api/customers', ['first_name' => 'fake first name', 'last_name' => 'fake last name']);
        $response->assertStatus(201);
    }

    public function test_show(): void
    {
        $response = $this->get('/api/customers/1');
        $response->assertStatus(200);
    }

    public function test_destroy(): void
    {
        $response = $this->delete('/api/customers/1');
        $response->assertStatus(200);
    }
}
