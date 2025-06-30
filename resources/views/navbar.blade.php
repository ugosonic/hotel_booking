<!-- resources/views/navbar.blade.php -->
<nav class="bg-white border-b border-gray-200 fixed top-0 w-full z-50 overflow-x-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="text-2xl font-bold text-purple-600">
                    Ralph<span class="text-pink-500">City</span>
                    <p class="text-sm text-gray-700 ml-1">Apartments</p>
                </a>
            </div>

            <!-- Links for larger screens -->
            <div class="hidden sm:flex sm:space-x-4 items-center">
                <a href="/" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
                
                @auth
                    @if(auth()->user()->isStaff())
                        <a href="/staff/dashboard" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                            <i class="fas fa-user-tie mr-2"></i> Staff Dashboard
                        </a>
                    @else
                        <a href="/client/dashboard" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                            <i class="fas fa-user mr-2"></i> Client Dashboard
                        </a>
                    @endif

                    <a href="{{ route('book.apartment') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                        <i class="fas fa-bed mr-2"></i> Book Apartment
                    </a>
                    <a href="{{ route('bookings.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i> View Bookings
                    </a>
                    <a href="{{ route('client.settings') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                        <i class="fas fa-cog mr-2"></i> Settings
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline-block">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                    <a href="{{ route('register_client') }}" 
                       class="bg-purple-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-purple-700 flex items-center">
                        <i class="fas fa-user-plus mr-2"></i> Sign Up
                    </a>
                @endauth
            </div>

            <!-- Hamburger Menu for small screens -->
            <div class="flex items-center sm:hidden">
                <button id="menu-toggle" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" 
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                              stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="sm:hidden hidden bg-white border-t border-gray-200">
        <a href="/" class="block px-4 py-2 text-gray-700 hover:text-purple-600 flex items-center">
            <i class="fas fa-home mr-2"></i> Home
        </a>
        <a href="#about" class="block px-4 py-2 text-gray-700 hover:text-purple-600 flex items-center">
            <i class="fas fa-info-circle mr-2"></i> About
        </a>
       
        @auth
            @if(auth()->user()->isStaff())
                <a href="/staff/dashboard" class="block px-4 py-2 text-gray-700 hover:text-purple-600 flex items-center">
                    <i class="fas fa-user-tie mr-2"></i> Staff Dashboard
                </a>
            @else
                <a href="/client/dashboard" class="block px-4 py-2 text-gray-700 hover:text-purple-600 flex items-center">
                    <i class="fas fa-user mr-2"></i> Client Dashboard
                </a>
            @endif

            <a href="{{ route('book.apartment') }}" class="block px-4 py-2 text-gray-700 hover:text-purple-600 flex items-center">
                <i class="fas fa-bed mr-2"></i> Book Apartment
            </a>
            <a href="{{ route('bookings.index') }}" class="block px-4 py-2 text-gray-700 hover:text-purple-600 flex items-center">
                <i class="fas fa-calendar-alt mr-2"></i> View Bookings
            </a>

            <form method="POST" action="{{ route('logout') }}" class="block px-4 py-2">
                @csrf
                <button type="submit" class="text-gray-700 hover:text-red-600 flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-700 hover:text-purple-600 flex items-center">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </a>
            <a href="{{ route('register_client') }}" 
               class="block px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Sign Up
            </a>
        @endauth
    </div>
</nav>
<script src="https://kit.fontawesome.com/ff47068b03.js" crossorigin="anonymous"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    });
</script>


