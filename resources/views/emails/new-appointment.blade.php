<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Appointment Reminder</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f7fafc; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
        .header { background: #4f46e5; padding: 25px; text-align: center; }
        .header h1 { color: white; margin: 0; font-weight: 600; }
        .content { padding: 30px; }
        .pet-info { display: flex; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; }
        .pet-image { width: 80px; height: 80px; border-radius: 10px; object-fit: cover; margin-right: 15px; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 25px 0; }
        .detail-card { background: #f8fafc; border-radius: 8px; padding: 15px; }
        .detail-label { font-size: 12px; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
        .detail-value { font-weight: 500; font-size: 16px; color: #1e293b; }
        .btn-primary { display: inline-block; background-color: #1e293b; color: #e2e8f0; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: 500; margin-top: 10px; }        .footer { text-align: center; padding: 20px; color: #64748b; font-size: 12px; }
        .footer { text-align: center; padding: 20px; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>New Appointment Reminder</h1>
            </div>

            <div class="content">
                <div class="pet-info">
                    @if($appointment->pet->profile_image)
                    <img src="{{ asset('storage/'.$appointment->pet->profile_image) }}"
                         alt="{{ $appointment->pet->name }}" class="pet-image">
                    @endif
                    <div>
                        <h2 style="margin: 0 0 5px;">{{ $appointment->pet->name }}</h2>
                        <p style="margin: 0; color: #64748b;">
                            {{ $appointment->pet->species }} • {{ $appointment->pet->breed }}
                        </p>
                    </div>
                </div>

                <div class="details-grid">
                    <div class="detail-card">
                        <div class="detail-label">Date & Time</div>
                        <div class="detail-value">{{ $appointmentDate }}</div>
                    </div>
                    <div class="detail-card">
                        <div class="detail-label">Duration</div>
                        <div class="detail-value">{{ $appointment->duration_minutes }} minutes</div>
                    </div>
                    <div class="detail-card">
                        <div class="detail-label">Veterinarian</div>
                        <div class="detail-value">{{ $appointment->veterinarian->user->name }}</div>
                    </div>
                    <div class="detail-card">
                        <div class="detail-label">Appointment Type</div>
                        <div class="detail-value">{{ ucfirst($appointment->type) }}</div>
                    </div>
                </div>

                <div style="margin: 25px 0;">
                    <div class="detail-label">Reason</div>
                    <div class="detail-value">{{ $appointment->reason }}</div>
                </div>

                <a href="{{ route('appointments.show', $appointment) }}" class="btn-primary" style="color: #e2e8f0;">
                    View Appointment Details
                </a>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated reminder. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
