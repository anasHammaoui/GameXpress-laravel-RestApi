<?php

namespace Tests\Unit;

use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_delete_category(): void
    {
        $response = $this -> delete('/api/v1/admin/categories/7');
        $response->assertStatus(200);
    }
}
