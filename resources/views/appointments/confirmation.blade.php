<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Appointment Confirmed</h1>
        <p>Thank you! Your appointment has been successfully booked.</p>
    </div>

    <!-- Confirmation Card -->
    <div class="join-queue-card">
        <div class="card-content">
            <h3>Appointment Details</h3>

            <div class="appointment-stats">
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-label">Full Name</div>
                        <div class="stat-number">{{ $appointment->full_name }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Phone Number</div>
                        <div class="stat-number">{{ $appointment->phone }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Email</div>
                        <div class="stat-number">{{ $appointment->email }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Date</div>
                        <div class="stat-number">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Time Slot</div>
                        <div class="stat-number">{{ $appointment->time_slot }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Queue Number</div>
                        <div class="stat-number">
                            A{{ str_pad($queueNumber, 3, '0', STR_PAD_LEFT) }} - {{ $appointment->full_name }}
                        </div>
                    </div>
                </div>
            </div>

            <p class="queue-status">
                Please arrive 10 minutes early. Bring your ID and confirmation details.
            </p>

            <a href="{{ url('/') }}" class="join-button" style="text-align: center;">
                Back to Home
            </a>
        </div>
    </div>
</div>
</body>
</html>
