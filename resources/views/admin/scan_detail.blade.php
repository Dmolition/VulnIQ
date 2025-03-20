<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css') <!-- Include CSS file -->
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

    @include('admin.footer') <!-- Footer -->
</body>
</html>
