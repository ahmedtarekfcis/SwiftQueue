<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentStatusMail;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

            return view('appointments.status', [
                'appointment' => $existing,
                'queueNumber' => $existing->queue_number
            ]);
        }

        $lastQueue = \App\Models\Appointment::orderByDesc('queue_number')->value('queue_number') ?? 0;

        $validated['queue_number'] = $lastQueue + 1;

        $appointment = Appointment::create($validated);
        // $appointment->generateQrCode($appointment);
        Mail::to($appointment->email)->send(new AppointmentStatusMail($appointment));

        return view('appointments.confirmation', [
            'appointment' => $appointment,
            'queueNumber' => $appointment->queue_number,
        ]);
    }

    public function status($queueNumber)
    {
        // إزالة أي حروف من بداية الرقم (مثلاً A005 → 005)
        $numericQueueNumber = preg_replace('/\D/', '', $queueNumber);

        // نحاول نجيب الحجز برقم الانتظار (queue_number)
        $appointment = Appointment::where('queue_number', $numericQueueNumber)->first();

        if (!$appointment) {
            return redirect()->back()->withErrors(['message' => 'No appointment found with the provided queue number.']);
        }
        $appointment->generateQrCode();

        // نحسب رقم الانتظار بالنسبة لباقي الحجوزات بنفس التاريخ والتايم سلوت
        $positionInQueue = Appointment::where('appointment_date', $appointment->appointment_date)
            ->where('time_slot', $appointment->time_slot)
            ->where('id', '<=', $appointment->id)
            ->count();

        return view('appointments.status', [
            'appointment' => $appointment,
            'queueNumber' => $positionInQueue
        ]);
    }


}
