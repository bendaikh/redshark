<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_still_create_users(): void
    {
        Role::findOrCreate('superadmin');
        Role::findOrCreate('media_buyer');

        $admin = User::factory()->create();
        $admin->assignRole('superadmin');

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New Buyer',
            'email' => 'buyer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'media_buyer',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'buyer@example.com',
            'name' => 'New Buyer',
        ]);

        $created = User::where('email', 'buyer@example.com')->first();
        $this->assertTrue($created->hasRole('media_buyer'));
    }

    public function test_user_names_are_escaped_in_the_admin_list(): void
    {
        Role::findOrCreate('superadmin');

        $admin = User::factory()->create(['name' => 'Safe Admin']);
        $admin->assignRole('superadmin');

        $payload = '<a href="https://myxa.cc/">SCAM SERVICE 24/7</a><script>alert(1)</script>';
        User::factory()->create(['name' => $payload]);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertOk();
        $response->assertSee($payload);
        $response->assertDontSee($payload, false);
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertDontSee('<a href="https://myxa.cc/">', false);
    }
}
