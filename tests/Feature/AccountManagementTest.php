<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_can_open_account_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $faculty = User::factory()->create(['role' => 'faculty']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($admin)->get(route('accounts.index'))->assertOk()->assertSee('Manage Accounts');
        $this->actingAs($faculty)->get(route('accounts.index'))->assertForbidden();
        $this->actingAs($student)->get(route('accounts.index'))->assertForbidden();
    }

    public function test_admin_can_assign_roles_deactivate_reactivate_and_delete_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($admin)
            ->patch(route('accounts.role', $user), ['role' => 'faculty'])
            ->assertRedirect();
        $this->assertSame('faculty', $user->refresh()->role);

        $this->actingAs($admin)->patch(route('accounts.status', $user))->assertRedirect();
        $this->assertFalse($user->refresh()->is_active);

        $this->actingAs($admin)->patch(route('accounts.status', $user))->assertRedirect();
        $this->assertTrue($user->refresh()->is_active);

        $this->actingAs($admin)->delete(route('accounts.destroy', $user))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_change_deactivate_or_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->patch(route('accounts.role', $admin), ['role' => 'student'])->assertRedirect();
        $this->actingAs($admin)->patch(route('accounts.status', $admin))->assertRedirect();
        $this->actingAs($admin)->delete(route('accounts.destroy', $admin))->assertRedirect();

        $admin->refresh();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_faculty_cannot_call_account_management_actions_directly(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($faculty)
            ->patch(route('accounts.role', $student), ['role' => 'admin'])
            ->assertForbidden();
        $this->actingAs($faculty)->patch(route('accounts.status', $student))->assertForbidden();
        $this->actingAs($faculty)->delete(route('accounts.destroy', $student))->assertForbidden();

        $this->assertSame('student', $student->refresh()->role);
        $this->assertTrue($student->is_active);
    }
}
