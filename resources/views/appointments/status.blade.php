<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Appointment Status</title>
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Your Existing Appointment</h1>
        <p>You already have a booked appointment. Here are the details:</p>
    </div>

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
                        <div class="stat-label">Phone</div>
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

                            A{{ str_pad($appointment->queue_number, 3, '0', STR_PAD_LEFT) }} - {{ $appointment->full_name }}

                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Status</div>
                        <div class="stat-number">{{ ucfirst($appointment->status) }}</div>
                    </div>
                </div>
            </div>

            <p class="queue-status">
                If you need to make changes, please contact the support team.
            </p>
        </div>
    </div>
</div>
</body>
</html>
