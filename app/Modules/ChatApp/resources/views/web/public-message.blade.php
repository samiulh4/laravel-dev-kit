<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Public Chat</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        #messages {
            height: 400px;
            overflow-y: scroll;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 10px;
        }

        .username {
            font-weight: bold;
            color: #3182ce;
        }

        form {
            display: flex;
            gap: 10px;
        }

        input,
        button {
            padding: 10px;
        }

        input {
            flex: 1;
        }

        button {
            background: #3182ce;
            color: white;
            border: none;
            cursor: pointer;
        }

        #status {
            margin-bottom: 10px;
            font-size: 14px;
            color: green;
        }
    </style>
</head>

<body>
    <h1>Public Chat</h1>
    <div id="status">Connecting...</div>
    <div id="messages"></div>
    <form id="chat-form">
        <input type="text" id="username" placeholder="Your name" required>
        <input type="text" id="message" placeholder="Type your message" required>
        <button type="submit">Send</button>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.7/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/8.3.0/pusher.min.js"></script> <!-- Reverb depends on Pusher -->
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>


    {{-- <script>
        // Set CSRF token for axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

        // Initialize Echo with Reverb settings
        const echo = new window.Echo({
            broadcaster: 'reverb',
            key: '{{ env('REVERB_APP_KEY') }}',
            wsHost: window.location.hostname,
            wsPort: 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });

        // Set up channel listener
        echo.channel('chat-public-message')
            .listen('ChatPublicMessage', (data) => {
                const messages = document.getElementById('messages');
                const messageElement = document.createElement('div');
                messageElement.className = 'message';
                messageElement.innerHTML = `<span class="username">${data.username}:</span> ${data.message}`;
                messages.appendChild(messageElement);
                messages.scrollTop = messages.scrollHeight;
            });

        // Display connection status (safe way without .socket)
        const statusElement = document.getElementById('status');
        let connected = false;
        let retryInterval = setInterval(() => {
            if (!connected && echo.connector.pusher && echo.connector.pusher.connection.state === 'connected') {
                connected = true;
                statusElement.textContent = 'Connected to Reverb';
                statusElement.style.color = 'green';
                clearInterval(retryInterval);
            }
        }, 1000);

        // Handle form submission
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value;
            const message = document.getElementById('message').value;

            axios.post("{{ url('/web/chat/public-message/send') }}", {
                username: username,
                message: message
            }).then(response => {
                document.getElementById('message').value = '';
            }).catch(error => {
                console.error(error);
            });
        });
    </script> --}}

    <script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env('REVERB_APP_KEY') }}', // Optional: can be left blank or removed
        wsHost: window.location.hostname,
        wsPort: 8080,
        wssPort: 8080,
        forceTLS: false,
        enabledTransports: ['ws'],
    });

    window.Echo.channel('chat-public-message')
        .listen('ChatPublicMessage', (e) => {
            const messages = document.getElementById('messages');
            const messageElement = document.createElement('div');
            messageElement.className = 'message';
            messageElement.innerHTML = `<span class="username">${e.username}:</span> ${e.message}`;
            messages.appendChild(messageElement);
            messages.scrollTop = messages.scrollHeight;
        });

    // Form handler
    document.getElementById('chat-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const username = document.getElementById('username').value;
        const message = document.getElementById('message').value;

        axios.post("{{ url('/web/chat/public-message/send') }}", {
            username: username,
            message: message
        }).then(response => {
            document.getElementById('message').value = '';
        }).catch(error => {
            console.error(error);
        });
    });

    // Connection status
    const statusElement = document.getElementById('status');
    let connected = false;
    const retryInterval = setInterval(() => {
        if (!connected && window.Echo.connector.pusher?.connection.state === 'connected') {
            connected = true;
            statusElement.textContent = 'Connected to Reverb';
            statusElement.style.color = 'green';
            clearInterval(retryInterval);
        }
    }, 1000);
</script>


</body>

</html>
