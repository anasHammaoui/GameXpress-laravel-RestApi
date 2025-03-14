<?php

namespace Tests\Unit;

use Tests\TestCase;

class UpdateCategroyTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_update_category(): void
    {

        // Then update it
        $request = $this->put('/api/v1/admin/categories/' . 7, ['name' => 'Updated Sport']);
                
        $request->assertStatus(200);
    }
}
