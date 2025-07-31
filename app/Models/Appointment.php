<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'full_name', 'phone', 'email', 'appointment_date', 'time_slot', 'status', 'queue_number', 'is_currently_serving'
    ];
}
