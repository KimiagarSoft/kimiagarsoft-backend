<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Policies\ServicePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_services(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $service = new Service();

        $policy = new ServicePolicy();

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->view($admin, $service));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $service));
        $this->assertTrue($policy->delete($admin, $service));
    }

    public function test_author_cannot_manage_services(): void
    {
        $authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        $author = User::factory()->create([
            'role_id' => $authorRole->id,
        ]);

        $service = new Service();

        $policy = new ServicePolicy();

        $this->assertFalse($policy->viewAny($author));
        $this->assertFalse($policy->view($author, $service));
        $this->assertFalse($policy->create($author));
        $this->assertFalse($policy->update($author, $service));
        $this->assertFalse($policy->delete($author, $service));
    }
}