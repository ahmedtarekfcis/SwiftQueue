<?php

namespace App\Mail;
namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function build(): self
    {
        $url = route('appointments.status', ['id' => $this->appointment->queue_number]);

        return $this->subject('Your Appointment Details')
            ->view('emails.appointment-status')
            ->with([
                'appointment' => $this->appointment,
                'url' => $url,
            ]);
    }
}
