<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_inquiry_can_be_created(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '09123456789',
            'subject' => 'Website Design',
            'message' => 'I need a new website for my business.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Test User')
            ->assertJsonPath('data.email', 'test@example.com')
            ->assertJsonPath('data.phone', '09123456789')
            ->assertJsonPath('data.subject', 'Website Design')
            ->assertJsonPath('data.message', 'I need a new website for my business.')
            ->assertJsonPath('data.status', 'new');

        $this->assertDatabaseHas('inquiries', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '09123456789',
            'subject' => 'Website Design',
            'message' => 'I need a new website for my business.',
            'status' => 'new',
        ]);
    }

    public function test_inquiry_status_is_always_new_on_creation(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
            'status' => 'closed',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'new');

        $this->assertDatabaseHas('inquiries', [
            'email' => 'test@example.com',
            'status' => 'new',
        ]);

        $this->assertDatabaseMissing('inquiries', [
            'email' => 'test@example.com',
            'status' => 'closed',
        ]);
    }
}