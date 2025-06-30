<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ralph City Apartment</title>
    


    <!-- Example: Tailwind + universal CSS -->
   
    <!-- Add these to your layout or this page -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://kit.fontawesome.com/ff47068b03.js" crossorigin="anonymous"></script>
<script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/universal.css'])
</head>
<body class="font-sans antialiased bg-gray-100">

    @include('navbar')
    <!-- Our fixed navbar is now included. Occupies top 4rem (h-16). -->
  
    <!-- Page heading in a black background with white text, centered -->
    <header class="text-center py-4 mt-20">
        <!-- We push content down by 4rem to avoid overlap with the fixed navbar. -->
        
    </header>

    <!-- Notifications area below heading but above main content -->
    <div id="notificationArea" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-4">
        @if(session('success'))
            <div class="alert bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <ul>
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        @yield('content')
    </main>

    <!-- Optional script for auto-hiding notifications after 10s -->
    <script>
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => el.style.display = 'none');
        }, 10000);
    </script>
</body>
</html>
