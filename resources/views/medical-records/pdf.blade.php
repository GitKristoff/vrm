{{-- filepath: c:\xampp\htdocs\vrmas\resources\views\medical-records\pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $pet->name }} Medical Records</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #333; font-size: 13px; }
        .header { text-align: center; margin-bottom: 18px; }
        .logo { height: 48px; margin-bottom: 8px; }
        .clinic-info { font-size: 12px; color: #555; margin-bottom: 8px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 15px; font-weight: bold; color: #2c3e50; margin-bottom: 6px; border-bottom: 1px solid #eee; }
        .pet-details, .owner-details { font-size: 13px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px;}
        th, td { border: 1px solid #e1e1e1; padding: 6px; }
        th { background: #f5f7fa; font-weight: bold; font-size: 12px; }
        td { font-size: 12px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { text-align: right; font-size: 11px; color: #888; margin-top: 24px; }
        .record-section { margin-bottom: 14px; padding: 10px; border: 1px solid #e1e1e1; border-radius: 5px; background: #f8fafc; }
        .record-title { font-size: 13px; font-weight: bold; color: #34495e; margin-bottom: 5px; }
        .record-meta { font-size: 12px; color: #666; margin-bottom: 5px; }
        .med-table th, .med-table td { font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        {{-- <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Clinic Logo"> --}}
        <div class="clinic-info">
            <strong>Bulan Veterinary Records Management System</strong><br>
            123 Main St, City, Country<br>
            Phone: (123) 456-7890 | Email: info@vetclinic.com
        </div>
        <h2 style="font-size:17px;">Pet Medical Records Report</h2>
    </div>

    <div class="section">
        <div class="section-title">Pet & Owner Details</div>
        <div class="pet-details">
            <strong>Pet Name:</strong> {{ $pet->name }}<br>
            <strong>Species:</strong> {{ $pet->species }}<br>
            <strong>Breed:</strong> {{ $pet->breed }}<br>
            <strong>Age:</strong> {{ $pet->age }} years
        </div>
        <div class="owner-details">
            <strong>Owner:</strong> {{ $pet->owner->user->name ?? 'N/A' }}<br>
            <strong>Contact:</strong> {{ $pet->owner->user->email ?? 'N/A' }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Appointment Type Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Records</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $types = ['vaccination' => 'Vaccination', 'dental' => 'Dental Care', 'checkup' => 'Check-up', 'surgery' => 'Surgery', 'other' => 'Other'];
                @endphp
                @foreach($types as $key => $label)
                    @php
                        $count = $pet->appointments()->where('type', $key)->count();
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $count > 0 ? $count . ' record(s)' : 'None recorded' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Medical Records</div>
        @forelse($medicalRecords as $record)
            <div class="record-section">
                <div class="record-title">
                    {{ $record->record_date->format('M d, Y') }} &mdash; {{ ucfirst($record->appointment->type ?? 'N/A') }}
                </div>
                <div class="record-meta">
                    <strong>Veterinarian:</strong> Dr. {{ $record->veterinarian->user->name ?? 'N/A' }}
                </div>
                <div>
                    <strong>Subjective Notes:</strong> {{ $record->subjective_notes }}<br>
                    <strong>Objective Findings:</strong> {{ $record->objective_notes }}<br>
                    <strong>Assessment:</strong> {{ $record->assessment }}<br>
                    <strong>Plan:</strong> {{ $record->plan }}
                </div>
                @if($record->medications && $record->medications->count())
                    <div style="margin-top:8px;">
                        <strong>Medications:</strong>
                        <table class="med-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($record->medications as $med)
                                    <tr>
                                        <td>{{ $med->name }}</td>
                                        <td>{{ $med->dosage }}</td>
                                        <td>{{ $med->frequency }}</td>
                                        <td>{{ $med->start_date->format('M d, Y') }}</td>
                                        <td>{{ $med->end_date->format('M d, Y') }}</td>
                                        <td>{{ $med->purpose }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @empty
            <p>No medical records found for this pet.</p>
        @endforelse
    </div>

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i') }}
    </div>
</body>
</html>
