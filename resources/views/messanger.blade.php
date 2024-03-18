<!DOCTYPE html>
<head>
    <title>Messanger Test</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('styles.css') }}">

</head>
<body>
  <h1>Whatsapp Messanger</h1>

  <div id="app">
    <!-- Message container for displaying chat messages -->
    <div class="message-container">
        <div class="message-display" id="message-container">
        </div>
    </div>

    <!-- User input area -->
    <div>
        <div class="chat-input">
            <textarea id="userInput" class="materialize-textarea" placeholder="Type your message..."></textarea>
            <i id="sendButton" class="material-icons teal accent-4 white-text send">send</i>
        </div>
    </div>
  </div>


  <div id="app">
    <ul id="messages-list">
    </ul>
  </div>

  <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
  <script>
    var messages = [];

    document.getElementById("sendButton").addEventListener("click", function() {
        sendMessage();
    });

    document.getElementById("userInput").addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        event.preventDefault(); // Prevent default behavior of Enter key
        sendMessage();
    }
    });

    function generateUniqueId() {
        const timestamp = Date.now().toString(36); // Convert timestamp to base36 string
        const randomString = Math.random().toString(36).substring(2, 7); // Generate random string
        return timestamp + '-' + randomString; // Concatenate timestamp and random string
    }

    function convertTimestampToTime(timestamp) {
        // Create a new Date object with the timestamp (in milliseconds)
        var date = new Date(timestamp * 1000);

        // Extract hours and minutes from the date object
        var hours = date.getHours();
        var minutes = ('0' + date.getMinutes()).slice(-2); // Pad minutes with leading zeros if needed

        // Convert hours to 12-hour format
        var meridian = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // If hours is 0, set it to 12

        // Construct the time string
        var timeString = hours + ':' + minutes + ' ' + meridian;

        return timeString;
    }

    function sendMessage() {
        var userInput = document.getElementById("userInput").value.trim();
        const uniqueId = generateUniqueId()
        if (userInput !== "") {
            var message = { id: 'user', text: userInput, textId: uniqueId, status: 'sending', timestamp: Date.now() }; // Assuming 'user' as message sender and setting initial status to 'sending'

            // Update UI to show sending status
            messages.push(message);
            displayMessages();

            fetch('{{url('')}}/api/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(message),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                // Handle successful response
                console.log('Message sent successfully');
                // Update status to 'sent' when API request returns success
                message.status = 'sent';
                displayMessages();
                response.json().then(res => {
                    console.log(res)
                })
            })
            .catch(error => {
                // Handle error
                console.error('There was a problem sending the message:', error);
                // Update status to 'failed' in case of error
                message.status = 'failed';
                displayMessages();
            });

            // Clear input field
            document.getElementById("userInput").value = "";
        }
    }

    function displayMessages() {
        var messageContainer = document.getElementById("message-container");
        messageContainer.innerHTML = ""; // Clear previous messages
        messages.forEach(function(message) {
            var messageElement = document.createElement("div");
            messageElement.classList.add("message-text");
            if (message.id === 'user') {
                messageElement.classList.add("user-text");
                messageElement.innerHTML = `
                    <div class="user-text-content">
                        <span class="sender-shoot-out"></span>
                        ${message.text}
                        <div class="status-indicator" style="display: flex; justify-content: flex-end; padding-left: 50px; font-size: .5rem; opacity: .7 "><span style="padding-right: 5px"> ${convertTimestampToTime(message.timestamp)} </span> ${getStatusIndicator(message.status)}</div>
                    </div>
                `;
            } else {
                messageElement.classList.add("bot-text");
                messageElement.innerHTML = `
                    <div class="bot-text-content">
                        <span class="receiver-shoot-out"></span>
                        ${message.text}
                        <div class="status-indicator" style="display: flex; justify-content: flex-end; margin-left: 50px; font-size: .5rem; opacity: .7 ">${convertTimestampToTime(message.timestamp)}</div>
                    </div>
                `;
            }
            messageContainer.appendChild(messageElement);
        });
    }

    // Helper function to get status indicator text based on message status
    function getStatusIndicator(status) {
        switch (status) {
            case 'sending':
                return '<i class="material-icons" style="font-size: .7rem">schedule</i>'; // Clock icon for sending
            case 'sent':
                return '<i class="material-icons" style="font-size: .7rem">done_all</i>'; // Check (tick) icon for sent
            case 'failed':
                return '<i class="material-icons" style="color: red; font-size: .7rem">error</i>'; // Red circled exclamation mark for failed
            default:
                return '';
        }
    }

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
      cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
      encrypted: true
    });

    var channel = pusher.subscribe('whatsapp-events');
    channel.bind('message-received', function(data) {
        if(data.message.messages){
            if(data.message.messages[0].from === '2347036998003'){
                messages.push({ id: 'bot', text: data.message.messages[0].text.body, timestamp: data.message?.messages[0].timestamp }); // Assuming 'bot' as message sender
                displayMessages();
            }
        }
        

        console.log(data)
    });
  </script>
</body>
