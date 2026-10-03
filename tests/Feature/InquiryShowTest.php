<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_an_inquiry(): void
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
            ->getJson("/api/v1/inquiries/{$inquiry->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'subject',
                    'message',
                    'status',
                    'created_at',
                ],
            ])
            ->assertJsonPath('data.id', $inquiry->id);
    }

    public function test_author_cannot_view_an_inquiry(): void
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
            ->getJson("/api/v1/inquiries/{$inquiry->id}");

        $response->assertStatus(403);
    }

    public function test_guest_cannot_view_an_inquiry(): void
    {
        $inquiry = Inquiry::factory()->create();

        $response = $this->getJson("/api/v1/inquiries/{$inquiry->id}");

        $response->assertStatus(401);
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
            ->getJson('/api/v1/inquiries/999999');

        $response->assertStatus(404);
    }
}

