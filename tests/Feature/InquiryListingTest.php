<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_inquiries(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        Inquiry::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/inquiries');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'subject',
                        'message',
                        'status',
                        'created_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_author_cannot_list_inquiries(): void
    {
        $authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        $author = User::factory()->create([
            'role_id' => $authorRole->id,
        ]);

        $response = $this->actingAs($author, 'sanctum')
            ->getJson('/api/v1/inquiries');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_list_inquiries(): void
    {
        $response = $this->getJson('/api/v1/inquiries');

        $response->assertStatus(401);
    }
}

