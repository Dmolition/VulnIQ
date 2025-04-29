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
        max-width: 800px;  /* Set a maximum width for the form */
        margin: 50px auto;  /* Center the form horizontally and add some top margin */
        padding: 20px;  /* Add some padding around the form */
          /* Optional: Background color for visibility */
        border-radius: 8px;  /* Optional: Rounded corners for better look */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);  /* Optional: Add some shadow */
        position: relative;  /* Ensure the form stays in front */
        z-index: 1;  /* Ensure it is above any elements with a lower z-index */
        margin-top: 140px;  /* Increased margin-top to push the form further down */
    }

    /* Input text styles */
    input[type="text"], textarea, select, input[type="file"] {
        width: 50%;  /* Make inputs take full width */
        padding: 10px;  /* Add padding inside input fields */
        margin: 5px 0 15px;  /* Add some space between fields */
        background-color: #34495e;  /* Dark background for the inputs */
        color: #ecf0f1;  /* Light text color for visibility */
        border: 1px solid #7f8c8d;  /* Light border */
        border-radius: 5px;  /* Optional: Rounded corners for input fields */
    }

    input[type="text"]:focus, textarea:focus, select:focus, input[type="file"]:focus {
        border-color: #1abc9c;  /* Focus border color */
        outline: none;  /* Remove default outline */
    }

    .div_deg{

        padding-top: 20px;
    
    }
</style>



</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

@include('admin.nav')
  
  <!-- /.navbar -->
  @include('admin.sidebar')
  
<div class="form-container">

<form action="{{ url('add_scan') }}" method="get">
    @csrf

    <div class="div_deg">
        <label>Scan Name</label>
        <input type="text" name="title">
    </div>

    <div class="div_deg">
        <label>Description</label>
        <input type="text" name="description" style="font-size: 18px; height: 80px; padding: 10px;">
    </div>

    <div class="div_deg">
        <label>Scan Type</label>
        <select name="scan_type" class="form-control" required>
            <option value="full">Full Scan</option>
            <option value="quick">Host Discovery</option>
            <option value="tls">TLS Security</option>
            <option value="ai">AI Checker</option>
        </select>
    </div>

    <div class="div_deg">
        <label>Target</label>
        <input type="text" placeholder="Ex: 192.168.1.1-192.168.1.5,test.com"name="target" >
    </div>
              


    <div class="div_deg">
        <input class="btn btn-primary" type="submit" value="Add Scan">
    </div>

</form>




  @include('admin.footer')

</body>
</html>
