<?php

namespace App\Events;

use App\Models\Meeting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MeetingReminder implements ShouldBroadcast
{
    public $meeting;

    public function __construct($meeting)
    {
        $this->meeting = $meeting;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ricochet360-techassessment.' . $this->meeting->user_id);
    }

    /**
     * Método para enviar o conteúdo da transmissão.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'meeting_id' => $this->meeting->id,
            'meeting_title' => $this->meeting,
            'meeting_notes' => $this->meeting->notes,
            'client_name' => $this->meeting->client->name,
            'scheduled_at' => $this->meeting->scheduled_at,
        ];
    }
}
