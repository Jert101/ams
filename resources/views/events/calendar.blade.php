@extends('layouts.main', ['title' => 'Event Calendar'])

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white card-header-flex">
                <h5 class="mb-0">Event Calendar</h5>
                <div>
                    <button class="btn btn-sm btn-primary" id="newEventBtn">
                        <i class="bi bi-plus-circle"></i> New Event
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <div class="mb-3">
                        <label for="eventTitle" class="form-label">Event Title</label>
                        <input type="text" class="form-control" id="eventTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventDate" class="form-label">Date</label>
                        <input type="date" class="form-control" id="eventDate" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="eventStartTime" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="eventStartTime" required>
                        </div>
                        <div class="col">
                            <label for="eventEndTime" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="eventEndTime" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="eventType" class="form-label">Event Type</label>
                        <select class="form-select" id="eventType">
                            <option value="regular">Regular Mass</option>
                            <option value="special">Special Mass</option>
                            <option value="training">Training</option>
                            <option value="meeting">Meeting</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEventBtn">Save Event</button>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 id="detailTitle" class="mb-3"></h4>
                <div class="mb-3">
                    <strong><i class="bi bi-calendar"></i> Date:</strong>
                    <span id="detailDate"></span>
                </div>
                <div class="mb-3">
                    <strong><i class="bi bi-clock"></i> Time:</strong>
                    <span id="detailTime"></span>
                </div>
                <div class="mb-3">
                    <strong><i class="bi bi-tag"></i> Type:</strong>
                    <span id="detailType"></span>
                </div>
                <div class="mb-3">
                    <strong><i class="bi bi-info-circle"></i> Description:</strong>
                    <p id="detailDescription" class="mt-2"></p>
                </div>
                <div class="mb-0">
                    <strong><i class="bi bi-people"></i> Attendance:</strong>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">85%</div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small>38 present</small>
                        <small>42 total</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editEventBtn">Edit</button>
                <button type="button" class="btn btn-danger" id="deleteEventBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sample event data (in a real app, this would come from your database)
        const events = [
            {
                id: '1',
                title: 'Sunday Mass',
                start: '2023-06-11T08:00:00',
                end: '2023-06-11T09:30:00',
                color: '#b91c1c',
                type: 'regular',
                description: 'Regular Sunday Mass'
            },
            {
                id: '2',
                title: 'Training Session',
                start: '2023-06-15T15:00:00',
                end: '2023-06-15T17:00:00',
                color: '#047857',
                type: 'training',
                description: 'Training for new altar servers'
            },
            {
                id: '3',
                title: 'Special Mass',
                start: '2023-06-20T10:00:00',
                end: '2023-06-20T11:30:00',
                color: '#d97706',
                type: 'special',
                description: 'Special Mass for feast day'
            },
            {
                id: '4',
                title: 'Monthly Meeting',
                start: '2023-06-25T14:00:00',
                end: '2023-06-25T15:00:00',
                color: '#2563eb',
                type: 'meeting',
                description: 'Monthly coordination meeting for all servers'
            }
        ];
        
        // Initialize FullCalendar
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: events,
            eventClick: function(info) {
                showEventDetails(info.event);
            },
            editable: true,
            selectable: true,
            select: function(info) {
                showAddEventModal(info.startStr);
            }
        });
        
        calendar.render();
        
        // Show event details modal
        function showEventDetails(event) {
            const typeLabels = {
                'regular': 'Regular Mass',
                'special': 'Special Mass',
                'training': 'Training Session',
                'meeting': 'Meeting'
            };
            
            document.getElementById('detailTitle').textContent = event.title;
            document.getElementById('detailDate').textContent = new Date(event.start).toLocaleDateString();
            document.getElementById('detailTime').textContent = `${new Date(event.start).toLocaleTimeString()} - ${new Date(event.end).toLocaleTimeString()}`;
            document.getElementById('detailType').textContent = typeLabels[event.extendedProps.type] || event.extendedProps.type;
            document.getElementById('detailDescription').textContent = event.extendedProps.description || 'No description provided.';
            
            const eventDetailsModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
            eventDetailsModal.show();
            
            // Set up delete button
            document.getElementById('deleteEventBtn').onclick = function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b91c1c',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        event.remove();
                        eventDetailsModal.hide();
                        
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your event has been deleted.',
                            icon: 'success',
                            confirmButtonColor: '#b91c1c'
                        });
                    }
                });
            };
        }
        
        // Show add event modal
        function showAddEventModal(startDate) {
            // Clear form
            document.getElementById('eventForm').reset();
            
            // Set default date
            document.getElementById('eventDate').value = startDate.split('T')[0];
            
            const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            eventModal.show();
        }
        
        // New event button click
        document.getElementById('newEventBtn').addEventListener('click', function() {
            const today = new Date().toISOString().split('T')[0];
            showAddEventModal(today);
        });
        
        // Save event button click
        document.getElementById('saveEventBtn').addEventListener('click', function() {
            const title = document.getElementById('eventTitle').value;
            const date = document.getElementById('eventDate').value;
            const startTime = document.getElementById('eventStartTime').value;
            const endTime = document.getElementById('eventEndTime').value;
            const type = document.getElementById('eventType').value;
            const description = document.getElementById('eventDescription').value;
            
            if (!title || !date || !startTime || !endTime) {
                alert('Please fill in all required fields.');
                return;
            }
            
            // Event colors based on type
            const colors = {
                'regular': '#b91c1c',
                'special': '#d97706',
                'training': '#047857',
                'meeting': '#2563eb'
            };
            
            const newEvent = {
                id: Math.random().toString(36).substr(2, 9),
                title: title,
                start: `${date}T${startTime}`,
                end: `${date}T${endTime}`,
                color: colors[type] || '#b91c1c',
                type: type,
                description: description
            };
            
            calendar.addEvent(newEvent);
            
            // In a real app, you would save this to your database here
            
            // Close the modal
            const eventModal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
            eventModal.hide();
            
            // Show success notification
            Swal.fire({
                title: 'Event Saved!',
                text: 'Your event has been added to the calendar.',
                icon: 'success',
                confirmButtonColor: '#b91c1c'
            });
        });
    });
</script>
@endpush 