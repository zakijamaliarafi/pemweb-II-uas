<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Guests') }}
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
        <x-bladewind::button color="green" onclick="showModal('create-guest')" class="mb-4">Create guest</x-bladewind::button>
        <x-bladewind::table>
            <x-slot name="header">
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Actions</th>
            </x-slot>
            @if ($guests->isEmpty())
                <tr>
                    <td colspan="4" class="text-center">Data is empty</td>
                </tr>
            @else  
                @foreach ($guests as $guest)
                    <tr>
                        <td>{{ $guest->name }}</td>
                        <td>{{ $guest->address }}</td>
                        <td>{{ $guest->email }}</td>
                        <td>
                            <x-bladewind::button size="tiny" onClick="viewGuest({{ $guest->id }})">View</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="yellow" onClick="editGuest({{ $guest->id }})">Edit</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="red" onclick="deleteGuest({{ $guest->id }})">Delete</x-bladewind::button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </x-bladewind::table>
    </div>

    
    <x-bladewind::modal
        size="large"
        name="create-guest"
        title="Create Guest"
        ok_button_label="Create"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('create-guest-form').submit();"
    >
        <form id="create-guest-form" action="{{ route('guests.store') }}" method="POST">
            @csrf
            <x-bladewind::input  
                name="name"
                id="name"
                label="Guest Name"
                required="true"
            />
            <div class="flex w-full gap-4">
                <div class="w-full">
                    <x-bladewind::input  
                        name="address"
                        id="address"
                        label="Guest Address"
                        required="true"
                    />
                </div>
                <div class="w-full">
                    <x-bladewind::input  
                        name="email"
                        id="email"
                        label="Guest Email"
                        required="true"
                    />
                </div>
            </div>
        </form>
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="edit-guest"
        title="Edit Guest"
        ok_button_label="Update"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('edit-guest-form').submit();"
    >
    <div id="guest-details-edit">
        <!-- Guest details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="view-guest"
        title="View Guest"
        ok_button_label=""
        cancel_button_label="Back"
    >
    <div id="guest-details-view">
        <!-- Guest details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="delete-guest"
        title="Delete Guest"
        ok_button_label="Delete"
        cancel_button_label="Back"
        ok_button_action="document.getElementById('delete-guest-form').submit();"
    >
        <div id="guest-details-delete">
            <!-- Guest details will be loaded here -->
        </div>    
    </x-bladewind::modal>

    <script>
        function editGuest(id) {
            console.log('Edit guest clicked for id:', id);
            fetch(`guests/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    let details = `
                        <form id="edit-guest-form" action="/guests/${id}/update" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="${data.id}">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Guest Name</label>
                                <input type="text" name="name" id="name" value="${data.name}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="flex w-full gap-4 mb-4">
                                <div class="w-full">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Guest Address</label>
                                    <input type="text" name="address" id="address" value="${data.address}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="w-full">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Guest Email</label>
                                    <input type="text" name="email" id="email" value="${data.email}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                            </div>
                        </form>
                    `;
                    document.getElementById('guest-details-edit').innerHTML = details;
                    showModal('edit-guest');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function viewGuest(id) {
            fetch(`guests/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    let details = `
                            <input type="hidden" name="id" value="${data.id}">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Guest Name</label>
                                <input type="text" name="name" id="name" value="${data.name}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                            <div class="flex w-full gap-4 mb-4">
                                <div class="w-full">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Guest Address</label>
                                    <input type="text" name="address" id="address" value="${data.address}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                                </div>
                                <div class="w-full">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Guest Email</label>
                                    <input type="text" name="email" id="email" value="${data.email}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                                </div>
                            </div>
                    `;
                    document.getElementById('guest-details-view').innerHTML = details;
                    showModal('view-guest');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function deleteGuest(id) {
            let details = `
                <form id="delete-guest-form" action="/guests/${id}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" value="${id}" name="id">
                </form>
                <p>Are you sure you want to delete this guest?</p> 
            `;
            document.getElementById('guest-details-delete').innerHTML = details;
            showModal('delete-guest');
        }
    </script>
</x-app-layout>