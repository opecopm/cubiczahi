<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        {!! $form->auto_responder_template['body'] ?? '<p>Thank you for contacting us. We have received your submission and will get back to you shortly.</p>' !!}
        
        <br>
        <p>Regards,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
