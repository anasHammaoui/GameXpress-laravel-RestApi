<?php

namespace Tests\Unit;

use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_create_category(): void
    {
        $request = $this -> post('/api/v1/admin/categories',['name'=> 'Sport']);
        
        $request->assertStatus(200);
    }
}
