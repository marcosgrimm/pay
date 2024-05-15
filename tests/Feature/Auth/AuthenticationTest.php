<?php

namespace Tests\Feature\Auth;

use App\Models\Merchant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_random_token_cannot_be_accessed_in_prod(): void
    {
        // force change environment to prod
        putenv('APP_ENV=production');
        $response = $this->get('/demo-random-token');

        $response->assertStatus(400);
    }

    public function test_demo_random_token_can_be_accessed_in_local_with_no_results(): void
    {
        // change environment to prod
        config(['app.env' => 'local']);

        $response = $this->get('/demo-random-token');

        $response->assertStatus(404);
    }

    public function test_demo_random_token_can_be_accessed_in_local_with_results(): void
    {
        // change environment to prod
        config(['app.env' => 'local']);

        Merchant::factory(5)->create();

        $response = $this->get('/demo-random-token');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'token'
        ]);
    }
}
