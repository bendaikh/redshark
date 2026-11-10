<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_less_user_cannot_see_company_financials_and_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertForbidden();
        $response->assertSee('Account pending approval');
        $response->assertSee('Log out');
        $response->assertDontSee('Total Balance');
        $response->assertDontSee('Total Expenses');
        $response->assertDontSee('Accounting Data');

        $logout = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $logout->assertRedirect('/');
    }

    public function test_user_without_an_operational_role_cannot_see_company_financials(): void
    {
        Role::findOrCreate('user');
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertForbidden();
        $response->assertDontSee('Total Balance');
        $response->assertDontSee('Accounting Data');
    }

    public function test_superadmin_dashboard_flow_is_unchanged(): void
    {
        Role::findOrCreate('superadmin');
        $user = User::factory()->create();
        $user->assignRole('superadmin');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_media_buyer_dashboard_flow_is_unchanged(): void
    {
        Role::findOrCreate('media_buyer');
        $user = User::factory()->create();
        $user->assignRole('media_buyer');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('media-buyer.dashboard'));

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('media-buyer.dashboard'));
    }

    public function test_role_less_home_redirect_does_not_reveal_financials(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->followingRedirects()
            ->get('/')
            ->assertForbidden()
            ->assertSee('Account pending approval')
            ->assertDontSee('Total Balance');
    }
}
