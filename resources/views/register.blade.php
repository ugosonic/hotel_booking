{{-- resources/views/register.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register Page</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required autofocus>
        </div>

        <!-- Email -->
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <!-- Password -->
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit">Register</button>
    </form>
</body>
</html>
