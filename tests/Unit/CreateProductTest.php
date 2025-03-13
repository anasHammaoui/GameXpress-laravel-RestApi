<?php

namespace Tests\Unit;

use Tests\TestCase;

class CreateProductTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $request = $this -> post('/api/v1/admin/products',[
            "name" =>'test',
            "slug" => 'test',
            "price" => 20,
            "stock" => 20,
            "category_id" => 3
        ]);
        $request -> assertStatus(200);

    }
}
