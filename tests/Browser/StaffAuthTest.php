<?php

namespace Tests\Browser;

use App\Models\Staff;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class StaffAuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected Staff $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->admin = Staff::where('email', 'admin@crm.com')->first();
    }

    public function test_login_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login/staff')
                ->assertSee('Staff Access')
                ->assertSee('Email Address')
                ->assertSee('Password')
                ->assertSee('Login to Dashboard')
                ->assertPresent('input[name=email]')
                ->assertPresent('input[name=password]');
        });
    }

    public function test_successful_login_redirects_to_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login/staff')
                ->type('email', 'admin@crm.com')
                ->type('password', 'password')
                ->press('Login to Dashboard')
                ->assertPathIs('/admin/dashboard')
                ->assertSee('Welcome back')
                ->assertSee('Admin');
        });
    }

    public function test_login_with_invalid_password_shows_error(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login/staff')
                ->type('email', 'admin@crm.com')
                ->type('password', 'wrongpassword')
                ->press('Login to Dashboard')
                ->assertPathIs('/login/staff')
                ->assertSee('Invalid staff credentials');
        });
    }

    public function test_login_with_nonexistent_email_shows_error(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login/staff')
                ->type('email', 'nonexistent@crm.com')
                ->type('password', 'password')
                ->press('Login to Dashboard')
                ->assertPathIs('/login/staff')
                ->assertSee('Invalid staff credentials');
        });
    }

    public function test_authenticated_user_redirected_to_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin, 'staff')
                ->visit('/login/staff')
                ->assertPathIs('/admin/dashboard');
        });
    }

    public function test_logout_redirects_to_homepage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin, 'staff')
                ->visit('/admin/dashboard')
                ->assertPathIs('/admin/dashboard')
                ->logout('staff')
                ->visit('/admin/dashboard')
                ->assertPathIs('/login/staff');
        });
    }

    public function test_dashboard_inaccessible_without_authentication(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/dashboard')
                ->assertPathIs('/login/staff');
        });
    }
}
