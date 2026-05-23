<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationModelTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_notification_belongs_to_user(): void
    {
        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'loan',
            'title' => 'Test notification',
            'body' => 'Test body',
            'icon' => 'bell',
        ]);

        $this->assertEquals($this->user->id, $notification->user->id);
    }

    public function test_mark_as_read(): void
    {
        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'loan',
            'title' => 'Test',
        ]);

        $this->assertNull($notification->read_at);
        $this->assertFalse($notification->isRead());

        $notification->markAsRead();

        $this->assertNotNull($notification->fresh()->read_at);
        $this->assertTrue($notification->fresh()->isRead());
    }

    public function test_mark_as_unread(): void
    {
        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'loan',
            'title' => 'Test',
            'read_at' => now(),
        ]);

        $this->assertNotNull($notification->read_at);

        $notification->markAsUnread();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_mark_as_read_idempotent(): void
    {
        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'loan',
            'title' => 'Test',
        ]);

        $notification->markAsRead();
        $firstReadAt = $notification->fresh()->read_at;

        $notification->markAsRead();

        $this->assertEquals($firstReadAt->timestamp, $notification->fresh()->read_at->timestamp);
    }

    public function test_scope_unread(): void
    {
        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'Unread']);
        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'Read', 'read_at' => now()]);

        $unread = Notification::unread()->get();

        $this->assertCount(1, $unread);
        $this->assertEquals('Unread', $unread->first()->title);
    }

    public function test_scope_for_user(): void
    {
        $otherUser = User::factory()->create();

        Notification::create(['user_id' => $this->user->id, 'type' => 'loan', 'title' => 'User 1']);
        Notification::create(['user_id' => $otherUser->id, 'type' => 'loan', 'title' => 'User 2']);

        $userNotifications = Notification::forUser($this->user->id)->get();

        $this->assertCount(1, $userNotifications);
        $this->assertEquals('User 1', $userNotifications->first()->title);
    }

    public function test_read_at_cast_to_datetime(): void
    {
        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'loan',
            'title' => 'Test',
            'read_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $notification->read_at);
    }
}
