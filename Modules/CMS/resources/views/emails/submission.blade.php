<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd; }
        .field { margin-bottom: 10px; }
        .label { font-weight: bold; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Submission: {{ $submission->form->getTranslation('title', 'en') }}</h2>
            <p><strong>Date:</strong> {{ $submission->created_at->format('F d, Y h:i A') }}</p>
        </div>

        <div class="content">
            @foreach($submission->data as $key => $value)
                <div class="field">
                    <span class="label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                    <span>
                        @if(is_array($value))
                            {{ implode(', ', $value) }}
                        @elseif(filter_var($value, FILTER_VALIDATE_URL))
                             <a href="{{ asset('storage/'.$value) }}" target="_blank">View File</a>
                        @else
                            {{ $value }}
                        @endif
                    </span>
                </div>
            @endforeach
        </div>

        <div class="footer">
            <p>Sent from {{ config('app.name') }}</p>
            <p>IP: {{ $submission->ip_address }}</p>
        </div>
    </div>
</body>
</html>
