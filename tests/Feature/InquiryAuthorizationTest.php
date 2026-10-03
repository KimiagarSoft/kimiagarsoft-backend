<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_laravel_discovers_inquiry_policy_for_admin(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $inquiry = Inquiry::factory()->create();

        $this->assertTrue(
            $admin->can('view', $inquiry)
        );

        $this->assertTrue(
            $admin->can('update', $inquiry)
        );

        $this->assertTrue(
            $admin->can('delete', $inquiry)
        );

        $this->assertTrue(
            $admin->can('viewAny', Inquiry::class)
        );
    }

    public function test_laravel_discovers_inquiry_policy_for_author(): void
    {
        $authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        $author = User::factory()->create([
            'role_id' => $authorRole->id,
        ]);

        $inquiry = Inquiry::factory()->create();

        $this->assertFalse(
            $author->can('view', $inquiry)
        );

        $this->assertFalse(
            $author->can('update', $inquiry)
        );

        $this->assertFalse(
            $author->can('delete', $inquiry)
        );

        $this->assertFalse(
            $author->can('viewAny', Inquiry::class)
        );
    }
}