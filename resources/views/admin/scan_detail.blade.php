<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css') <!-- Include CSS file -->

    <style>
       /* Center the button */
        .center-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 500px;
            margin-bottom: 100px;
        }


    </style>
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    @include('admin.nav') <!-- Navbar -->
    @include('admin.sidebar') <!-- Sidebar -->

    <div class="container mt-4">
        <h3 class="text-white">Scan Details</h3>

        @if (isset($output) && count($output) > 0)
            <div class="table-responsive">
                <table class="table table-dark table-bordered">
                    <thead>
                        <tr>
                            <th>Index</th>
                            <th>Scan Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($output as $index => $line)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $line }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-white">No output available or scan failed.</p>
        @endif
    </div>


    <div class="center-btn">
            <a href="assets\images\scan_report_20250205_220059.csv" class="btn btn-primary">Generate Report</a>
        </div>
    </div>


    @include('admin.footer') <!-- Footer -->
</body>
</html>
