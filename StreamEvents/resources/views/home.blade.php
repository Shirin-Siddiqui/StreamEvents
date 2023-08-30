<!DOCTYPE html>
<html>
<head>
    <title>Stream Events</title>
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
            var donationItem = '';
            events.forEach(item => {
               console.log(item);
                if(item.type == 'donation'){
                   donationItem = `<li class="list-group-item">RandomUser donated ${item.eventable.amount} ${item.eventable.currency} to you!</li>`;
                } else if(item.type == 'subscriber') {
                  donationItem = `<li class="list-group-item"> ${item.eventable.name} (${item.eventable.tier}) subscribed to you!</li>`;
                }else if(item.type == 'follower') {
                  donationItem = `<li class="list-group-item"> ${item.eventable.name} followed you!</li>`;
                } else {
                    donationItem = `<li class="list-group-item">RandomUser bought some ${item.eventable.item_name} from you for ${item.eventable.amount} ${item.eventable.currency}!</li>`;
                }
                eventsList.append(donationItem);
            });
        }

        // Initial load
        loadEvents();

        // Infinite scrolling
        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                loadEvents();
            }
        });
     
        </script>
</body>
</html>
