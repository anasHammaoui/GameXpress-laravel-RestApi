<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase ;

class ShowProductsTest extends TestCase
{
    /*
     * A basic unit test example.
     */
    public function test_show_products(): void
    {
        $response = $this -> get('/api/v1/admin/products');
        $response->assertStatus(200);
    }
}
