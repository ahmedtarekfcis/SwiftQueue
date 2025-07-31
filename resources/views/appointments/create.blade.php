<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Reservation System
    </title>
    <link rel="stylesheet" href="{{asset('assets')}}/style.css">

</head>
<body>
<div class="container">
    <div class="header">
        <h1>Line Reservation System</h1>
        <p>
            Reserve your spot in line to vote in the upcoming election — without waiting under the sun or rain.
        </p>

    </div>

    <!-- Join Queue Card -->
    <div class="join-queue-card" id="joinQueueCard">
        <div class="card-content">
            <h3>Book Election Appointment</h3>

            <div class="appointment-stats">
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-label">Appointments booked</div>
                        <div class="stat-number" id="appointmentsBooked">{{ $appointmentsBooked }}</div>

                    </div>
                    <div class="stat-item">
                        <div class="stat-label">
                            <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12,6 12,12 16,14"/>
                            </svg>
                            Estimated wait
                        </div>
                        <div class="stat-number" id="estimatedWaitTime">{{ $estimatedWait }}m</div>
                    </div>
                </div>
            </div>


            @if($bookingStatus == 'active')
                <button class="join-button" id="joinQueueBtn">
                    <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="16"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    Book Your Appointment
                </button>
            @else
                <button class="join-button" id="joinQueueBtn" disabled style="opacity: 0.5;">Booking is closed</button>
            @endif

            <p class="queue-status" id="queueStatus">
                {{ $bookingStatus == 'active' ? 'Appointment booking is active' : 'Appointment booking is closed' }}
            </p>

        </div>
    </div>
    @if($bookingStatus == 'active')
        <!-- Customer Info Form -->
        <div class="customer-info-form" id="customerInfoForm" style="display: none;">
            <div class="form-content">
                <div class="form-header">
                    <h3>
                        Hold Your Place Without the Wait
                    </h3>
                    <p>
                        Skip standing around. Just check in, save your spot, and relax nearby until it’s
                        your turn to vote.
                    </p>
                </div>

                <form id="appointmentForm" action="{{ route('appointments.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="+1 (555) 123-4567" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="appointment_date">Election Date</label>
                            <select id="appointment_date" name="appointment_date" required>
                                <option value="">Select election date</option>
                                <option value="2025-08-01">August 1, 2025</option>
                                <option value="2025-08-02">August 2, 2025</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="time_slot">Time Slot</label>
                            <select id="time_slot" name="time_slot" required>
                                <option value="">Select time slot</option>
                                <option value="09:00–10:00">09:00–10:00</option>
                                <option value="10:00–11:00">10:00–11:00</option>
                                <option value="11:00–12:00">11:00–12:00</option>
                                <option value="12:00–13:00">12:00–13:00</option>
                                <option value="13:00–14:00">13:00–14:00</option>
                                <option value="14:00–15:00">14:00–15:00</option>
                                <option value="16:00–17:00">16:00–17:00</option>
                                <option value="17:00–18:00">17:00–18:00</option>
                                <option value="18:00–19:00">18:00–19:00</option>
                                <option value="19:00–20:00">19:00–20:00</option>
                                <option value="20:00–21:00">20:00–21:00</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="cancel-button" id="cancelFormBtn">Cancel</button>
                        <button type="submit" class="submit-button" id="submitFormBtn">Book Appointment</button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- Queue Display -->
    <div class="queue-display">
        <div class="queue-content">
            <div class="now-serving-section">
                <h2>Now Serving</h2>

                @forelse ($currentlyServing as $serving)
                    <div class="serving-number">
                        {{ isset($serving) ? 'A' . str_pad($serving->queue_number, 3, '0', STR_PAD_LEFT) : 'A000' }}
                        {{--                                    <form method="POST" action="{{ route('admin.appointments.markAsDone', $serving->id) }}" style="display:inline;">--}}
                        {{--                                        @csrf--}}
                        {{--                                        <button class="admin-btn small-btn">✔ Done</button>--}}
                        {{--                                    </form>--}}
                        {{--                                    <form method="POST" action="{{ route('admin.appointments.returnPrevious', $serving->id) }}" style="display:inline;">--}}
                        {{--                                        @csrf--}}
                        {{--                                        <button class="admin-btn small-btn red-btn">⏪ Return</button>--}}
                        {{--                                    </form>--}}
                    </div>
                @empty
                    <div>---</div>
                @endforelse

            </div>

            <div class="queue-stats">
                <div class="stat-card">
                    <div class="stat-header">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>In Queue</span>
                    </div>
                    <div class="stat-value" id="totalInQueue">{{ $waitingCount }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                        <span>Est. Wait</span>
                    </div>
                    <div class="stat-value" id="estimatedWait">{{ $estimatedWait }}m</div>
                </div>
            </div>
        </div>
    </div>


</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const joinButton = document.getElementById('joinQueueBtn');
        const form = document.getElementById('customerInfoForm');
        const card = document.getElementById('joinQueueCard');
        const cancelButton = document.getElementById('cancelFormBtn');

        joinButton.addEventListener('click', function () {
            card.style.display = 'none';
            form.style.display = 'block';
        });

        cancelButton.addEventListener('click', function (e) {
            e.preventDefault(); // عشان ميعملش reset مباشر
            form.style.display = 'none';
            card.style.display = 'block';
        });
    });
</script>

</body>
</html>
