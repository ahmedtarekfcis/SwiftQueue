<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class Appointment extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'email', 'appointment_date', 'time_slot', 'status', 'queue_number',
        'is_currently_serving',
        'qr_code'
    ];

    public function generateQrCode()
    {
        $data = route('appointments.status', ['id' => $this->queue_number]);

        $filename = 'qr_' . $this->id . '.png';
        $path = 'qrcodes/' . $filename;

        // توليد QR وتخزينه في public/qrcodes
        $qrImage = QrCode::format('png')->size(300)->generate($data);
        Storage::disk('public')->put($path, $qrImage);

        // تحديث الحقل في الداتا بيز
        $this->update(['qr_code' => $path]);
    }
}
