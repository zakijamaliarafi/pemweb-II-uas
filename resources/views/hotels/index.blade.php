<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hotels') }}
        </h2>
    </x-slot>
    @if (session('success'))
        <x-bladewind::alert
            type="success">
            {{ session('success') }}
        </x-bladewind::alert>
    @endif
    @if (session('error'))
        <x-bladewind::alert
            type="error">
            {{ session('error') }}
        </x-bladewind::alert>
    @endif
    <div class="px-40 mx-auto mt-8">
        <x-bladewind::button color="green" onclick="showModal('create-hotel')" class="mb-4">Create hotel</x-bladewind::button>
        <x-bladewind::table>
            <x-slot name="header">
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Actions</th>
            </x-slot>
            @if ($hotels->isEmpty())
                <tr>
                    <td colspan="4" class="text-center">Data is empty</td>
                </tr>
            @else  
                @foreach ($hotels as $hotel)
                    <tr>
                        <td>{{ $hotel->name }}</td>
                        <td>{{ $hotel->address }}</td>
                        <td>{{ $hotel->email }}</td>
                        <td>
                            <x-bladewind::button size="tiny" onClick="viewHotel({{ $hotel->id }})">View</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="yellow" onClick="editHotel({{ $hotel->id }})">Edit</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="red" onclick="deleteHotel({{ $hotel->id }})">Delete</x-bladewind::button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </x-bladewind::table>
    </div>

    
    <x-bladewind::modal
        size="large"
        name="create-hotel"
        title="Create Hotel"
        ok_button_label="Create"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('create-hotel-form').submit();"
    >
        <form id="create-hotel-form" action="{{ route('hotels.store') }}" method="POST">
            @csrf
            <x-bladewind::input  
                name="name"
                id="name"
                label="Hotel Name"
                required="true"
            />
            <div class="flex w-full gap-4">
                <div class="w-full">
                    <x-bladewind::input  
                        name="address"
                        id="address"
                        label="Hotel Address"
                        required="true"
                    />
                </div>
                <div class="w-full">
                    <x-bladewind::input  
                        name="email"
                        id="email"
                        label="Hotel Email"
                        required="true"
                    />
                </div>
            </div>
        </form>
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="edit-hotel"
        title="Edit Hotel"
        ok_button_label="Update"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('edit-hotel-form').submit();"
    >
    <div id="hotel-details-edit">
        <!-- Hotel details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="view-hotel"
        title="View Hotel"
        ok_button_label=""
        cancel_button_label="Back"
    >
    <div id="hotel-details-view">
        <!-- Hotel details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="delete-hotel"
        title="Delete Hotel"
        ok_button_label="Delete"
        cancel_button_label="Back"
        ok_button_action="document.getElementById('delete-hotel-form').submit();"
    >
        <div id="hotel-details-delete">
            <!-- Hotel details will be loaded here -->
        </div>    
    </x-bladewind::modal>

    <script>
        function editHotel(id) {
            console.log('Edit hotel clicked for id:', id);
            fetch(`hotels/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    let details = `
                        <form id="edit-hotel-form" action="/hotels/${id}/update" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="${data.id}">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Hotel Name</label>
                                <input type="text" name="name" id="name" value="${data.name}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="flex w-full gap-4 mb-4">
                                <div class="w-full">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Hotel Address</label>
                                    <input type="text" name="address" id="address" value="${data.address}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="w-full">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Hotel Email</label>
                                    <input type="text" name="email" id="email" value="${data.email}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                            </div>
                        </form>
                    `;
                    document.getElementById('hotel-details-edit').innerHTML = details;
                    showModal('edit-hotel');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function viewHotel(id) {
            fetch(`hotels/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    let details = `
                            <input type="hidden" name="id" value="${data.id}">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Hotel Name</label>
                                <input type="text" name="name" id="name" value="${data.name}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                            <div class="flex w-full gap-4 mb-4">
                                <div class="w-full">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Hotel Address</label>
                                    <input type="text" name="address" id="address" value="${data.address}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                                </div>
                                <div class="w-full">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Hotel Email</label>
                                    <input type="text" name="email" id="email" value="${data.email}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                                </div>
                            </div>
                    `;
                    document.getElementById('hotel-details-view').innerHTML = details;
                    showModal('view-hotel');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function deleteHotel(id) {
            let details = `
                <form id="delete-hotel-form" action="/hotels/${id}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" value="${id}" name="id">
                </form>
                <p>Are you sure you want to delete this hotel?</p> 
            `;
            document.getElementById('hotel-details-delete').innerHTML = details;
            showModal('delete-hotel');
        }
    </script>
</x-app-layout>