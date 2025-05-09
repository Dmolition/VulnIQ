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

        input[type="text"], textarea {
            width: 50%;
            padding: 10px;
            margin: 5px 0 15px;
            background-color: #34495e;
            color: #ecf0f1;
            border: 1px solid #7f8c8d;
            border-radius: 5px;
        }

        input[type="text"]:focus, textarea:focus {
            border-color: #1abc9c;
            outline: none;
        }

        .div_deg {
            padding-top: 20px;
        }
    </style>
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

@include('admin.nav')
@include('admin.sidebar')

<div class="form-container">
    <h1>Dictionary Attack Simulation</h1>

    <div class="div_deg">
        <label>Target Username</label>
        <input type="text" id="username" placeholder="Enter Target Username">
    </div>

    <div class="div_deg">
        <label>Dictionary List (comma-separated)</label>
        <textarea id="dictionary" placeholder="Ex: password123,123456,welcome,qwerty,admin123"></textarea>
    </div>

    <div class="div_deg">
        <button type="button" class="btn btn-danger" onclick="simulateDictionaryAttack()">Start Dictionary Attack</button>
    </div>

    <div id="output" style="margin-top: 30px; font-size: 16px; color: #ecf0f1;">
        <!-- Results will appear here -->
    </div>
</div>

<script>
    function simulateDictionaryAttack() {
        const username = document.getElementById('username').value.trim();
        const dictionary = document.getElementById('dictionary').value.split(',').map(word => word.trim());
        const output = document.getElementById('output');

        output.innerHTML = '';
        let found = false;

        dictionary.forEach((password, index) => {
            const attempt = `Attempt ${index + 1}: Trying password "${password}"<br>`;
            output.innerHTML += attempt;

            // Simulated match condition
            if (username === 'admin' && password === 'admin123') {
                output.innerHTML += `<strong style="color: #2ecc71;">Success! Found password: ${password}</strong>`;
                found = true;
                return;
            }

            if (index === dictionary.length - 1 && !found) {
                output.innerHTML += `<strong style="color: #e74c3c;">No valid password found.</strong>`;
            }
        });
    }
</script>

@include('admin.footer')
</body>
</html>
