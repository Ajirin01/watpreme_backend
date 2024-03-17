<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
</head>
<body>
  <h1>Pusher Test</h1>
  <p>
    Publish an event to channel <code>my-channel</code>
    with event name <code>my-event</code>; it will appear below:
  </p>
  <div id="app">
    <ul id="messages-list">
    </ul>
  </div>

  <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
  <script>
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
      cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
      encrypted: true
    });

    var channel = pusher.subscribe('whatsapp-events');
    channel.bind('message-received', function(data) {
      var messageList = document.getElementById('messages-list');
      var listItem = document.createElement('li');
      listItem.appendChild(document.createTextNode(JSON.stringify(data)));
      messageList.appendChild(listItem);

      console.log(data)
    });
  </script>
</body>
