@if(auth()->check())
<!-- For mobile toggling, we can use Alpine.js or plain JS. Example with Alpine: -->
<div x-data="{ openSidebar: false }">

    <!-- This is the hamburger icon: visible on small screens, hidden on md+ -->
    <button class="md:hidden p-2 focus:outline-none" 
            @click="openSidebar = !openSidebar">
        <!-- Icon can be FontAwesome or Heroicons. Example: -->
        <svg class="h-6 w-6 text-gray-800" fill="none" stroke="currentColor" 
             viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" 
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Sidebar container -->
    <div :class="openSidebar ? 'block' : 'hidden'"
         class="fixed top-16 left-0 w-64 h-screen bg-white shadow-md overflow-y-auto 
                md:block z-20">
      
      <ul class="space-y-2 py-4 px-2">
          <!-- Account Settings -->
          <li>
              <a href="{{ route('staff.settings.account') }}"
                 class="flex items-center p-2 text-gray-700 
                        hover:bg-gray-100 rounded transition">
                  <svg class="h-5 w-5 mr-2 text-purple-600" fill="none"
                       stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path d="M..."></path>
                  </svg>
                  Account Settings
              </a>
          </li>

          <!-- Notifications -->
          <li>
              <a href="{{ route('notifications.index') }}"
                 class="flex items-center p-2 text-gray-700 
                        hover:bg-gray-100 rounded transition">
                  <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                       stroke-width="2" viewBox="0 0 24 24">
                      <path d="M..."></path>
                  </svg>
                  Notifications
              </a>
          </li>

          <!-- Staff-Only: Site Settings -->
          @if(auth()->user()->isStaff())
           
            <li>
                <a href="{{ route('staff.refund.settings') }}"
                   class="flex items-center p-2 text-gray-700 
                          hover:bg-gray-100 rounded transition">
                    <svg class="h-5 w-5 mr-2 text-green-600" fill="none"
                         stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M..."></path>
                    </svg>
                    Refund Settings
                </a>
            </li>
          @endif
      </ul>
    </div>
</div>
@endif
