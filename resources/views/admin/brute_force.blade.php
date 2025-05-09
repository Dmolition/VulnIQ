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
            max-height: 300px;
            overflow-y: auto;
            background-color: #2c3e50;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

@include('admin.nav')
@include('admin.sidebar')

<div class="form-container">
    <h1>Brute Force Attack Simulation</h1>

    <form action="{{ url('add_scan') }}" method="get">
        @csrf
        <div class="div_deg">
            <label>Choose Test User</label>
            <select id="userSelect" name="username">
                <option value="" disabled selected>Select a test user</option>
                <option value="admin|04237">admin (password: 04237)</option>
                <option value="user1|99999">user1 (password: 99999)</option>
                <option value="guest|00000">guest (password: 00000)</option>
            </select>
        </div>

        <div class="div_deg">
            <label>Brute Force Start</label>
            <button type="button" class="btn btn-warning" onclick="simulateBruteForce()">Start Brute Force Attack</button>
        </div>

        
    </form>

    <div id="output">
        <!-- Results will appear here -->
    </div>
</div>

<script>
function padNumber(num, length) {
    return num.toString().padStart(length, '0');
}

async function simulateBruteForce() {
    const userSelect = document.getElementById("userSelect");
    const outputDiv = document.getElementById("output");

    if (!userSelect.value) {
        alert("Please select a test user.");
        return;
    }

    const [username, testPassword] = userSelect.value.split("|");

    outputDiv.innerHTML = `<strong>Simulating brute force attack on user:</strong> ${username}<br><br>`;
    outputDiv.innerHTML += `Generating all 5-digit numeric passwords...<br><br>`;

    const total = 100000;

    for (let i = 0; i < total; i++) {
        const password = padNumber(i, 5);
        outputDiv.innerHTML += `Trying password: ${password}<br>`;
        outputDiv.scrollTop = outputDiv.scrollHeight;

        if (password === testPassword) {
            outputDiv.innerHTML += `<br><strong style="color: lightgreen;">Password found: ${password}</strong>`;
            return;
        }

        await new Promise(resolve => setTimeout(resolve, 5)); // simulate delay
    }

    outputDiv.innerHTML += "<br><strong style='color: red;'>Brute force simulation completed. Password not found.</strong>";
}
</script>

@include('admin.footer')

</body>
</html>
