<!-- resources/views/emails/payment_success.blade.php -->
<!DOCTYPE html>
<html>
<head>
  <title>Payment Success</title>
</head>
<body>
  <h1>Dear 
    @if($booking->guest_name)
      {{ $booking->guest_name }}
    @elseif($booking->client)
      {{ $booking->client->name }}
    @else
      Valued Customer
    @endif,
  </h1>

  <p>We have successfully received your payment for Booking #{{ $booking->id }}.</p>
  <p>Reference: <strong>{{ $payment->reference }}</strong></p>
  <p>Thank you for choosing us!</p>

  <p>Regards,<br>
     Your Hotel Team
  </p>
</body>
</html>
