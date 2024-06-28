<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rooms') }}
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
        <x-bladewind::button color="green" onclick="createRoom()" class="mb-4">Add room</x-bladewind::button>
        <x-bladewind::table>
            <x-slot name="header">
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Hotel</th>
                <th>Price</th>
                <th>Actions</th>
            </x-slot>
            @if ($rooms->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">Data is empty</td>
                </tr>
            @else  
                @foreach ($rooms as $room)
                    <tr>
                        <td>{{ $room->number }}</td>
                        <td>{{ $room->type }}</td>
                        <td>{{ $room->hotel->name }}</td>
                        <td>{{ $room->price }}</td>
                        <td>
                            <x-bladewind::button size="tiny" onClick="viewRoom({{ $room->id }})">View</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="yellow" onClick="editRoom({{ $room->id }})">Edit</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="red" onclick="deleteRoom({{ $room->id }})">Delete</x-bladewind::button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </x-bladewind::table>
    </div>

    
    <x-bladewind::modal
        size="large"
        name="create-room"
        title="Create Room"
        ok_button_label="Create"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('create-room-form').submit();"
    >
        <div id="room-details-create">

        </div>
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="edit-room"
        title="Edit Room"
        ok_button_label="Update"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('edit-room-form').submit();"
    >
    <div id="room-details-edit">
        <!-- Room details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="view-room"
        title="View Room"
        ok_button_label=""
        cancel_button_label="Back"
    >
    <div id="room-details-view">
        <!-- Room details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="delete-room"
        title="Delete Room"
        ok_button_label="Delete"
        cancel_button_label="Back"
        ok_button_action="document.getElementById('delete-room-form').submit();"
    >
        <div id="room-details-delete">
            <!-- Room details will be loaded here -->
        </div>    
    </x-bladewind::modal>

    <script>
        function editRoom(id) {
            fetch(`/rooms/details`)
                .then(response => response.json())
                .then(data => {
                    fetch(`/rooms/${id}/edit`)
                        .then(response => response.json())
                        .then(room => {
                            let hotelOptions = data.hotel.map(hotel => `<option value="${hotel.id}" ${hotel.id === room.hotel_id ? 'selected' : ''}>${hotel.name}</option>`).join('');
                            let details = `
                                <form id="edit-room-form" action="{{ route('rooms.update', '') }}/${id}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="${room.id}">
                                    <div class="mb-4">
                                        <label for="hotel" class="block text-sm font-medium text-gray-700">Hotel Name</label>
                                        <select id="hotel" name="hotel_id" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option disabled selected>Select Hotel</option>
                                            ${hotelOptions}
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="number" class="block text-sm font-medium text-gray-700">Room Number</label>
                                        <input type="number" name="number" id="number" value="${room.number}" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <div class="mb-4">
                                        <label for="type" class="block text-sm font-medium text-gray-700">Room Type</label>
                                        <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option disabled selected>Select Room Type</option>
                                            <option value="Economy" ${room.type === 'Economy' ? 'selected' : ''}>Economy</option>
                                            <option value="Premium" ${room.type === 'Premium' ? 'selected' : ''}>Premium</option>
                                            <option value="Luxury" ${room.type === 'Luxury' ? 'selected' : ''}>Luxury</option>
                                        </select>
                                    </div>
                                    <div class="w-full">
                                        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                                        <input type="number" name="price" id="price" value="${room.price}" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </form>
                            `;

                            document.getElementById('room-details-edit').innerHTML = details;
                            showModal('edit-room');
                        })
                        .catch(error => console.error('Error fetching room details:', error));
                })
                .catch(error => console.error('Error fetching details:', error));
        }

        function viewRoom(id) {
            fetch(`rooms/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    let details = `
                            <input type="hidden" name="id" value="${data.room.id}">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Hotel Name</label>
                                <input type="text" name="name" id="name" value="${data.hotel.name}" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                            <div class="mb-4">
                                <label for="number" class="block text-sm font-medium text-gray-700">Room Number</label>
                                <input type="number" name="number" id="number" value="${data.room.number}" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                            <div class="mb-4">
                                <label for="type" class="block text-sm font-medium text-gray-700">Room Type</label>
                                <input type="text" name="type" id="type" value="${data.room.type}" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                            <div class="w-full">
                                <label for="price" class="block text-sm font-medium text-gray-700">Room Type</label>
                                <input type="number" name="price" id="price" value="${data.room.price}" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                    `;
                    document.getElementById('room-details-view').innerHTML = details;
                    showModal('view-room');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function deleteRoom(id) {
            let details = `
                <form id="delete-room-form" action="/rooms/${id}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" value="${id}" name="id">
                </form>
                <p>Are you sure you want to delete this room?</p> 
            `;
            document.getElementById('room-details-delete').innerHTML = details;
            showModal('delete-room');
        }

        function createRoom(id) {
            fetch(`/rooms/details`)
                .then(response => response.json())
                .then(data => {
                    let hotelOptions = data.hotel.map(hotel => `<option value="${hotel.id}">${hotel.name}</option>`).join('');
                    let details = `
                        <form id="create-room-form" action="{{ route('rooms.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="hotel" class="block text-sm font-medium text-gray-700">Hotel Name</label>
                                <select id="hotel" name="hotel_id" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option disabled selected>Select Hotel</option>
                                    ${hotelOptions}
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="number" class="block text-sm font-medium text-gray-700">Room Number</label>
                                <input type="number" name="number" id="number" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label for="type" class="block text-sm font-medium text-gray-700">Room Type</label>
                                <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option disabled selected>Select Room Type</option>
                                    <option value="Economy">Economy</option>
                                    <option value="Premium">Premium</option>
                                    <option value="Luxury">Luxury</option>
                                </select>
                            </div>
                            <div class="w-full">
                                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                                <input type="number" name="price" id="price" required class="mt-1 block w-full rounded-md broom-gray-300 shadow-sm focus:broom-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </form>
                    `;
                    document.getElementById('room-details-create').innerHTML = details;
                    showModal('create-room');
                })
                .catch(error => console.error('Error fetching details:', error));
        }
    </script>
</x-app-layout>