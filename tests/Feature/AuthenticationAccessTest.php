<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_registration_pages_are_available_to_guests(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign In');
        $this->get(route('register'))->assertOk()->assertSee('Create an account');
    }

    public function test_public_registration_creates_only_a_student_account(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Student User',
            'email' => 'student@example.test',
            'role' => 'admin',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('feedback.create'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'student@example.test',
            'role' => 'student',
        ]);
    }

    public function test_student_login_redirects_to_feedback_and_logout_ends_the_session(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('Password123!'),
        ]);

        $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'Password123!',
            'remember' => true,
        ])->assertRedirect(route('feedback.create'));

        $this->assertAuthenticatedAs($student);
        $this->get(route('feedback.create'))->assertOk();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_feedback_routes_require_authentication_and_sessions_last_one_hour(): void
    {
        $this->get(route('feedback.create'))->assertRedirect(route('login'));
        $this->post(route('feedback.store'))->assertRedirect(route('login'));
        $this->assertSame(60, config('session.lifetime'));
        $this->assertFalse(config('session.expire_on_close'));
    }

    public function test_deactivated_user_cannot_log_in_and_an_existing_session_is_ended(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'is_active' => false,
            'password' => Hash::make('Password123!'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($user)
            ->get(route('feedback.create'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
        $this->assertGuest();
    }

    #[DataProvider('authorizedRoles')]
    public function test_authorized_users_can_log_in_and_access_dashboard_and_export(string $role): void
    {
        $user = User::factory()->create([
            'role' => $role,
            'password' => Hash::make('Password123!'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk();
        $this->get(route('feedback.export'))->assertOk()->assertDownload();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function authorizedRoles(): array
    {
        return [
            'admin' => ['admin'],
            'faculty' => ['faculty'],
        ];
    }
}
