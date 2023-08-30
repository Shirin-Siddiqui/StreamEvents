<!DOCTYPE html>
<html>
<head>
    <title>Stream Events</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 50px 0;
        }
        .logout-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .list-group-item {
                border: none;
                border-radius: 8px;
                margin-bottom: 10px;
                padding: 15px;
                background-color: #fff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                transition: background-color 0.3s ease-in-out;
                font-size: 16px;
                line-height: 1.4;
            }
            .list-group-item:hover {
                background-color: #f0f0f0;
            }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Stream Events</h1>
            <a class="logout-btn" href="{{ url('logout') }}">Logout</a>
        </div>
        <ul class="list-group" id="event-list">
                    <!-- Display initial events -->
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            let last = 0;
            let loading = false;

        function loadEvents() {
            if (loading) {
                return;
            }
            loading = true;
            
            $.ajax({
                url: `/events`,
                method: 'GET',
                data: {
                last: last,
                // Add more parameters as needed
            },
                success: function (response) {
                    console.log(response.data);
                    if (response.data.length > 0) {
                        var lastRecord = response.data[response.data.length - 1];
                        last = lastRecord.created_timestamp;

                        console.log("Last created timestamp:", last);
                        
                        renderEvents(response.data);
                    }
                },
                complete: function () {
                    loading = false;
                }
            });
        }

        function renderEvents(events) {
    const eventsList = $('#event-list');
    events.forEach(item => {
        const isReadClass = item.is_read ? 'event-read' : 'event-unread';
        const fontWeight = item.is_read ? 'normal' : 'bold';
        const textColor = item.is_read ? '#333' : '#000';

        let eventContent = '';

                if (item.type === 'donation') {
                    eventContent = `RandomUser donated ${item.eventable.amount} ${item.eventable.currency} to you!`;
                } else if (item.type === 'subscriber') {
                    eventContent = `${item.eventable.name} (${item.eventable.tier}) subscribed to you!`;
                } else if (item.type === 'follower') {
                    eventContent = `${item.eventable.name} followed you!`;
                } else {
                    eventContent = `RandomUser bought some ${item.eventable.item_name} from you for ${item.eventable.amount} ${item.eventable.currency}!`;
                }

                const donationItem = `
                    <li class="list-group-item ${isReadClass}" 
                        style="font-weight: ${fontWeight}; color: ${textColor};"
                        onclick="handleEventClick(${item.id}, $(this))">
                        ${eventContent}
                    </li>
                `;

                eventsList.append(donationItem);
            });
        }

        // Initial load
        loadEvents();

        function handleEventClick(eventID, listItem) {
    console.log('Clicked event id:', eventID);
    // Update event status using AJAX
    $.ajax({
        url: `/events/${eventID}`,
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            console.log('Event status updated:', response.message);
            // Add class and update background color
            listItem.removeClass('event-unread').addClass('event-read');
        },
        error: function (error) {
            console.error('Error updating event status:', error);
        }
    });
}
        // Infinite scrolling
        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                loadEvents();
            }
        });
     
        </script>
</body>
</html>
