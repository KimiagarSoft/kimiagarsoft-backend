<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_belongs_to_role(): void
    {
        $role = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->assertTrue($user->role->is($role));
    }

    public function test_role_has_many_users(): void
    {
        $role = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->assertTrue($role->users->contains($user));
    }

    public function test_admin_user_is_identified_correctly(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $author = User::factory()->create([
            'role_id' => $authorRole->id,
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isAuthor());

        $this->assertFalse($author->isAdmin());
        $this->assertTrue($author->isAuthor());
    }
}