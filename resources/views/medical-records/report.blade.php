<!DOCTYPE html>
<html>
<head>
    <title>Medical Record Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 15px; }
        .section-title { font-weight: bold; border-bottom: 1px solid #000; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Medical Record Report</h1>
        <p>Pet: {{ $medicalRecord->pet->name }} | Date: {{ $medicalRecord->record_date->format('M d, Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Veterinarian</div>
        <p>{{ $medicalRecord->veterinarian->user->name }}</p>
    </div>

    <div class="section">
        <div class="section-title">Subjective Notes</div>
        <p>{{ $medicalRecord->subjective_notes }}</p>
    </div>

    <div class="section">
        <div class="section-title">Objective Findings</div>
        <p>{{ $medicalRecord->objective_notes }}</p>
    </div>

    <div class="section">
        <div class="section-title">Assessment</div>
        <p>{{ $medicalRecord->assessment }}</p>
    </div>

    <div class="section">
        <div class="section-title">Plan</div>
        <p>{{ $medicalRecord->plan }}</p>
    </div>

    @if($medicalRecord->medications->count())
    <div class="section">
        <div class="section-title">Medications</div>
        <table>
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
                @foreach($medicalRecord->medications as $med)
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

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
    </div>
</body>
</html>
