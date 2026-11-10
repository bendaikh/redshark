<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_routes_are_not_available(): void
    {
        $this->get('/register')->assertNotFound();

        $response = $this->post('/register', [
            'name' => '<a href="https://myxa.cc/">SCAM SERVICE 24/7</a>',
            'email' => 'bot@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertNotFound();
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'bot@example.com']);
        $this->assertSame(0, User::count());
    }
}
