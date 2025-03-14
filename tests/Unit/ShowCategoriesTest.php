<?php

namespace Tests\Unit;

use Tests\TestCase;

class ShowCategoriesTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_category(): void
    {
        $request = $this -> get('/api/v1/admin/categories');
        $request->assertStatus(200);
    }
}
