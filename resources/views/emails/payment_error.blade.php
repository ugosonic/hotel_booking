<!-- resources/views/emails/payment_error.blade.php -->
<!DOCTYPE html>
<html>
<head>
  <title>Payment Error</title>
</head>
<body>
  <h1>Hello 
    @if($booking->guest_name)
      {{ $booking->guest_name }}
    @elseif($booking->client)
      {{ $booking->client->name }}
    @else
      Valued Customer
    @endif,
  </h1>

  <p>We encountered an issue with your payment for Booking #{{ $booking->id }}.</p>
  <p>Reason: {{ $reason }}</p>
  <p>Please retry or contact support.</p>

  <p>Regards,<br>
     Your Hotel Team
  </p>
</body>
</html>
S