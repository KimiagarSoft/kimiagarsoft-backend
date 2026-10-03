<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Role;
use App\Models\User;
use App\Policies\InquiryPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_inquiries(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $inquiry = new Inquiry();

        $policy = new InquiryPolicy();

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->view($admin, $inquiry));
        $this->assertTrue($policy->update($admin, $inquiry));
        $this->assertTrue($policy->delete($admin, $inquiry));
    }

    public function test_author_cannot_manage_inquiries(): void
    {
        $authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        $author = User::factory()->create([
            'role_id' => $authorRole->id,
        ]);

        $inquiry = new Inquiry();

        $policy = new InquiryPolicy();

        $this->assertFalse($policy->viewAny($author));
        $this->assertFalse($policy->view($author, $inquiry));
        $this->assertFalse($policy->update($author, $inquiry));
        $this->assertFalse($policy->delete($author, $inquiry));
    }
}