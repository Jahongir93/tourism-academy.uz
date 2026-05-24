<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class FaceDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $action;
    public $time;
    public $cameraId;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $action, $time, string $cameraId)
    {
        $this->user = $user;
        $this->action = $action;
        $this->time = $time;
        $this->cameraId = $cameraId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('face-detection');
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'action' => $this->action,
            'time' => $this->time->format('Y-m-d H:i:s'),
            'camera_id' => $this->cameraId,
            'timestamp' => now()->timestamp
        ];
    }
}