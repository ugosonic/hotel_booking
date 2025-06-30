@extends('layouts.app')

@section('page-heading', 'Book an Apartment')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">Book Apartment</h2>

    <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow space-y-6">

    @if(session('success'))
  <div class="flash-message bg-green-100 text-green-700 border-l-4 border-green-500 p-4">
      {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="flash-message bg-red-100 text-red-700 border-l-4 border-red-500 p-4">
      {{ session('error') }}
  </div>
@endif


        <!-- Booking Form -->
        <form action="{{ route('apartment.book') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- If staff, let them pick booking_type -->
            @if(auth()->user()->isStaff())
                <!-- Booking Type -->
                <div>
                    <label class="block font-semibold mb-1">Booking Type</label>
                    <select name="booking_type"
                            class="w-full border-gray-300 rounded"
                            id="bookingTypeSelect"
                            onchange="toggleBookingType(this.value)">
                        <option value="guest" selected>Guest</option>
                        <option value="client">Client</option>
                    </select>
                </div>

                <!-- Guest fields (when booking_type=guest) -->
                <div id="guestFields" class="space-y-2">
                    <label class="font-semibold">Name</label>
                    <input type="text" name="guest_name" id="guestNameInput"
                           class="w-full border-gray-300 rounded" autocomplete="off" required/>

                    <label class="font-semibold">Email</label>
                    <input type="email" name="guest_email" id="guestEmailInput"
                           class="w-full border-gray-300 rounded" autocomplete="off" required/>

                    <label class="font-semibold">Address</label>
                    <input type="text" name="guest_address" id="guestAddressInput"
                           class="w-full border-gray-300 rounded" autocomplete="off" required/>

                    <label class="font-semibold">Phone</label>
                    <input type="text" name="guest_phone" id="guestPhoneInput"
                           class="w-full border-gray-300 rounded" autocomplete="off" required/>

                    <label class="font-semibold">Date of Birth</label>
                    <input type="date" name="guest_dob" id="guestDob"
                           class="w-full border-gray-300 rounded" autocomplete="off" required/>

                    <!-- Document -->
                    <label class="font-semibold">Document Type</label>
                    <select name="doc_type"
                            class="w-full border-gray-300 rounded"
                            onchange="toggleDocNumber(this.value)">
                        <option value="">-- Select doc --</option>
                        <option value="driving_licence">Driving Licence</option>
                        <option value="passport">International Passport</option>
                        <option value="nin">NIN Document</option>
                    </select>

                    <div id="docNumberField" class="hidden space-y-2">
                        <label class="font-semibold">Document Number</label>
                        <input type="text" name="doc_number" class="w-full border-gray-300 rounded" />

                        <label class="font-semibold">Upload Document</label>
                        <input type="file" name="doc_upload" class="w-full border-gray-300 p-1 rounded" />
                    </div>
                </div>

                <!-- Client fields (when booking_type=client) -->
                <div id="clientFields" class="hidden space-y-2">
                    <label class="font-semibold">Select Client</label>
                    <input type="text" id="clientSearch" placeholder="Search client name..."
                           class="w-full border border-gray-300 rounded mb-2 p-1"
                           onkeyup="filterClients(this.value)" />

                    <select name="client_id" id="clientSelect"
                            class="w-full border-gray-300 rounded h-32 overflow-y-auto">
                        <option value="">-- Choose a client --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <!-- If user is client (not staff) -->
                <div class="space-y-2">
                    <label class="block font-semibold mb-1">Your Name</label>
                    <input type="text" name="client_name"
                           class="w-full border-gray-300 rounded bg-gray-100"
                           value="{{ auth()->user()->name }}"
                           readonly />
                </div>
            @endif

            <!-- Additional Guests Section -->
            <div class="mt-4">
                <h3 class="text-lg font-semibold mb-2">Additional Guests</h3>

                <div id="additionalGuestsContainer" class="space-y-4">
                    <!-- We'll dynamically insert extra guest forms here -->
                </div>

                <!-- Button to add more guests -->
                <button type="button"
                        class="mt-2 inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
                        onclick="addAdditionalGuest()">
                    <i class="fas fa-user-plus mr-1"></i>
                    Add Another Guest
                </button>
            </div>
            <!-- Hidden input for extra guests JSON -->
            <input type="hidden" name="extra_guests" id="extraGuestsInput" />

            <!-- Category and Subcategory Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block font-semibold mb-1">Apartment Type</label>
                    <select name="category_id"
                            class="w-full border-gray-300 rounded"
                            id="categorySelect"
                            onchange="loadSubCategories(this.value)">
                        <option value="">-- Choose Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Select Apartment</label>
                    <select name="sub_category_id"
                            class="w-full border-gray-300 rounded"
                            id="subCategorySelect"
                            onchange="onSubCategorySelected(this.value)"
                            disabled>
                        <option value="">-- Choose Subcategory --</option>
                    </select>
                </div>
            </div>

            <!-- Calendar Section -->
            <div id="calendarSection" class="mt-6">
                <h3 class="font-bold text-lg mb-4">Booking Calendar</h3>
                <div id="calendar"></div>
            </div>

            <!-- Features + Price + Image Slider -->
            <div id="featuresSection" class="hidden p-4 bg-gray-50 rounded mt-4">
                <!-- Image Slider -->
                <div class="mb-6">
                    <div id="imageSlider" class="relative w-full overflow-hidden">
                        <div id="sliderContainer" class="flex transition-transform duration-300">
                            <!-- Images will be dynamically inserted here -->
                        </div>

                        <!-- Navigation Arrows -->
                        <button id="prevButton"
                                type="button"
                                class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full hidden"
                                onclick="prevSlide()">
                            ‹
                        </button>
                        <button id="nextButton"
                                type="button"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full hidden"
                                onclick="nextSlide()">
                            ›
                        </button>
                    </div>
                </div>

                <h3 class="font-bold text-lg mb-4">Apartment Features</h3>
                <ul id="featuresList" class="grid grid-cols-2 gap-3 text-gray-800 text-sm"></ul>

                <div class="mt-4">
                    <strong class="text-lg">Price:</strong>
                    <span id="apartmentPrice" class="text-red-600 text-xl font-extrabold">₦0</span>
                    <span id="slotsInfo" class="text-sm text-gray-600 ml-2"></span>
                </div>
                <div>
                    <strong>Additional Info:</strong>
                    <p id="apartmentInfo" class="text-gray-700"></p>
                </div>
            </div>

            <!-- Start/End Date, Nights -->
            <div class="flex space-x-4 mt-4">
                <div class="flex-1">
                    <label class="block font-semibold mb-1">Check-In</label>
                    <input type="date" name="start_date" id="startDate"
                           class="w-full border-gray-300 rounded"
                           onchange="calculateNights()" />
                </div>
                <div class="flex-1">
                    <label class="block font-semibold mb-1">Check-Out</label>
                    <input type="date" name="end_date" id="endDate"
                           class="w-full border-gray-300 rounded"
                           onchange="calculateNights()" />
                </div>
            </div>
            <div>
                <span id="nightsDisplay" class="text-gray-700"></span>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                Confirm Booking
            </button>
        </form>
    </div>
</div>

<!-- FullCalendar CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
  setTimeout(function(){
    document.querySelectorAll('.flash-message').forEach(function(el){
      el.style.display = 'none';
    });
  }, 10000); // 10000 milliseconds = 10 seconds
</script>

<!-- FontAwesome for icons: (Replace "your-fontawesome-kit.js" with your real kit) -->
<script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

<script>
/* -------------------------------------------------------
 | (A) Global/initial setup
 ------------------------------------------------------- */

// If you need to pass in subcategory data, you can do so similarly:


// But here we'll just fetch them on-the-fly from the API endpoints.

// We'll track additional guests in an array:
let additionalGuests = [];

// For the slider:
let currentSliderIndex = 0;
let totalImages = 0;

/* -------------------------------------------------------
 | (B) Toggle Guest vs. Client
 ------------------------------------------------------- */
function toggleBookingType(val) {
    const guestFields   = document.getElementById('guestFields');
    const clientFields  = document.getElementById('clientFields');

    const guestNameInput  = document.getElementById('guestNameInput');
    const guestEmailInput = document.getElementById('guestEmailInput');
    const guestPhoneInput = document.getElementById('guestPhoneInput');
    const guestAddrInput  = document.getElementById('guestAddressInput');
    const guestDobInput   = document.getElementById('guestDob');
    const clientSelect    = document.getElementById('clientSelect');

    if (val === 'client') {
        // Hide guest fields
        guestFields.classList.add('hidden');
        guestNameInput.removeAttribute('required');
        guestEmailInput.removeAttribute('required');
        guestPhoneInput.removeAttribute('required');
        guestAddrInput.removeAttribute('required');
        guestDobInput.removeAttribute('required');

        // Show client fields
        clientFields.classList.remove('hidden');
        clientSelect.setAttribute('required', true);
    } else {
        // Show guest fields
        guestFields.classList.remove('hidden');
        guestNameInput.setAttribute('required', true);
        guestEmailInput.setAttribute('required', true);
        guestPhoneInput.setAttribute('required', true);
        guestAddrInput.setAttribute('required', true);
        guestDobInput.setAttribute('required', true);

        // Hide client fields
        clientFields.classList.add('hidden');
        clientSelect.removeAttribute('required');
    }
}

/* -------------------------------------------------------
 | (C) Toggle doc upload
 ------------------------------------------------------- */
function toggleDocNumber(val) {
    const docNumberField = document.getElementById('docNumberField');
    if (val) {
        docNumberField.classList.remove('hidden');
    } else {
        docNumberField.classList.add('hidden');
    }
}

/* -------------------------------------------------------
 | (D) Filter clients in the dropdown
 ------------------------------------------------------- */
function filterClients(searchVal) {
    const selectEl = document.getElementById('clientSelect');
    searchVal = searchVal.toLowerCase();
    for (let i = 0; i < selectEl.options.length; i++) {
        const txt = selectEl.options[i].text.toLowerCase();
        selectEl.options[i].style.display = txt.includes(searchVal) ? '' : 'none';
    }
}

/* -------------------------------------------------------
 | (E) Additional Guests - dynamic form
 ------------------------------------------------------- */
function addAdditionalGuest() {
    const container = document.getElementById('additionalGuestsContainer');
    const guestIndex = additionalGuests.length;

    const newDiv = document.createElement('div');
    newDiv.className = "p-4 border rounded bg-gray-50";
    newDiv.innerHTML = `
      <h4 class="font-semibold mb-2">Guest #${guestIndex + 2}</h4>
      <div class="grid grid-cols-2 gap-4 mb-2">
        <div>
          <label class="block text-sm font-medium">Name</label>
          <input type="text" class="w-full border-gray-300 rounded subGuestName"
                 data-index="${guestIndex}" />
        </div>
        <div>
          <label class="block text-sm font-medium">Date of Birth</label>
          <input type="date"  max="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded subGuestDob"
                 data-index="${guestIndex}" />
        </div>
        <div>
          <label class="block text-sm font-medium">Phone</label>
          <input type="text" class="w-full border-gray-300 rounded subGuestPhone"
                 data-index="${guestIndex}" />
        </div>
        <div>
          <label class="block text-sm font-medium">Address</label>
          <input type="text" class="w-full border-gray-300 rounded subGuestAddress"
                 data-index="${guestIndex}" />
        </div>
      </div>
      <label class="inline-flex items-center text-sm">
        <input type="checkbox" class="subGuestSameParent mr-2" data-index="${guestIndex}"
               onclick="toggleSameAsParent(${guestIndex})" />
        Same as primary guest?
      </label>
    `;

    container.appendChild(newDiv);
    additionalGuests.push({
        name: "",
        dob: "",
        phone: "",
        address: "",
        sameAsParent: false
    });
}

function toggleSameAsParent(idx) {
    const box = document.querySelector(`.subGuestSameParent[data-index="${idx}"]`);
    if (!box) return;
    const isChecked = box.checked;

    if (isChecked) {
        // Copy from main guest fields
        const pName    = document.getElementById('guestNameInput')?.value || '';
        const pPhone   = document.getElementById('guestPhoneInput')?.value || '';
        const pAddress = document.getElementById('guestAddressInput')?.value || '';

        document.querySelector(`.subGuestName[data-index="${idx}"]`).value = pName;
        document.querySelector(`.subGuestPhone[data-index="${idx}"]`).value = pPhone;
        document.querySelector(`.subGuestAddress[data-index="${idx}"]`).value = pAddress;
    }
}

// Before form submit, store sub-guest info in hidden
document.addEventListener('submit', () => {
    additionalGuests.forEach((g, idx) => {
        g.name    = document.querySelector(`.subGuestName[data-index="${idx}"]`)?.value || '';
        g.dob     = document.querySelector(`.subGuestDob[data-index="${idx}"]`)?.value || '';
        g.phone   = document.querySelector(`.subGuestPhone[data-index="${idx}"]`)?.value || '';
        g.address = document.querySelector(`.subGuestAddress[data-index="${idx}"]`)?.value || '';

        const chk = document.querySelector(`.subGuestSameParent[data-index="${idx}"]`);
        g.sameAsParent = chk && chk.checked;
    });
    document.getElementById('extraGuestsInput').value = JSON.stringify(additionalGuests);
});

/* -------------------------------------------------------
 | (F) Load subcategories on category selection
 ------------------------------------------------------- */
function loadSubCategories(catId) {
    const subSelect = document.getElementById('subCategorySelect');
    subSelect.disabled = true;
    subSelect.innerHTML = '<option value="">Loading...</option>';

    if (!catId) {
        subSelect.innerHTML = '<option value="">-- Choose Subcategory --</option>';
        subSelect.disabled = true;
        return;
    }

    // Adjust this to your actual API route for subcategories
    fetch(`/api/categories/${catId}/subcategories`)
        .then(response => response.json())
        .then(data => {
            subSelect.innerHTML = '<option value="">-- Choose Subcategory --</option>';
            data.forEach(sub => {
                subSelect.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
            });
            subSelect.disabled = false;
        })
        .catch(err => {
            console.error('Error fetching subcategories:', err);
            subSelect.innerHTML = '<option value="">-- Error Loading --</option>';
        });
}

/* -------------------------------------------------------
 | (G) When a subcategory is selected
 ------------------------------------------------------- */
let currentSubCategory = null;

function onSubCategorySelected(subCatId) {
    const featuresSection = document.getElementById('featuresSection');
    // If blank, hide features
    if (!subCatId) {
        featuresSection.classList.add('hidden');
        // clear the calendar events
        if (calendar) {
            calendar.removeAllEvents();
        }
        return;
    }

    // Fetch that subcategory details
    fetch(`/api/subcategories/${subCatId}`)
        .then(response => response.json())
        .then(data => {
            currentSubCategory = data;
            showSubCategoryFeatures(data);
            updateCalendar(subCatId);
        })
        .catch(err => console.error('Error fetching subcategory:', err));
}

/* -------------------------------------------------------
 | (H) Show subcategory features + images
 ------------------------------------------------------- */
function showSubCategoryFeatures(subCat) {
    const section = document.getElementById('featuresSection');
    section.classList.remove('hidden');

    // Build the features list
    let featuresHTML = '';
    if (subCat.num_rooms > 0) {
        featuresHTML += featureItem('fa-bed', 'Rooms', subCat.num_rooms);
    }
    if (subCat.num_toilets > 0) {
        featuresHTML += featureItem('fa-toilet', 'Toilets', subCat.num_toilets);
    }
    if (subCat.num_sittingrooms > 0) {
        featuresHTML += featureItem('fa-couch', 'Sitting Rooms', subCat.num_sittingrooms);
    }
    if (subCat.num_kitchens > 0) {
        featuresHTML += featureItem('fa-utensils', 'Kitchens', subCat.num_kitchens);
    }
    if (subCat.num_balconies > 0) {
        featuresHTML += featureItem('fa-warehouse', 'Balconies', subCat.num_balconies);
    }
    if (subCat.free_wifi) {
        featuresHTML += featureItem('fa-wifi', 'Free WiFi');
    }
    if (subCat.water) {
        featuresHTML += featureItem('fa-tint', 'Water Available');
    }
    if (subCat.electricity) {
        featuresHTML += featureItem('fa-bolt', '24/7 Electricity');
    }
    if (!featuresHTML) {
        featuresHTML = '<li>No special features listed.</li>';
    }

    document.getElementById('featuresList').innerHTML = featuresHTML;
    document.getElementById('apartmentPrice').textContent = `₦${Number(subCat.price).toLocaleString()}`;
    document.getElementById('apartmentInfo').textContent = subCat.additional_info || 'None';
    document.getElementById('slotsInfo').textContent = subCat.max_slots
        ? `(Max ${subCat.max_slots} slots per day)`
        : '';

   // Setup image slider
const sliderContainer = document.getElementById('sliderContainer');
sliderContainer.innerHTML = '';
currentSliderIndex = 0;
totalImages = subCat.images.length;

subCat.images.forEach((img, index) => {
    const imgDiv = document.createElement('div');
    imgDiv.className = 'w-full flex-shrink-0';
    imgDiv.innerHTML = `
        <img src="${img.image_path}"
             class="mx-auto w-full h-72 object-cover rounded-lg"
             alt="Subcategory image ${index + 1}">
    `;
    sliderContainer.appendChild(imgDiv);
});


    const prevBtn = document.getElementById('prevButton');
    const nextBtn = document.getElementById('nextButton');
    // Show/hide nav arrows
    if (totalImages > 1) {
        prevBtn.classList.remove('hidden');
        nextBtn.classList.remove('hidden');
    } else {
        prevBtn.classList.add('hidden');
        nextBtn.classList.add('hidden');
    }

    updateSliderPosition();
}

// Helper to build a feature list item
function featureItem(iconClass, label, value = '') {
    return `
        <li class="flex items-center gap-2">
            <i class="fas ${iconClass} text-blue-600"></i>
            <strong>${label}:</strong> <span>${value}</span>
        </li>
    `;
}

/* -------------------------------------------------------
 | (I) Slider navigation
 ------------------------------------------------------- */
function updateSliderPosition() {
    const slider = document.getElementById('sliderContainer');
    slider.style.transform = `translateX(-${currentSliderIndex * 100}%)`;
}

function nextSlide() {
    if (currentSliderIndex < totalImages - 1) {
        currentSliderIndex++;
        updateSliderPosition();
    }
}

function prevSlide() {
    if (currentSliderIndex > 0) {
        currentSliderIndex--;
        updateSliderPosition();
    }
}

/* -------------------------------------------------------
 | (J) FullCalendar
 ------------------------------------------------------- */
let calendar; // We'll store the instance here

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
          // Only let clients see from "today" onward:
  @if(!auth()->user()->isStaff())
    validRange: {
      start: new Date(), // or today string
    },
    @endif
        dateClick: handleDateClick,
        events: []
    });
    calendar.render();

    // Adjust min date for startDate/endDate to "today"
    const todayStr = new Date().toISOString().split('T')[0];
    document.getElementById('startDate').setAttribute('min', todayStr);
    document.getElementById('endDate').setAttribute('min', todayStr);

    // If there's a "guestDob", remove its max limit
    if (document.getElementById('guestDob')) {
        document.getElementById('guestDob').removeAttribute('max');
    }
});

function updateCalendar(subCatId) {
    if (!calendar) return;

    // Clear any existing events first
    calendar.removeAllEvents();

    // Fetch the booking events for this subcategory
    fetch(`/api/subcategories/${subCatId}/calendar`)
        .then(response => response.json())
        .then(data => {
            // data.events should be an array of event objects
            calendar.addEventSource(data.events);
        })
        .catch(error => console.error('Error fetching calendar data:', error));
}

function handleDateClick(info) {
    // Simple logic: if start not set or end was set, reset
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    if (!startDate.value || endDate.value) {
        startDate.value = info.dateStr;
        endDate.value = '';
    } else {
        endDate.value = info.dateStr;
    }
    calculateNights();
}

/* -------------------------------------------------------
 | (K) Calculate nights
 ------------------------------------------------------- */
function calculateNights() {
    const startVal = document.getElementById('startDate').value;
    const endVal = document.getElementById('endDate').value;
    const disp = document.getElementById('nightsDisplay');

    if (!startVal || !endVal) {
        disp.textContent = '';
        return;
    }

    const startDate = new Date(startVal);
    const endDate = new Date(endVal);
    const diffTime = endDate - startDate;

    if (diffTime < 0) {
        disp.textContent = 'End date is before start date!';
        return;
    }

    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    disp.textContent = `${diffDays} night(s). Checkout by 12pm on end date.`;
    
}
</script>
@endsection
