<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_returns_user_notifications(): void
    {
        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'Test 1']);
        Notification::create(['user_id' => $this->user->id, 'type' => 'payment', 'title' => 'Test 2']);

        $response = $this->actingAs($this->user)->getJson('/notifications');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_index_does_not_return_other_users_notifications(): void
    {
        $otherUser = User::factory()->create();
        Notification::create(['user_id' => $otherUser->id, 'type' => 'loan', 'title' => 'Other']);

        $response = $this->actingAs($this->user)->getJson('/notifications');

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_unread_count_returns_correct_number(): void
    {
        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'Unread 1']);
        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'Unread 2']);
        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'Read', 'read_at' => now()]);

        $response = $this->actingAs($this->user)->getJson('/notifications/unread-count');

        $response->assertOk();
        $response->assertJson(['count' => 2]);
    }

    public function test_mark_as_read(): void
    {
        $notification = Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'Test']);

        $response = $this->actingAs($this->user)->postJson("/notifications/{$notification->id}/read");

        $response->assertOk();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_as_unread(): void
    {
        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'loan',
            'title' => 'Test',
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->postJson("/notifications/{$notification->id}/unread");

        $response->assertOk();
        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_cannot_mark_other_users_notification(): void
    {
        $otherUser = User::factory()->create();
        $notification = Notification::create(['user_id' => $otherUser->id, 'type' => 'loan', 'title' => 'Other']);

        $response = $this->actingAs($this->user)->postJson("/notifications/{$notification->id}/read");

        $response->assertForbidden();
    }

    public function test_mark_all_as_read(): void
    {
        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'A']);
        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'B']);
        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'C', 'read_at' => now()]);

        $response = $this->actingAs($this->user)->postJson('/notifications/mark-all-read');

        $response->assertOk();
        $this->assertEquals(0, Notification::forUser($this->user->id)->unread()->count());
    }

    public function test_delete_notification(): void
    {
        $notification = Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'Test']);

        $response = $this->actingAs($this->user)->deleteJson("/notifications/{$notification->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_cannot_delete_other_users_notification(): void
    {
        $otherUser = User::factory()->create();
        $notification = Notification::create(['user_id' => $otherUser->id, 'type' => 'loan', 'title' => 'Other']);

        $response = $this->actingAs($this->user)->deleteJson("/notifications/{$notification->id}");

        $response->assertForbidden();
    }

    public function test_send_notification(): void
    {
        $response = $this->actingAs($this->user)->postJson('/notifications/send', [
            'user_id' => $this->user->id,
            'type' => 'loan',
            'title' => 'New loan approved',
            'body' => 'Your loan has been approved',
            'icon' => 'loan',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'title' => 'New loan approved',
        ]);
    }

    public function test_send_notification_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/notifications/send', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['user_id', 'type', 'title']);
    }

    public function test_unauthenticated_user_cannot_access(): void
    {
        $response = $this->getJson('/notifications');
        $response->assertUnauthorized();
    }
}
