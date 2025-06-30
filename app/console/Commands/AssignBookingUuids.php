<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Illuminate\Support\Str;

class AssignBookingUuids extends Command
{
    protected $signature   = 'app:assign-booking-uuids';
    protected $description = 'Command description';

    public function handle()
    {
        $bookings = Booking::whereNull('uuid')->get();
        foreach($bookings as $b){
            $b->uuid = (string) Str::uuid();
            $b->save();
        }

        $this->info('Assigned UUIDs to all bookings missing a uuid.');
    }
}
