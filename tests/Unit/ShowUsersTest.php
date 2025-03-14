<?php

namespace Tests\Unit;

use Tests\TestCase;

class ShowUsersTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_show_users(): void
    {
        $response = $this -> get('/api/v1/admin/users');
        $response->assertStatus(200);
    }
}
