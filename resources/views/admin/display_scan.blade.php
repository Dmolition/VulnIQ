<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.css')
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    @include('admin.nav') <!-- Navbar -->

    @include('admin.sidebar') <!-- Sidebar -->

 <!-- Main content -->
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8"> <!-- Adjust the size of the card container -->
            <div class="card" style="margin-top: 150px; margin-left: 130px;"> <!-- Add margin-top here -->
                <div class="card-header">
                    <h3 class="card-title">Scans</h3>
                    <div class="card-tools">
                        <ul class="pagination pagination-sm float-right">
                            <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body p-0" >
                    <table class="table" >
                        <thead>
                            <tr>
                                <th>Scan Name</th>
                                <th>Description</th>
                                <th>Scan Type</th>
                                <th>Last Scanned</th>
                                <th>Progress</th>
                                <th style="width: 40px">Label</th>
                                <th>Delete</th>
                                
                            </tr>
                        </thead>
                        <tbody>

                        @foreach($data as $data)
                            <tr>
                                <td><a href="{{url('scan_detail')}}">{{$data->scan_title}}</a></td>
                                <td>{{$data->description}}</td>
                                <td> {{$data->scan_type}}</td>
                                <td>{{$data->updated_at}}</td>
                                
                                <td>
                                    <div class="progress progress-xs">
                                        <div class="progress-bar progress-bar-danger" style="width: 55%"></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-danger">55%</span></td>
                                <td><a onclick="return confirm('Are you sure you want to delete?');" class="btn btn-danger"
                                 href="{{url('scan_delete',$data->id)}}"> Delete </a> </td>
                                
                            </tr>
                        @endforeach
                            
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

    <!-- End Main content -->

    @include('admin.footer') <!-- Footer -->

    <!-- Add any additional scripts if needed -->
</body>

</html>
