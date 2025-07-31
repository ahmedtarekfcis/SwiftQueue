<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .small-btn {
            font-size: 12px;
            padding: 4px 8px;
            margin-left: 4px;
        }

        .red-btn {
            background-color: #e74c3c;
            color: white;
        }

        .serving-entry {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <div class="header">
        <h1>Election Admin Dashboard</h1>
        <p>Manage election appointments and call voters</p>
    </div>

    <div class="admin-grid">
        <!-- Queue Display -->
        <div class="queue-display-section">
            <div class="queue-display">
                <div class="queue-content">
                    <div class="now-serving-section">
                        <h2>Now Serving</h2>
                        <div class="serving-number" id="adminCurrentlyServing">
                            @forelse ($currentlyServing as $serving)
                                <div class="serving-entry">
                                    A{{ str_pad($serving->queue_number, 3, '0', STR_PAD_LEFT) }} - {{ $serving->full_name }}
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
                    </div>

                    <div class="queue-stats">
                        <div class="stat-card">
                            <div class="stat-header">
                                <span>In Queue</span>
                            </div>
                            <div class="stat-value" id="adminTotalInQueue">{{ $waitingCount }}</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <span>Est. Wait</span>
                            </div>
                            <div class="stat-value" id="adminEstimatedWait">{{ $waitingCount * 2 }}m</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Panel -->
        <div class="admin-panel">
            <h3>Admin Controls</h3>

            <div class="admin-buttons" style="display: flex; gap: 10px; flex-wrap: wrap;">

                <!-- Call Next -->
                <form method="POST" action="{{ route('admin.appointments.callNext') }}">
                    @csrf
                    <button type="submit" class="admin-btn call-next-btn"
                            style="background-color: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px;">
                        Call Next Customer
                    </button>
                </form>

                <!-- Return Previous -->
                <form method="POST" action="{{ route('admin.appointments.returnPrevious') }}">
                    @csrf
                    <button type="submit" class="admin-btn reset-btn"
                            style="background-color: #e67e22; color: white; border: none; padding: 10px 20px; border-radius: 5px;">
                        Return Previous
                    </button>
                </form>

                <!-- Toggle Queue Status -->
                @if ($bookingStatus === 'active')
                    <form id="pause-form" action="{{ route('admin.appointments.pauseBooking') }}" method="POST">
                        @csrf
                        <button type="submit" class="admin-btn toggle-btn"
                                style="background-color: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 5px;">
                            Pause Queue
                        </button>
                    </form>
                @else
                    <form id="resume-form" action="{{ route('admin.appointments.resumeBooking') }}" method="POST">
                        @csrf
                        <button type="submit" class="admin-btn toggle-btn"
                                style="background-color: #2ecc71; color: white; border: none; padding: 10px 20px; border-radius: 5px;">
                            Resume Queue
                        </button>
                    </form>
                @endif

            </div>

            <div class="admin-status">
                <div class="status-card">
                    <div class="status-label">Now Serving:</div>
                    <div class="status-value" id="statusCurrentlyServing">
                        @forelse ($currentlyServing as $serving)
                            A{{ str_pad($serving->queue_number, 3, '0', STR_PAD_LEFT) }}
                        @empty
                            ---
                        @endforelse
                    </div>
                </div>

                <div class="status-card">
                    <div class="status-label">Queue Status:</div>
                    <div class="status-value {{ $bookingStatus == 'active' ? 'active' : '' }}" id="statusQueueState">
                        {{ ucfirst($bookingStatus) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Overview -->
    <div class="appointment-overview">
        <h2>Appointments Overview</h2>
        <div class="appointments-grid" id="appointmentsGrid">
            @foreach($appointmentsInQueue as $index => $appointment)
                <div class="appointment-card">
                    <div class="appointment-header">
                        <div class="ticket-badge">A{{ str_pad($appointment->queue_number, 3, '0', STR_PAD_LEFT) }}</div>
                        <div class="status-badges">
                            <span class="status-badge {{ $appointment->status === 'called' ? 'called' : 'active' }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                            <span class="position-text">Pos: {{ $index + 1 }}</span>
                        </div>
                    </div>

                    <div class="appointment-details">
                        <div class="detail-row">
                            <span class="detail-text">{{ $appointment->full_name }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-text">{{ $appointment->phone }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-text">{{ $appointment->email }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-text">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M j') }} at {{ $appointment->time_slot }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

</body>
</html>
