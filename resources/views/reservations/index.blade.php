<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservations') }}
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
        <x-bladewind::button color="green" onclick="createReservation()" class="mb-4">Add reservation</x-bladewind::button>
        <x-bladewind::table>
            <x-slot name="header">
                <th>Guest Name</th>
                <th>Room Number</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Actions</th>
            </x-slot>
            @if ($reservations->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">Data is empty</td>
                </tr>
            @else  
                @foreach ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->guest->name }}</td>
                        <td>{{ $reservation->room->number }}</td>
                        <td>{{ $reservation->check_in }}</td>
                        <td>{{ $reservation->check_out }}</td>
                        <td>
                            <x-bladewind::button size="tiny" onClick="viewReservation({{ $reservation->id }})">View</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="yellow" onClick="editReservation({{ $reservation->id }})">Edit</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="red" onclick="deleteReservation({{ $reservation->id }})">Delete</x-bladewind::button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </x-bladewind::table>
    </div>

    
    <x-bladewind::modal
        size="large"
        name="create-reservation"
        title="Create Reservation"
        ok_button_label="Create"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('create-reservation-form').submit();"
    >
        <div id="reservation-details-create">

        </div>
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="edit-reservation"
        title="Edit Reservation"
        ok_button_label="Update"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('edit-reservation-form').submit();"
    >
    <div id="reservation-details-edit">
        <!-- Reservation details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="view-reservation"
        title="View Reservation"
        ok_button_label=""
        cancel_button_label="Back"
    >
    <div id="reservation-details-view">
        <!-- Reservation details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="delete-reservation"
        title="Delete Reservation"
        ok_button_label="Delete"
        cancel_button_label="Back"
        ok_button_action="document.getElementById('delete-reservation-form').submit();"
    >
        <div id="reservation-details-delete">
            <!-- Reservation details will be loaded here -->
        </div>    
    </x-bladewind::modal>

    <script>
        function editReservation(id) {
            fetch(`/reservations/details`)
                .then(response => response.json())
                .then(data => {
                    fetch(`/reservations/${id}/edit`)
                        .then(response => response.json())
                        .then(reservation => {
                            let guestOptions = data.guest.map(guest => `<option value="${guest.id}" ${guest.id === reservation.guest_id ? 'selected' : ''}>${guest.name}</option>`).join('');
                            let roomOptions = data.room.map(room => `<option value="${room.id}" ${room.id === reservation.room_id ? 'selected' : ''}>${room.number}</option>`).join('');
                            let details = `
                                <form id="edit-reservation-form" action="{{ route('reservations.update', '') }}/${id}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="${reservation.id}">
                                    <div class="mb-4">
                                        <label for="guest" class="block text-sm font-medium text-gray-700">Guest Name</label>
                                        <select id="guest" name="guest_id" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option disabled selected>Select Guest</option>
                                            ${guestOptions}
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="room" class="block text-sm font-medium text-gray-700">Room Number</label>
                                        <select id="room" name="room_id" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option disabled selected>Select Room</option>
                                            ${roomOptions}
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                                        <input type="date" name="check_in" id="check_in" value="${reservation.check_in}" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <div class="w-full">
                                        <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out Date</label>
                                        <input type="date" name="check_out" id="check_out" value="${reservation.check_out}" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </form>
                            `;

                            document.getElementById('reservation-details-edit').innerHTML = details;
                            showModal('edit-reservation');
                        })
                        .catch(error => console.error('Error fetching reservation details:', error));
                })
                .catch(error => console.error('Error fetching details:', error));
        }

        function viewReservation(id) {
            fetch(`reservations/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    let details = `
                            <input type="hidden" name="id" value="${data.reservation.id}">
                            <div class="mb-4">
                                <label for="guest" class="block text-sm font-medium text-gray-700">Guest Name</label>
                                <input type="text" name="guest" id="guest" value="${data.guest.name}" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                            <div class="mb-4">
                                <label for="room" class="block text-sm font-medium text-gray-700">Room Number</label>
                                <input type="number" name="room" id="room" value="${data.room.number}" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                            <div class="mb-4">
                                <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                                <input type="date" name="check_in" id="check_in" value="${data.reservation.check_in}" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                            <div class="w-full">
                                <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out Date</label>
                                <input type="date" name="check_out" id="check_out" value="${data.reservation.check_out}" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                    `;
                    document.getElementById('reservation-details-view').innerHTML = details;
                    showModal('view-reservation');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function deleteReservation(id) {
            let details = `
                <form id="delete-reservation-form" action="/reservations/${id}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" value="${id}" name="id">
                </form>
                <p>Are you sure you want to delete this reservation?</p> 
            `;
            document.getElementById('reservation-details-delete').innerHTML = details;
            showModal('delete-reservation');
        }

        function createReservation(id) {
            fetch(`/reservations/details`)
                .then(response => response.json())
                .then(data => {
                    let guestOptions = data.guest.map(guest => `<option value="${guest.id}">${guest.name}</option>`).join('');
                    let roomOptions = data.room.map(room => `<option value="${room.id}">${room.number}</option>`).join('');
                    let details = `
                        <form id="create-reservation-form" action="{{ route('reservations.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="guest" class="block text-sm font-medium text-gray-700">Guest Name</label>
                                <select id="guest" name="guest_id" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option disabled selected>Select Guest</option>
                                    ${guestOptions}
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="room" class="block text-sm font-medium text-gray-700">Room Number</label>
                                <select id="room" name="room_id" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option disabled selected>Select Room</option>
                                    ${roomOptions}
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                                <input type="date" name="check_in" id="check_in" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="w-full">
                                <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out Date</label>
                                <input type="date" name="check_out" id="check_out" required class="mt-1 block w-full rounded-md breservation-gray-300 shadow-sm focus:breservation-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </form>
                    `;
                    document.getElementById('reservation-details-create').innerHTML = details;
                    showModal('create-reservation');
                })
                .catch(error => console.error('Error fetching details:', error));
        }
    </script>
</x-app-layout>