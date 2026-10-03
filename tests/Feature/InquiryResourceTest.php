<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_inquiry_response_has_expected_resource_structure(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '09123456789',
            'subject' => 'Website Design',
            'message' => 'I need a new website for my business.',
        ]);

        $response->assertStatus(201)
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
            ]);
    }
}