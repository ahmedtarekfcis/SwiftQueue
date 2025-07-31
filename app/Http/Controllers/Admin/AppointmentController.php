<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\SystemSetting;

class AppointmentController extends Controller
{
    public function queue()
    {
        // المواعيد الحالية التي يتم خدمتها الآن
        $currentlyServing = Appointment::where('is_currently_serving', true)->get();

        // أقرب موعد في الانتظار (اللي عليه الدور الجاي)
        $next = Appointment::where('status', 'waiting')
            ->whereNotNull('queue_number')
            ->orderBy('queue_number')
            ->first();

        // عدد المنتظرين حاليًا
        $waitingCount = Appointment::where('status', 'waiting')->count();

        // جميع المواعيد المنتظرة مرتبة حسب رقم الطابور
        $appointmentsInQueue = Appointment::where('status', 'waiting')
            ->orderBy('queue_number')
            ->get();

        // حالة الحجز مفعّل أو موقوف
        $bookingStatus = \App\Models\SystemSetting::getValue('appointment_booking_status', 'active');

        return view('admin.queue', compact(
            'currentlyServing',
            'next',
            'waitingCount',
            'appointmentsInQueue',
            'bookingStatus'
        ));
    }


    public function callNext()
    {
        $next = Appointment::where('status', 'waiting')
            ->whereNotNull('queue_number')
            ->orderBy('queue_number')
            ->first();

        if ($next) {
            $next->update([
                'status' => 'called',
                'is_currently_serving' => true
            ]);

            return redirect()->back()->with('success', 'تم مناداة العميل التالي.');
        }

        return redirect()->back()->with('error', 'لا يوجد عملاء في قائمة الانتظار.');
    }


    public function returnPrevious()
    {
        // نجيب آخر واحد عليه الدور حاليًا
        $lastCalled = Appointment::where('is_currently_serving', true)
            ->orderByDesc('updated_at') // نفترض إن اللي بدأناه خدمته آخر واحد اتحرك حالته
            ->first();

        if ($lastCalled) {
            $lastCalled->update([
                'status' => 'waiting',
                'is_currently_serving' => false
            ]);

            return redirect()->back()->with('success', 'تمت إعادة العميل إلى قائمة الانتظار.');
        }

        return redirect()->back()->with('error', 'لا يوجد موعد حالي لإرجاعه.');
    }


    public function pauseBooking()
    {
        SystemSetting::setValue('appointment_booking_status', 'paused');
        return redirect()->back()->with('success', 'تم إيقاف الحجز');
    }

    public function resumeBooking()
    {
        SystemSetting::setValue('appointment_booking_status', 'active');
        return redirect()->back()->with('success', 'تم تفعيل الحجز');
    }


    public function markAsDone($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('is_currently_serving', true)
            ->first();

        if ($appointment) {
            $appointment->update([
                'status' => 'done',
                'is_currently_serving' => false
            ]);
            return redirect()->back()->with('success', 'تم إنهاء الموعد');
        }

        return redirect()->back()->with('error', 'لم يتم العثور على الموعد المطلوب');
    }
}
