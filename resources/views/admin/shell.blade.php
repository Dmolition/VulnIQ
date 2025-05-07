<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')

    <style type="text/css">
        label {
            display: inline-block;
            width: 200px;
        }

        .form-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
            margin-top: 140px;
        }

        input[type="text"], select {
            width: 50%;
            padding: 10px;
            margin: 5px 0 15px;
            background-color: #34495e;
            color: #ecf0f1;
            border: 1px solid #7f8c8d;
            border-radius: 5px;
        }

        input[type="text"]:focus, select:focus {
            border-color: #1abc9c;
            outline: none;
        }

        .div_deg {
            padding-top: 20px;
        }

        #output {
            margin-top: 30px;
            font-size: 16px;
            color: #ecf0f1;
            max-height: 400px;
            overflow-y: auto;
            background-color: #2c3e50;
            padding: 15px;
            border-radius: 5px;
            white-space: pre-wrap; /* Preserve formatting */
        }
    </style>
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

@include('admin.nav')
@include('admin.sidebar')

<div class="form-container">
    <h1>Admin Shell</h1>
    <p class="text-warning"><strong>SECURITY WARNING:</strong> Use this tool with extreme caution. Unauthorized or malicious commands can severely compromise your system.</p>

    <form id="shellForm">
        @csrf
        <div class="div_deg">
            <label for="command">Enter Command:</label>
            <input type="text" class="form-control" id="command" name="command" placeholder="e.g., nmap -v localhost or nikto -h http://example.com">
            <small class="form-text text-muted">Only whitelisted commands are allowed. Input is sanitized, but use with caution.</small>
        </div>

        <div class="div_deg">
            <button type="submit" class="btn btn-danger">Execute Command</button>
        </div>
    </form>

    <div id="output">
    </div>
</div>

<script>
    document.getElementById('shellForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const commandInput = document.getElementById('command');
        const command = commandInput.value;
        const outputDiv = document.getElementById('output');
        outputDiv.textContent = 'Executing...';

        fetch('/admin/execute-shell-command', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({ command: command }),
        })
        .then(response => response.json())
        .then(data => {
            outputDiv.textContent = data.output;
        })
        .catch(error => {
            outputDiv.textContent = 'Error: ' + error;
        })
        .finally(() => {
            commandInput.value = ''; // Clear the input field after execution
        });
    });
</script>

@include('admin.footer')

</body>
</html>