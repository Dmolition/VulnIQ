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

    input[type="text"], textarea, select {
        width: 50%;
        padding: 10px;
        margin: 5px 0 15px;
        background-color: #34495e;
        color: #ecf0f1;
        border: 1px solid #7f8c8d;
        border-radius: 5px;
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
    <h1>SQL Injection Simulation (sqlmap)</h1>

    <form action="{{ url('simulatesqlmap') }}" method="POST">
        @csrf

        <div class="div_deg">
            <label>Target URL</label>
            <input type="text" name="target_url" placeholder="e.g., http://example.com/index.php?id=1" required>
        </div>

        <div class="div_deg">
            <label>Additional Options</label>
            <input type="text" name="options" placeholder="e.g., --batch --dbs">
        </div>

        <div class="div_deg">
            <button type="submit" class="btn btn-danger">Start SQLMap Scan</button>
        </div>
    </form>

    @if(session('output'))
    <div class="div_deg" style="margin-top: 30px; color: #ecf0f1;">
        <h4>Scan Output:</h4>
        <pre>{{ session('output') }}</pre>
    </div>
    @endif
</div>

@include('admin.footer')
</body>
</html>
