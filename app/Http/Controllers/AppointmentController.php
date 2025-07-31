<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function create()
    {
        $currentlyServing = Appointment::where('is_currently_serving', true)->get();
        $waitingCount = Appointment::where('status', 'waiting')->count();
        $estimatedWait = $waitingCount * 2; // مثلاً 2 دقيقة لكل شخص
        $appointmentsBooked = Appointment::count();
        $bookingStatus = \App\Models\SystemSetting::getValue('appointment_booking_status', 'active');

        return view('appointments.create', compact(
            'currentlyServing',
            'waitingCount',
            'estimatedWait',
            'appointmentsBooked',
            'bookingStatus'
        ));
    }

    public function store(Request $request)
    {
        if (SystemSetting::getValue('appointment_booking_status') === 'paused') {
            return redirect()->back()
                ->withErrors(['booking_paused' => 'Appointment booking is currently paused. Please try again later.']);
        }

        $validated = $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'appointment_date' => 'required|date',
            'time_slot' => 'required|string',
        ]);

        $existing = Appointment::where('email', $validated['email'])
            ->orWhere('phone', $validated['phone'])
            ->first();

        if ($existing) {
            $queueNumber = Appointment::where('appointment_date', $existing->appointment_date)
                ->where('time_slot', $existing->time_slot)
                ->where('id', '<=', $existing->id)
                ->count();

            return view('appointments.status', [
                'appointment' => $existing,
                'queueNumber' => $queueNumber
            ]);
        }

        $lastQueue = Appointment::where('appointment_date', $validated['appointment_date'])
            ->where('time_slot', $validated['time_slot'])
            ->max('queue_number') ?? 0;

        $validated['queue_number'] = $lastQueue + 1;

        $appointment = Appointment::create($validated);

        return view('appointments.confirmation', [
            'appointment' => $appointment,
            'queueNumber' => $appointment->queue_number,
        ]);
    }


    public function status(Request $request)
    {
        $email = $request->input('email');
        $phone = $request->input('phone');

        if (!$email && !$phone) {
            return redirect()->back()->withErrors(['message' => 'Please provide phone or email to check your status.']);
        }

        $appointment = Appointment::when($email, fn($q) => $q->orWhere('email', $email))
            ->when($phone, fn($q) => $q->orWhere('phone', $phone))
            ->latest()
            ->first();

        if (!$appointment) {
            return redirect()->back()->withErrors(['message' => 'No appointment found with the provided details.']);
        }

        $queueNumber = Appointment::where('appointment_date', $appointment->appointment_date)
            ->where('time_slot', $appointment->time_slot)
            ->where('id', '<=', $appointment->id)
            ->count();

        return view('appointments.status', compact('appointment', 'queueNumber'));
    }
}
