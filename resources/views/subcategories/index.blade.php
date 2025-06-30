@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">

    <h2 class="text-2xl font-bold mb-4">Subcategories (Apartment Units)</h2>

    @if(session('success'))
      <div class="bg-green-100 text-green-700 p-3 mb-4">
        {{ session('success') }}
      </div>
    @endif

    @if($subcats->count())
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($subcats as $sc)
          <div class="border p-4 rounded shadow bg-white">
            <h3 class="text-xl font-semibold mb-2">{{ $sc->name }}</h3>
            <p class="mb-2">Price: ₦{{ number_format($sc->price,2) }}</p>

            <!-- The slider container -->
            @if($sc->images && $sc->images->count())
              <div class="relative w-64 h-40 overflow-hidden border"
                   id="slider-{{ $sc->id }}">
                <!-- Each image in a "slide" -->
                @foreach($sc->images as $index => $img)
                  <img src="{{ asset($img->image_path) }}"
                       class="absolute w-full h-full object-cover transition-all duration-300"
                       style="display: {{ $index === 0 ? 'block' : 'none' }};"
                       data-index="{{ $index }}">
                @endforeach

                <!-- If more than 1 image, show arrows -->
                @if($sc->images->count() > 1)
                  <!-- Prev button -->
                  <button class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-2 py-1 rounded"
                          onclick="prevSlide({{ $sc->id }}, {{ $sc->images->count() }})">
                    ‹
                  </button>
                  <!-- Next button -->
                  <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-2 py-1 rounded"
                          onclick="nextSlide({{ $sc->id }}, {{ $sc->images->count() }})">
                    ›
                  </button>
                @endif
              </div>
            @endif

            <!-- Show features, etc. ... -->
              <!-- Features list with icons. Only show if feature is true -->
            <div class="mt-2 mb-2 text-sm text-gray-700">
              <ul class="space-y-1">
                @if($sc->has_rooms && $sc->num_rooms)
                  <li>
                    <i class="fas fa-bed text-blue-600"></i>
                    <strong>Rooms:</strong> {{ $sc->num_rooms }}
                  </li>
                @endif

                @if($sc->has_toilets && $sc->num_toilets)
                  <li>
                    <i class="fas fa-toilet text-blue-600"></i>
                    <strong>Toilets:</strong> {{ $sc->num_toilets }}
                  </li>
                @endif

                @if($sc->has_sittingroom && $sc->num_sittingrooms)
                  <li>
                    <i class="fas fa-couch text-blue-600"></i>
                    <strong>Sitting Rooms:</strong> {{ $sc->num_sittingrooms }}
                  </li>
                @endif

                @if($sc->has_kitchen && $sc->num_kitchens)
                  <li>
                    <i class="fas fa-utensils text-blue-600"></i>
                    <strong>Kitchens:</strong> {{ $sc->num_kitchens }}
                  </li>
                @endif

                @if($sc->has_balcony && $sc->num_balconies)
                  <li>
                    <i class="fas fa-warehouse text-blue-600"></i>
                    <strong>Balconies:</strong> {{ $sc->num_balconies }}
                  </li>
                @endif

                @if($sc->free_wifi)
                  <li>
                    <i class="fas fa-wifi text-purple-700"></i>
                    Free WiFi
                  </li>
                @endif

                @if($sc->water)
                  <li>
                    <i class="fas fa-tint text-blue-600"></i>
                    Water Available
                  </li>
                @endif

                @if($sc->electricity)
                  <li>
                    <i class="fas fa-bolt text-yellow-500"></i>
                    24/7 Electricity
                  </li>
                @endif
              </ul>
            </div>

            

            <div class="flex space-x-2 mt-4">
              <!-- Link to edit subcat -->
              <a href="{{ route('subcategories.edit',$sc->id) }}"
                 class="bg-yellow-500 text-white px-2 py-1 rounded">
                Edit
              </a>

              <!-- Link to delete subcat -->
              <form action="{{ route('subcategories.destroy',$sc->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="bg-red-600 text-white px-2 py-1 rounded"
                        onclick="return confirm('Delete this subcategory?')">
                  Delete
                </button>
              </form>

              <!-- Availability (if you have a route) -->
              <a href="{{ route('subcategories.availability.index',$sc->id) }}"
                 class="bg-green-600 text-white px-2 py-1 rounded">
                Availability
              </a>
            </div>
        </div>
        @endforeach
      </div>

     

      <div class="mt-4">
        {{ $subcats->links() }}
      </div>
    @else
      <p>No subcategories found.</p>
    @endif

</div>

<!-- Font Awesome for any icons you might use -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
function nextSlide(subCatId, totalImages) {
  const sliderEl = document.getElementById('slider-' + subCatId);
  if(!sliderEl) return;
  const images = sliderEl.querySelectorAll('img[data-index]');
  let currentIndex = -1;
  // find which one is currently displayed
  images.forEach((img, idx) => {
    if(img.style.display !== 'none') {
      currentIndex = idx;
    }
  });
  if(currentIndex < 0) return; // fallback

  // Hide current
  images[currentIndex].style.display = 'none';
  // Next index
  let newIndex = currentIndex + 1;
  if(newIndex >= totalImages) newIndex = 0; // wrap
  images[newIndex].style.display = 'block';
}

function prevSlide(subCatId, totalImages) {
  const sliderEl = document.getElementById('slider-' + subCatId);
  if(!sliderEl) return;
  const images = sliderEl.querySelectorAll('img[data-index]');
  let currentIndex = -1;
  images.forEach((img, idx) => {
    if(img.style.display !== 'none') {
      currentIndex = idx;
    }
  });
  if(currentIndex < 0) return;

  images[currentIndex].style.display = 'none';
  let newIndex = currentIndex - 1;
  if(newIndex < 0) newIndex = totalImages - 1; // wrap to last
  images[newIndex].style.display = 'block';
}
</script>
@endsection
