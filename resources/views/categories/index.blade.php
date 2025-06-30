@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    @if(session('success'))
      <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
        {{ session('success') }}
      </div>
    @endif

    <h2 class="text-xl font-bold mb-4">Create a New Category</h2>
    <form action="{{ route('categories.store') }}" method="POST" class="mb-6 p-4 border rounded">
        @csrf
        <div class="mb-2">
            <label class="block font-semibold">Category Name</label>
            <input type="text" name="name" class="border-gray-300 rounded w-full" required>
        </div>
        <div class="mb-2">
            <label class="block font-semibold">Price</label>
            <input type="number" step="0.01" name="price" class="border-gray-300 rounded w-full">
        </div>
        <button class="bg-blue-600 text-white px-3 py-1 rounded">Create</button>
    </form>

    <table class="w-full table-auto border-collapse">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-2 py-1 border">ID</th>
            <th class="px-2 py-1 border">Category Name</th>
            <th class="px-2 py-1 border">Price</th>
            <th class="px-2 py-1 border">File Path</th>
            <th class="px-2 py-1 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($categories as $cat)
          <tr>
            <td class="border px-2 py-1">{{ $cat->id }}</td>
            <td class="border px-2 py-1">{{ $cat->name }}</td>
            <td class="border px-2 py-1">{{ number_format($cat->price,2) }}</td>
            <td class="border px-2 py-1">{{ $cat->file_path }}</td>
            <td class="border px-2 py-1">
                <!-- Edit category -->
                <a href="{{ route('categories.edit', $cat->id) }}"
                   class="bg-yellow-500 text-white px-2 py-1">Edit Category</a>

                <!-- Delete category -->
                <form action="{{ route('categories.destroy', $cat->id) }}" 
                      method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 text-white px-2 py-1"
                            onclick="return confirm('Delete category?')">
                      Delete
                    </button>
                </form>

                <!-- Add Subcategory -->
                <a href="{{ route('subcategories.create', $cat->id) }}"
                   class="bg-gray-600 text-white px-2 py-1">Add Subcategory</a>
            </td>
          </tr>

          <!-- Show subcategories for this category -->
          @php
            $subcats = $cat->subCategories()->orderBy('id','desc')->get();
          @endphp
          @if($subcats->count())
            <tr>
              <td colspan="5" class="border px-2 py-2">
                <table class="table-auto w-full">
                  <thead>
                    <tr class="bg-gray-200">
                      <th class="border px-2 py-1">ID</th>
                      <th class="border px-2 py-1">Subcategory Name</th>
                      <th class="border px-2 py-1">Price</th>
                      <th class="border px-2 py-1">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($subcats as $sc)
                      <tr>
                        <td class="border px-2 py-1">{{ $sc->id }}</td>
                        <td class="border px-2 py-1">{{ $sc->name }}</td>
                        <td class="border px-2 py-1">{{ number_format($sc->price,2) }}</td>
                        <td class="border px-2 py-1">
                          <!-- Link to subcategory "show" page if you have one -->
                          <a href="{{ route('subcategories.index', $sc->id) }}"
                             class="bg-purple-600 text-white px-2 py-1">View</a>

                          <!-- Edit subcat -->
                          <a href="{{ route('subcategories.edit', $sc->id) }}"
                             class="bg-yellow-500 text-white px-2 py-1">Edit</a>

                          <!-- Delete subcat -->
                          <form action="{{ route('subcategories.destroy', $sc->id) }}" method="POST" style="display:inline;">
                              @csrf
                              @method('DELETE')
                              <button class="bg-red-600 text-white px-2 py-1" onclick="return confirm('Delete subcategory?')">Delete</button>
                          </form>

                          <!-- Add images to subcat -->
                          <form action="{{ route('subcategories.images.store', $sc->id) }}" method="POST" enctype="multipart/form-data" style="display:inline;">
                              @csrf
                              <label class="text-sm">+ Images</label>
                              <input type="file" name="images[]" multiple>
                              <button class="bg-blue-600 text-white px-2 py-1">Upload</button>
                          </form>

                          <!-- Availability link (if you have it) -->
                          <a href="{{ route('subcategories.availability.index',$sc->id) }}"
                             class="bg-green-600 text-white px-2 py-1">Availability</a>
                        </td>
                      </tr>

                      <!-- If subcategory has images, display them below -->
                      @if($sc->images && $sc->images->count())
                        <tr>
                          <td colspan="5" class="border px-2 py-2">
                            <strong>Images:</strong>
                            @foreach($sc->images as $img)
                              <div style="display:inline-block; margin:5px;">
                                <!-- Show the image, 80px wide -->
                                <img src="{{ asset($img->image_path) }}" alt="img" width="80" height="80">
                                <!-- Delete image -->
                                <form action="{{ route('subcategories.images.destroy', $img->id) }}"
                                      method="POST" style="display:inline;">
                                  @csrf
                                  @method('DELETE')
                                  <button class="text-red-600 text-sm" onclick="return confirm('Remove this image?')">
                                    Delete
                                  </button>
                                </form>
                              </div>
                            @endforeach
                          </td>
                        </tr>
                      @endif
                    @endforeach
                  </tbody>
                </table>
              </td>
            </tr>
          @endif
          @endforeach
        </tbody>
    </table>

    <div class="mt-4">
      {{ $categories->links() }}
    </div>
</div>
@endsection
