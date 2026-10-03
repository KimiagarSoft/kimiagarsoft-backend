<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_an_inquiry(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $inquiry = Inquiry::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/inquiries/{$inquiry->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('inquiries', [
            'id' => $inquiry->id,
        ]);
    }

    public function test_author_cannot_delete_an_inquiry(): void
    {
        $authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        $author = User::factory()->create([
            'role_id' => $authorRole->id,
        ]);

        $inquiry = Inquiry::factory()->create();

        $response = $this->actingAs($author, 'sanctum')
            ->deleteJson("/api/v1/inquiries/{$inquiry->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
        ]);
    }

    public function test_guest_cannot_delete_an_inquiry(): void
    {
        $inquiry = Inquiry::factory()->create();

        $response = $this->deleteJson(
            "/api/v1/inquiries/{$inquiry->id}"
        );

        $response->assertStatus(401);

        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
        ]);
    }

    public function test_admin_gets_404_for_nonexistent_inquiry(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/v1/inquiries/999999');

        $response->assertStatus(404);
    }
}

