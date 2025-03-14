<?php

namespace Tests\Unit;

use Tests\TestCase;

class AddUserTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_add_user(): void
    {
        $response = $this -> post('/api/v1/admin/users',['name' => 'test', 'email'=>'unit@gmail.com','password' => 'unit@gmail.com']);
        $response->assertStatus(200);
    }
}
