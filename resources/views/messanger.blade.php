<!DOCTYPE html>
<head>
  <title>Messanger Test</title>

<style>
    #app {
        height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .row {
        height: 100%;
    }

    .col {
        overflow-y: hidden;
        margin: 0 auto;
    }

    /* Style the chat messages */
    .message-container {
        flex-grow: 1;
        overflow-y: auto;
        scrollbar-width: 4px;  /* Firefox */
        -ms-overflow-style: none;  /* Internet Explorer 11 */
    }

    .message-container::-webkit-scrollbar {
        width: 4px;  /* Set the width of the scrollbar */
    }

    .message-container::-webkit-scrollbar-thumb {
        background-color: #888;  /* Color of the scrollbar thumb */
        border-radius: 4px;  /* Round corners of the thumb */
    }


    .user-message {
        background-color: #dcf8c6;
        border-radius: 12px;
        padding: 8px;
        max-width: 70%;
        align-self: flex-end;
        margin-left: 30%;
        margin-bottom: 10px; /* Adjust as needed */
    }

    .bot-message {
        background-color: #fff;
        border-radius: 12px;
        padding: 8px;
        max-width: 70%;
        align-self: flex-start;
        margin-right: 30%;
        margin-bottom: 10px; /* Adjust as needed */
    }

    .inputArea {
        position: fixed;
        bottom: 0;
        width: 100%;
        background-color: #fff;
        padding: 10px;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
    }

    .message-display {
    flex-grow: 1;
    height: 250px;
    }

    .user-text {
        display: flex;
        align-items: flex-start;
        justify-content: flex-end; /* Align user text to the right */
        margin-right: 25px;
        margin-bottom: 10px
    }

    .user-text-content {
        /* white-space: pre-wrap; */
        word-wrap: break-word;
        background-color: #dcf8c6; /* Background color for bot messages */
        border-radius: 12px 0px 12px 12px;
        padding: 8px;
        min-width: 50px;
        max-width: 250px;
        position: relative;
    }

    .bot-text-content {
    /* white-space: pre-wrap; */
        min-width: 50px;
        max-width: 250px;
        word-wrap: break-word;
        margin-left: 10px;
        background-color: #e0e0e0; /* Background color for user messages */
        border-radius: 0px 12px 12px 12px;
        padding: 8px;
        position: relative; /* Set position relative for positioning the shootout */
    }

    .receiver-shoot-out {
    position: absolute;
    top: 0px;
    left: -5px; /* Adjust the distance from the right side */
    width: 10px;
    height: 15px;
    border-radius: 0 0 0 10px; /* Adjust border-radius as needed */
    background-color: #e0e0e0; /* Color of the shootout */
    }

    .sender-shoot-out {
    position: absolute;
    top: 0px;
    right: -6px; /* Adjust the distance from the right side */
    width: 10px;
    height: 15px;
    border-radius: 0 0 10px 0px; /* Adjust border-radius as needed */
    background-color: #dcf8c6; /* Color of the shootout */
    }

    .user-icon {
    margin-top: 5px;
    }

    .bot-text {
        display: flex;
        flex-direction: row;
        justify-content: left;
        justify-self: left;
        margin-bottom: 10px
    }

    .chat-input{
        top: 500px;
        display: flex;
        justify-content: flex-start;
    }

    .input-field{
        width: 100%;
    }

    textarea {
        width: 100%;
        background-color: white !important;
        color: rgb(28, 24, 24);
        border: 1px gray solid;
        border-radius: 50px !important;
        padding-left: 10px !important; /* Adjust the value as needed */
        padding-bottom: 5px
    }


    .send{
        margin-left: 20px;
        transition: .2s ease-in;
        cursor: pointer;
        padding: 13px 10px 0px 15px;
        text-align: center;
        border-radius: 50%;
        margin-bottom: 2px;
    }

    .send:hover{
        opacity: .7;
    }

    .materialize-textarea{
        max-height: 100px;
    }
</style>

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

function sendMessage() {
  var userInput = document.getElementById("userInput").value.trim();
  if (userInput !== "") {
    messages.push({ id: 'user', text: userInput }); // Assuming 'user' as message sender
    displayMessages();
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
        </div>
      `;
    } else {
      messageElement.classList.add("bot-text");
      messageElement.innerHTML = `
        <div class="bot-text-content">
          <span class="receiver-shoot-out"></span>
          ${message.text}
        </div>
      `;
    }
    messageContainer.appendChild(messageElement);
  });
}

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
      cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
      encrypted: true
    });

    var channel = pusher.subscribe('whatsapp-events');
    channel.bind('message-received', function(data) {
        // if(data.message.messages){
        //     if(messages[0].from === '2347036998003'){
        //         messages.push({ id: 'bot', text: data.message.messages[0].text.body }); // Assuming 'bot' as message sender
        //         displayMessages();
        //     }
        // }else if(data.message.metadata.display_phone_number === '15551029236'){
        //     // messages.push({ id: 'user', text: data.message.messages[0].text.body }); // Assuming 'user' as message sender
        //     // displayMessages();
        // }
        

        console.log(data)
    });
  </script>
</body>
