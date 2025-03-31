<?php

namespace App\Console\Commands;

use App\Events\MeetingReminder;
use App\Models\Meeting;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendMeetingReminders extends Command
{
    protected $signature = 'meetings:send-reminders';
    protected $description = 'Send reminders for meetings starting in 30 minutes or less';

    public function handle()
    {
        // Start with UTC time
        $now = Carbon::now('UTC');
        $this->info("Current time: " . $now->copy()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s') . " (America/Sao_Paulo)");

        // Debug all meetings
        $allMeetings = Meeting::with('client')->get();
        $this->info("\nAll meetings in database:");
        foreach ($allMeetings as $meeting) {
            $meetingTime = Carbon::parse($meeting->scheduled_at)->setTimezone('America/Sao_Paulo');
            $this->info("ID: {$meeting->id}, Time: {$meetingTime}, Client: {$meeting->client->name}");
        }

        // Query using UTC times and proper timezone conversion
        $meetings = Meeting::where('scheduled_at', '>', $now->copy()->setTimezone('UTC'))
            ->where('scheduled_at', '<=', $now->copy()->addMinutes(30)->setTimezone('UTC'))
            ->with('client')
            ->get();

        $this->info("\nTime window (UTC):");
        $this->info("From: " . $now->format('Y-m-d H:i:s'));
        $this->info("To: " . $now->copy()->addMinutes(30)->format('Y-m-d H:i:s'));

        if ($meetings->isEmpty()) {
            $this->info('No upcoming meetings found in the next 30 minutes.');
            return 0;
        }

        foreach ($meetings as $meeting) {
            $meetingTime = Carbon::parse($meeting->scheduled_at)->setTimezone('America/Sao_Paulo');
            $this->info("\nFound meeting in time window:");
            $this->info("ID: {$meeting->id}");
            $this->info("Client: {$meeting->client->name}");
            $this->info("Scheduled at (local): {$meetingTime}");
            $this->info("Scheduled at (UTC): {$meeting->scheduled_at}");

            try {
                event(new MeetingReminder($meeting));
                $this->info("✓ Reminder sent successfully");
                Log::info("Reminder sent for meeting {$meeting->id} with {$meeting->client->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send reminder: {$e->getMessage()}");
                Log::error("Failed to send reminder for meeting {$meeting->id}: " . $e->getMessage());
            }
        }

        return 0;
    }
}
