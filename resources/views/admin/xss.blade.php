<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <style>
        .form-container {
            max-width: 800px;
            margin: 140px auto 50px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        input[type="text"], textarea {
            width: 70%;
            padding: 10px;
            background-color: #34495e;
            color: #ecf0f1;
            border: 1px solid #7f8c8d;
            border-radius: 5px;
        }

        label {
            display: inline-block;
            width: 200px;
        }
    </style>
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
@include('admin.nav')
@include('admin.sidebar')

<div class="form-container">
    <h2>XSS Simulation</h2>
    <form method="POST" action="{{ url('simulate_xss') }}">
        @csrf
        <div>
            <label>Enter Name or Script</label>
            <input type="text" name="user_input" placeholder='e.g. <script>alert("XSS")</script>'>
        </div>
        <div style="margin-top: 20px;">
            <input class="btn btn-warning" type="submit" value="Submit Input">
        </div>
    </form>

    @if(session('result'))
        <div style="margin-top: 30px;">
            <h4>Output:</h4>
            <div style="padding: 15px; background-color: #2c3e50;">
                {!! session('result') !!}
            </div>
        </div>
    @endif
</div>

@include('admin.footer')
</body>
</html>
