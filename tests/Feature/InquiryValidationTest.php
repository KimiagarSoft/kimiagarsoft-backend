<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_is_required(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_email_is_required(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'Test User',
            'subject' => 'Test Subject',
            'message' => 'Test message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_valid(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'subject' => 'Test Subject',
            'message' => 'Test message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_subject_is_required(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject']);
    }

    public function test_message_is_required(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_phone_is_optional(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.phone', null);
    }
}