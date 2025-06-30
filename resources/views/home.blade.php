<!DOCTYPE html>
<html lang="en" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RalphCity Apartment</title>

  <!-- Tailwind CSS -->
  {{-- If you already have Tailwind via Vite or Mix, remove this CDN link and use your compiled app.css --}}
  @vite(['resources/css/app.css', 'resources/css/universal.css', 'resources/js/app.js'])

  @include('navbar')

  <!-- Typed.js for typewriter effect -->
  <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>

  <style>
    /* Fade image animation (Hero Section Right Image) */
    @keyframes zoomInOut {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05); /* slight zoom in */
  }
  100% {
    transform: scale(1); /* zoom out */
  }
}

#heroBackground {
  position: absolute;
  inset: 0; /* replaces top, left, width, height */
  z-index: 0;

  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  filter: blur(10px);

  animation: zoomInOut 20s ease-in-out infinite;
  transition: background-image 0.5s ease, opacity 0.5s ease;
}

.fade-in-on-scroll {
  opacity: 0;
  transition: opacity 0.8s ease-out;
}

.fade-in-on-scroll.animate {
  opacity: 1;
}

.slide-left-on-scroll.animate {
  animation: slideLeft 1s ease-out forwards;
}

.slide-up-on-scroll.animate {
  animation: slideUp 1s ease-out forwards;
}

.slide-right-on-scroll.animate {
  animation: slideRight 1s ease-out forwards;
}

@keyframes slideLeft {
  from {
    opacity: 0;
    transform: translateX(-2rem);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideRight {
  from {
    opacity: 0;
    transform: translateX(2rem);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(2rem);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}


    /* Animation classes for scroll effects */
    .fade-in,
    .slide-up,
    .slide-in {
      opacity: 0;
    }
    .fade-in.animate {
      animation: fadeIn 1.5s ease-in-out forwards;
    }
    @keyframes fadeIn {
      to { opacity: 1; }
    }
    .slide-up.animate {
      animation: slideUp 1.5s ease-in-out forwards;
    }
    @keyframes slideUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .slide-in.animate {
      animation: slideIn 1.5s ease-in-out forwards;
    }
    @keyframes slideIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Custom half-curved headings */
    .half-curve-heading {
      position: relative;
      display: inline-block;
      padding: 0.5rem 1rem;
      color: #fff;
      background-color: #000;
      overflow: hidden;
    }
    .half-curve-heading::before {
      content: '';
      position: absolute;
      top: 0;
      right: 100%;
      width: 200%;
      height: 100%;
      background-color: #fff;
      clip-path: ellipse(50% 100% at 100% 50%);
    }
    .half-curve-heading span {
      color: #000;
      position: relative;
      z-index: 2;
      margin-left: 1rem;
    }

    /* Flashing text for "BOOK AN APARTMENT..." */
    @keyframes flash {
      0%, 100% { opacity: 1; }
      50% { opacity: 0; }
    }
    .flashing-text {
      animation: flash 1.5s infinite;
    }

    /* Slider for SubCategory images */
    .slider-image {
      flex-shrink: 0;
      width: 100%;
      height: 300px;
      background-size: cover;
      background-position: center;
    }
    #sliderTrack {
      display: flex;
      transition: transform 0.3s ease-in-out;
    }

    @keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(2rem);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.slide-up {
  opacity: 0;
  transform: translateY(2rem);
  transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

.slide-up.animate {
  animation: slideUp 1.5s ease-out forwards;
}
/* Slide Down (from top) */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-2rem);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.slide-down {
  opacity: 0;
  transform: translateY(-2rem);
}
.slide-down.animate {
  animation: slideDown 1.5s ease-out forwards;
}

/* Slide Left (from left) */
@keyframes slideLeft {
  from {
    opacity: 0;
    transform: translateX(-3rem);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
.slide-left {
  opacity: 0;
  transform: translateX(-3rem);
}
.slide-left.animate {
  animation: slideLeft 1.8s ease-out forwards;
}

.room-card {
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.8s ease-out;
  background: linear-gradient(to right, #f0f4ff, #fef6fb);
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.room-card.animate {
  opacity: 1 !important; /* Force visibility after animation */
}

/* Keyframes for entry + shake */
@keyframes slideShakeLeft {
  0% {
    opacity: 0;
    transform: translateX(-100px);
  }
  50% {
    opacity: 1;
    transform: translateX(10px);
  }
  70% {
    transform: translateX(-6px);
  }
  90% {
    transform: translateX(3px);
  }
  100% {
    transform: translateX(0);
  }
}

@keyframes slideShakeRight {
  0% {
    opacity: 0;
    transform: translateX(100px);
  }
  50% {
    opacity: 1;
    transform: translateX(-10px);
  }
  70% {
    transform: translateX(6px);
  }
  90% {
    transform: translateX(-3px);
  }
  100% {
    transform: translateX(0);
  }
}

@keyframes slideShakeUp {
  0% {
    opacity: 0;
    transform: translateY(-100px);
  }
  50% {
    opacity: 1;
    transform: translateY(10px);
  }
  70% {
    transform: translateY(-6px);
  }
  90% {
    transform: translateY(3px);
  }
  100% {
    transform: translateY(0);
  }
}

@keyframes slideShakeDown {
  0% {
    opacity: 0;
    transform: translateY(100px);
  }
  50% {
    opacity: 1;
    transform: translateY(-10px);
  }
  70% {
    transform: translateY(6px);
  }
  90% {
    transform: translateY(-3px);
  }
  100% {
    transform: translateY(0);
  }
}

/* Animate class triggers */
.slide-shake-left-on-scroll.animate {
  animation: slideShakeLeft 1.2s ease-out;
}
.slide-shake-right-on-scroll.animate {
  animation: slideShakeRight 1.2s ease-out;
}
.slide-shake-up-on-scroll.animate {
  animation: slideShakeUp 1.2s ease-out;
}
.slide-shake-down-on-scroll.animate {
  animation: slideShakeDown 1.2s ease-out;
}


  </style>
</head>

<body style="background: linear-gradient(to right, #f9f7f7, #e4f1f7, #f6e7f8, #eef9f2); color: #4B5563;">


 
<!-- Hero Section -->
<section class="min-h-screen flex items-center px-8 py-16 relative overflow-hidden" id="heroSection">
  <!-- Background Image Slideshow -->
  <div class="absolute inset-0 z-0 bg-cover bg-center transition-opacity duration-1000" id="heroBackground"></div>

  <!-- Dark Overlay for better text visibility -->
  <div class="absolute inset-0 bg-black opacity-50 z-0"></div>

  <!-- Foreground Content -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-12 w-full max-w-7xl mx-auto items-center relative z-10 text-white">
    <!-- Left Content -->
    <div class="fade-in">
  <!-- Welcome Heading -->
  <h1 class="slide-down opacity-0 text-4xl md:text-6xl font-extrabold leading-tight mb-6">
    Welcome to 
    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
      <span id="typedText"></span>
    </span>
  </h1>

 <!-- Description Paragraph -->
<p class="slide-left opacity-0 text-lg md:text-xl leading-relaxed mb-8 text-[#bfa75d]">
  Stay in a complete Apartment at a very low cost with 24/7 electricity, constant water supply, and Free WiFi. 
  Experience comfort, luxury, and world-class hospitality at our exquisite Apartment. Enjoy serene environments, 
  modern amenities, and top-notch customer service tailored to your comfort.
</p>


  @guest
  <a href="{{ route('register_client') }}"
     class="slide-up opacity-0 translate-y-8 inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-lg font-semibold transition duration-300 delay-500">
     Create an Account
  </a>
  @endguest
</div>

  </div>
</section>

 <!-- Search Section -->
 <section class="py-16">
    <div class="max-w-7xl mx-auto px-4">

      <!-- Search Title -->
      <h2 class="text-2xl md:text-3xl font-bold text-center search-title">
        Search For an Apartment 
      </h2>

     <!-- Add flex-wrap, reduce padding, and ensure it fits smaller screens -->
<div class="bg-white text-gray-800 rounded-full shadow-lg 
     flex flex-wrap items-center justify-between 
     px-3 py-2 
     max-w-full sm:max-w-xl md:w-3/4 
     mx-auto">

  <!-- Category Select -->
  <div class="flex items-center space-x-2 mb-2 sm:mb-0">
    <label class="font-semibold text-sm">Type</label>
    <select id="categorySelect" 
            class="bg-transparent text-sm outline-none" 
            onchange="fetchSubCategories(this.value)">
      <option value="">-- Select Apartment Category  --</option>
    </select>
  </div>

  <!-- Divider (hidden on screens <md) -->
  <div class="w-px h-6 bg-gray-300 mx-4 hidden md:block"></div>

  <!-- SubCategory Select -->
  <div class="flex items-center space-x-2 mb-2 sm:mb-0">
    <label class="font-semibold text-sm">Apartment</label>
    <select id="subCategorySelect" 
            class="bg-transparent text-sm outline-none" 
            disabled 
            onchange="onSubCategoryChosen(this.value)">
      <option value="">-- Choose Apartment --</option>
    </select>
  </div>

  <!-- Divider (hidden on screens <md) -->
  <div class="w-px h-6 bg-gray-300 mx-4 hidden md:block"></div>

 
</div>


      <!-- Flashing Text -->
      <div class="text-center mt-6">
        <h2 class="flashing-text text-center font-bold">
          Book a one bedroom Apartmemnt as Low as ₦ 15,000/Night In Enugu
        </h2>
      </div>

      <!-- Apartment Details (Hidden Initially) -->
      <div id="apartmentDetails" class="hidden mt-12 bg-white text-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex flex-col md:flex-row md:space-x-6">

          <!-- Image Slider -->
          <div class="md:w-1/2">
            <div class="relative w-full overflow-hidden rounded" id="sliderContainer">
              <div id="sliderTrack"></div>
              <button id="prevBtn" class="hidden absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-2 py-1 rounded">‹</button>
              <button id="nextBtn" class="hidden absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-2 py-1 rounded">›</button>
            </div>
          </div>

          <!-- Apartment Info -->
          <div class="md:w-1/2 mt-4 md:mt-0">
            <h3 id="apartmentName" class="text-xl font-bold mb-2"></h3>
            <p id="apartmentPrice" class="text-lg text-green-700 font-semibold mb-4"></p>

            <div id="featuresList" class="text-sm space-y-2 mb-4"></div>

            <p id="additionalInfo" class="mt-2 text-gray-700 text-sm"></p>
          </div>
        </div>

        <!-- Book Now Button -->
        @guest
          <p class="mt-4 text-center text-gray-700">Login or Register to book this apartment</p>
          <a href="{{ route('login') }}" class="block text-center bg-yellow-600 text-white px-8 py-3 rounded-md text-lg font-semibold hover:bg-purple-700 transition duration-300 mt-4">
            Book Now
          </a>
        @else
          <a href="{{ route('book.apartment') }}" class="block text-center bg-yellow-600 text-white px-8 py-3 rounded-md text-lg font-semibold hover:bg-purple-700 transition duration-300 mt-4">
            Book Now
          </a>
        @endguest

      </div>
    </div>
  </section>
 <!-- About Section -->
<section id="about" class="py-16 px-8 bg-gradient-to-r from-blue-50 via-purple-50 to-pink-50 text-gray-800">
  <div class="grid gap-8">
    <div class="max-w-6xl mx-auto shadow-md rounded-lg p-6">
      <h2 class="text-4xl font-bold mb-6 fade-in-on-scroll">About Us</h2>

      <p class="text-lg leading-relaxed mb-6 fade-in-on-scroll slide-left-on-scroll">
        Ralph City Apartments is a family-owned business located in the heart of Enugu, Nigeria. As a fully registered establishment, we provide a range of accommodation options—from elegant one-bedroom apartments to spacious two-bedroom units—designed to suit both short stays and extended holidays.
      </p>

      <p class="text-lg leading-relaxed mb-6 fade-in-on-scroll slide-up-on-scroll">
        Our properties feature uninterrupted 24/7 electricity and water supply, high-speed internet, and modern amenities that ensure a comfortable and convenient experience. Whether your visit is for business or leisure, our commitment to exceptional customer service guarantees a serene environment tailored to your every need.
      </p>

      <p class="text-lg leading-relaxed mb-6 fade-in-on-scroll slide-right-on-scroll">
        Discover a refined stay with Ralph City Apartments, where comfort meets convenience.
      </p>

      <a href="#rooms" class="text-purple-400 underline hover:text-purple-600 transition duration-300 fade-in-on-scroll">
        Learn more
      </a>
    </div>
  </div>
</section>


  <!-- Rooms Section -->
<section id="rooms" class="py-16 px-8 bg-gradient-to-r from-blue-900 to-black text-gray-300">
  <div class="max-w-6xl mx-auto shadow-md rounded-lg p-6">
    <h2 class="text-4xl font-bold mb-6 text-center">Our Suites include</h2>
    <div class="grid gap-8 md:grid-cols-3">
      
      <!-- Room 1 -->
      <div class="room-card slide-shake-left-on-scroll">
        <h3 class="text-2xl font-bold mb-4">Executive Room</h3>
        <p class="text-gray-400">A luxurious room with a king-sized bed, stunning views.</p>
      </div>

      <!-- Room 2 -->
      <div class="room-card slide-shake-up-on-scroll">
        <h3 class="text-2xl font-bold mb-4">Furnished Sitting Room</h3>
        <p class="text-gray-400">Designed for business travelers with a dedicated work area, free Wi-Fi, and premium amenities.</p>
      </div>

      <!-- Room 3 -->
      <div class="room-card slide-shake-right-on-scroll">
        <h3 class="text-2xl font-bold mb-4">Kitchen</h3>
        <p class="text-gray-400">Perfect for families with spacious living areas, multiple beds, and kid-friendly amenities.</p>
      </div>

      <!-- Room 4 -->
      <div class="room-card slide-shake-down-on-scroll">
        <h3 class="text-2xl font-bold mb-4">Bathroom and Toilet</h3>
        <p class="text-gray-400">Featuring modern fixtures and ample space for relaxation.</p>
      </div>
    </div>
  </div>
</section>

  <!-- Booking Section -->
  <section id="book-now" class="py-16 px-8 bg-gradient-to-r from-purple-800 via-purple-900 to-black text-gray-300 slide-in">
    <div class="max-w-6xl mx-auto shadow-md rounded-lg p-6 text-center">
      <h2 class="text-4xl font-bold mb-6">Book Your Stay</h2>
      <p class="text-lg leading-relaxed mb-8">
        Ready to experience luxury? Reserve your Apartment now and enjoy our world-class service!
      </p>
      @guest
          <a href="{{ route('login') }}"  class="bg-green-600 text-white px-8 py-3 rounded-md text-lg font-semibold hover:bg-green-700 transition-colors duration-300">
          Reserve Now
          </a>
        @else
          <a href="{{ route('book.apartment') }}"  class="bg-green-600 text-white px-8 py-3 rounded-md text-lg font-semibold hover:bg-green-700 transition-colors duration-300">
          Reserve Now
          </a>
        @endguest

    </div>
  </section>

  <!-- Contact Us Section -->
  <section class="py-16 px-8 bg-gray-100 text-gray-800">
    <div class="max-w-6xl mx-auto shadow-md rounded-lg p-6">
      <h2 class="text-3xl font-bold mb-4">Contact Us</h2>
      <p class="mb-4">
        Address: 21 ONYIA STREET OLOGO QUARTERS ENUGU, NIGERIA <br>
        Email: <a href="mailto:support@ralphcityapt.com" class="text-blue-500 hover:underline">support@ralphcityapt.com</a><br>
        Phone: <a href="tel:08033091608" class="text-blue-500 hover:underline">08033091608</a>
      </p>
      <div class="mt-6">
        <!-- Google Map Iframe -->
        <iframe
          width="100%"
          height="450"
          style="border:0"
          loading="lazy"
          allowfullscreen
          src="https://www.google.com/maps?q=21%20ONYIA%20STREET%20OLOGO%20QUARTERS%20ENUGU,%20NIGERIA&output=embed">
        </iframe>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-8 px-4 bg-gray-900 text-gray-300 text-center">
    <p>&copy; 2025 Ralph City Apartment. All rights reserved.</p>
  </footer>

  <!-- Scripts -->
  <script>

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.slide-up, .slide-left, .slide-down').forEach(el => {
    el.classList.add('animate');
  });
});

    // Initialize Typed.js for typewriter effect
    document.addEventListener('DOMContentLoaded', function () {
      new Typed('#typedText', {
        strings: ["Luxury.", "Comfort.", "Elegance."],
        typeSpeed: 100,
        backSpeed: 50,
        loop: true,
      });
    });

    // Scroll-triggered animations for elements with fade/slide classes
    const animatedElements = document.querySelectorAll('.slide-in, .fade-in, .slide-up');
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.2 });
    animatedElements.forEach(el => observer.observe(el));

 // Slideshow for hero section background
const images = [
  '/images/hotel1.jpg',
  '/images/hotel2.jpg',
  '/images/hotel3.jpg',
  '/images/hotel7.jpg',
  '/images/hotel5.jpg',
  '/images/hotel6.jpg'
];

const slideshow = document.getElementById('heroBackground');
let currentImageIndex = 0;

function changeImage() {
  slideshow.style.opacity = 0;

  setTimeout(() => {
    slideshow.style.backgroundImage = `url(${images[currentImageIndex]})`;
    slideshow.style.filter = 'blur(3px)';
    slideshow.style.opacity = 1;

    currentImageIndex = (currentImageIndex + 1) % images.length;
  }, 500);
}

setInterval(changeImage, 10000); // Change image every 5 seconds
changeImage(); // Initial image


    // 1) Fetch categories on page load
   document.addEventListener('DOMContentLoaded', function() {
    fetch('/categories')
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById('categorySelect');
            categorySelect.innerHTML = '<option value="">-- Select Category --</option>';
            
            if (data.length === 0) {
                categorySelect.innerHTML += '<option value="">No Categories Found</option>';
                return;
            }

            data.forEach(category => {
                let option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching categories:', error));
});

    // 2) Fetch subcategories when a category is selected
    function fetchSubCategories(catId) {
    const subCategorySelect = document.getElementById('subCategorySelect');
    if (!subCategorySelect) {
        console.error("subCategorySelect not found in DOM!");
        return;
    }

    subCategorySelect.innerHTML = '<option value="">Loading...</option>';
    subCategorySelect.disabled = true;

    if (!catId) {
        subCategorySelect.innerHTML = '<option value="">-- Choose Apartment --</option>';
        return;
    }

    fetch(`/categories/${catId}/subcategories`)
        .then(response => response.json())
        .then(data => {
            console.log("Subcategories fetched:", data); // DEBUG: Log response
            subCategorySelect.innerHTML = '<option value="">-- Choose Apartment --</option>';
            
            if (data.length === 0) {
                subCategorySelect.innerHTML += '<option value="">No Apartments Found</option>';
                return;
            }

            data.forEach(sub => {
                let option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.name;
                subCategorySelect.appendChild(option);
            });

            subCategorySelect.disabled = false;
        })
        .catch(error => console.error('Error fetching subcategories:', error));
}

    // 3) When a subcategory is selected, fetch details and show them
    function onSubCategoryChosen(subCatId) {
    if (!subCatId) {
        document.getElementById('apartmentDetails').classList.add('hidden');
        return;
    }

    fetch(`/subcategories/${subCatId}`) // CORRECTED URL
        .then(response => response.json())
        .then(subCat => {
            console.log("Subcategory details fetched:", subCat); // DEBUG: Log response
            
            document.getElementById('apartmentDetails').classList.remove('hidden');

            document.getElementById('apartmentName').textContent = subCat.name;
            document.getElementById('apartmentPrice').textContent = `Price: ₦${Number(subCat.price).toLocaleString()}`;

            buildSlider(subCat.images || []);
            buildFeatures(subCat);

            document.getElementById('additionalInfo').textContent = subCat.additional_info || '';
        })
        .catch(err => console.error('Error fetching subcategory details:', err));
}

    // Slider logic for subcategory images
    let currentIndex = 0;
    function buildSlider(images) {
    const track = document.getElementById('sliderTrack');
    if (!track) {
        console.error("sliderTrack not found in DOM!");
        return;
    }
    
    track.innerHTML = '';
    currentIndex = 0;

    if (!images.length) {
        track.innerHTML = `
          <div class="slider-image flex items-center justify-center bg-gray-300 text-gray-700">
            No images available
          </div>
        `;
        document.getElementById('prevBtn').classList.add('hidden');
        document.getElementById('nextBtn').classList.add('hidden');
        return;
    }

    images.forEach(img => {
        const div = document.createElement('div');
        div.className = 'slider-image';
        div.style.backgroundImage = `url(${img.image_path})`;
        track.appendChild(div);
    });

    setTimeout(updateSliderPosition, 200); // Delay update for layout adjustments

    if (images.length > 1) {
        document.getElementById('prevBtn').classList.remove('hidden');
        document.getElementById('nextBtn').classList.remove('hidden');
    } else {
        document.getElementById('prevBtn').classList.add('hidden');
        document.getElementById('nextBtn').classList.add('hidden');
    }
}

    function updateSliderPosition() {
      const track = document.getElementById('sliderTrack');
      track.style.transform = `translateX(-${currentIndex * 100}%)`;
    }

    function nextSlide() {
      const track = document.getElementById('sliderTrack');
      const total = track.children.length;
      if (currentIndex < total - 1) {
        currentIndex++;
        updateSliderPosition();
      }
    }

    function prevSlide() {
      if (currentIndex > 0) {
        currentIndex--;
        updateSliderPosition();
      }
    }

    // Attach event listeners for slider arrows
    document.getElementById('prevBtn').addEventListener('click', prevSlide);
    document.getElementById('nextBtn').addEventListener('click', nextSlide);

    // Build features
    function buildFeatures(subCat) {
      const featsEl = document.getElementById('featuresList');
      featsEl.innerHTML = '';

      // Helper function for building feature items
      function featureItem(iconPath, label) {
        return `
          <div class="feature-item flex items-center gap-2">
            <svg class="h-5 w-5 text-green-600 flex-shrink-0"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round"
                    stroke-linejoin="round"
                    d="${iconPath}" />
            </svg>
            <span>${label}</span>
          </div>
        `;
      }

      // Example icons used, feel free to replace with better ones
      if (subCat.has_rooms && subCat.num_rooms) {
        featsEl.innerHTML += featureItem(
          "M5 13l4 4L19 7",
          `Rooms: ${subCat.num_rooms}`
        );
      }
      if (subCat.has_toilets && subCat.num_toilets) {
        featsEl.innerHTML += featureItem(
          "M9 3v12m6-12v12",
          `Toilets: ${subCat.num_toilets}`
        );
      }
      if (subCat.has_sittingroom && subCat.num_sittingrooms) {
        featsEl.innerHTML += featureItem(
          "M10 20h4v-2h-4z",
          `Sitting Room(s): ${subCat.num_sittingrooms}`
        );
      }
      if (subCat.has_kitchen && subCat.num_kitchens) {
        featsEl.innerHTML += featureItem(
          "M4 3h16v2H4zm2 4h12v1H6z",
          `Kitchen(s): ${subCat.num_kitchens}`
        );
      }
      if (subCat.has_balcony && subCat.num_balconies) {
        featsEl.innerHTML += featureItem(
          "M4 4h16v2H4z",
          `Balcony(ies): ${subCat.num_balconies}`
        );
      }
      if (subCat.free_wifi) {
        featsEl.innerHTML += featureItem(
          "M5 12h14M12 5v14",
          "Free Wi-Fi"
        );
      }
      if (subCat.water) {
        featsEl.innerHTML += featureItem(
          "M12 2C6.48 2 2 6.48 2 12",
          "Constant Water"
        );
      }
      if (subCat.electricity) {
        featsEl.innerHTML += featureItem(
          "M13 10V3L4 14h7v7l9-11h-7z",
          "24/7 Electricity"
        );
      }
    }

    // Dummy function for the "Search" button
    function searchApartments() {
      // You can customize how the search logic should behave
      const categoryId = document.getElementById('categorySelect').value;
      const subCategoryId = document.getElementById('subCategorySelect').value;
      if (!categoryId) {
        alert("Please select a category first.");
        return;
      }
      if (!subCategoryId) {
        alert("Please select a subcategory (apartment).");
        return;
      }
      // or just let user pick subcategory from the dropdown which calls onSubCategoryChosen
      onSubCategoryChosen(subCategoryId);
    }

    const animatedOnScroll = document.querySelectorAll('.fade-in-on-scroll');

function handleScrollAnimations() {
  animatedOnScroll.forEach(el => {
    const rect = el.getBoundingClientRect();
    const isVisible = rect.top <= window.innerHeight && rect.bottom >= 0;

    if (isVisible) {
      el.classList.add('animate');
    } else {
      el.classList.remove('animate'); // Remove to re-trigger on scroll again
    }
  });
}

window.addEventListener('scroll', handleScrollAnimations);
window.addEventListener('load', handleScrollAnimations);

const shakeCards = document.querySelectorAll('.slide-shake-left-on-scroll, .slide-shake-right-on-scroll, .slide-shake-up-on-scroll, .slide-shake-down-on-scroll');

function handleRoomAnimations() {
  shakeCards.forEach(card => {
    const rect = card.getBoundingClientRect();
    const inView = rect.top <= window.innerHeight && rect.bottom >= 0;

    if (inView) {
      card.classList.add('animate');
    } else {
      card.classList.remove('animate'); // Reset for scroll-up
    }
  });
}

window.addEventListener('scroll', handleRoomAnimations);
window.addEventListener('load', handleRoomAnimations);

  </script>
</body>
</html>
