<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_inquiry_status(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $inquiry = Inquiry::factory()->create([
            'status' => 'new',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/inquiries/{$inquiry->id}", [
                'status' => 'read',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $inquiry->id)
            ->assertJsonPath('data.status', 'read');

        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'status' => 'read',
        ]);
    }

    public function test_author_cannot_update_inquiry(): void
    {
        $authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        $author = User::factory()->create([
            'role_id' => $authorRole->id,
        ]);

        $inquiry = Inquiry::factory()->create([
            'status' => 'new',
        ]);

        $response = $this->actingAs($author, 'sanctum')
            ->putJson("/api/v1/inquiries/{$inquiry->id}", [
                'status' => 'read',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'status' => 'new',
        ]);
    }

    public function test_guest_cannot_update_inquiry(): void
    {
        $inquiry = Inquiry::factory()->create([
            'status' => 'new',
        ]);

        $response = $this->putJson("/api/v1/inquiries/{$inquiry->id}", [
            'status' => 'read',
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'status' => 'new',
        ]);
    }

    public function test_admin_cannot_update_inquiry_with_invalid_status(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $inquiry = Inquiry::factory()->create([
            'status' => 'new',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/inquiries/{$inquiry->id}", [
                'status' => 'invalid-status',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'status' => 'new',
        ]);
    }
}

