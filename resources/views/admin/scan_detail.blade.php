<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
          <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="admin/plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="admin/plugins/datatables-buttons/css/buttons.bootstrap4.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="admin\dist\css\adminlte.min.css">
    <title>VulnIQ</title>
    @include('admin.css') <!-- Your custom admin CSS -->

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .chart-container {
            width: 100%;
            height: calc(100vh - 200px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            gap: 3rem;
        }

        .chart-box {
            width: 350px;
            height: 350px;
        }

        .chart-title {
            text-align: center;
            color: #fff;
            margin-bottom: 1rem;
        }

        .center-btn {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }

        canvas + div ul li span {
    color: #ffffff !important;
        }

        .chart-container {
    width: 100%;
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
    padding: 2rem;
    gap: 3rem;
    margin-top: 80px;  /* Move it down a bit to avoid the navbar */
}

.scan-info-box {
    width: 350px;
    height: 380px;
    background-color: #343a40;
    padding: 2rem;
    border-radius: 10px;
    color: #fff;
    margin-left: 240px;  /* Push it right from the sidebar */
}

.scan-info-box h5 {
    text-align: center;
    color: #fff;
    margin-bottom: 1rem;
}

.scan-info-box table th, 
.scan-info-box table td {
    color: #fff;
}

.charts-box {
    display: flex;
    flex-direction: row;
    gap: 2rem;
    align-items: flex-start;
}

.chart-box {
    width: 350px;
    height: 350px;
}

.chart-title {
    text-align: center;
    color: #fff;
    margin-bottom: 1rem;
}

.risk-bar {
    gap: 1rem;
    flex-wrap: wrap;
    padding-right: 3rem;
}

.risk-box {
    flex: 1;
    min-width: 160px;
    max-width: 200px;
    padding: 1rem;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    transition: transform 0.2s ease;
}

.risk-box:hover {
    transform: translateY(-4px);
}



    </style>
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    @include('admin.nav')
    @include('admin.sidebar')

    <div class="chart-container">
    <!-- Scan Info Table on the left -->
    <div class="scan-info-box">
        <h5 class="chart-title">Scan Information</h5>
        <table class="table table-bordered table-sm table-dark">
            <tbody>
                <tr>
                    <th>Start Time</th>
                    <td>{{ $scanInfo['start_time'] ?? '2025-05-01 10:00' }}</td>
                </tr>
                <tr>
                    <th>Scan Type</th>
                    <td>{{ $scanInfo['type'] ?? 'Full' }}</td>
                </tr>
                <tr>
                    <th>Scan Protocol</th>
                    <td>{{ $scanInfo['protocol'] ?? 'TCP' }}</td>
                </tr>
                <tr>
                    <th>Command</th>
                    <td>{{ $scanInfo['command'] ?? 'nmap -A 192.168.1.1' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Two Donut Charts Side-by-Side on the right -->
    <div class="charts-box">
        <div class="chart-box">
            <h5 class="chart-title">Vulnerabilities</h5>
            <canvas id="donutChartVuln"></canvas>
        </div>
        <div class="chart-box">
            <h5 class="chart-title">Ports</h5>
            <canvas id="donutChartPorts"></canvas>
        </div>
    </div>
</div>


<div class="risk-bar d-flex justify-content-between align-items-center mt-4 text-white">
    <div class="risk-box bg-danger">
        <i class="fas fa-fire fa-2x mb-1"></i>
        <span class="badge badge-light text-dark">Critical</span>
        <div class="mt-1">{{ $summary['critical'] ?? 4 }} Hosts</div>
    </div>
    <div class="risk-box bg-warning text-dark">
        <i class="fas fa-exclamation-triangle fa-2x mb-1"></i>
        <span class="badge badge-dark">High</span>
        <div class="mt-1">{{ $summary['high'] ?? 6 }} Hosts</div>
    </div>
    <div class="risk-box bg-info">
        <i class="fas fa-info-circle fa-2x mb-1"></i>
        <span class="badge badge-light text-dark">Medium</span>
        <div class="mt-1">{{ $summary['medium'] ?? 10 }} Hosts</div>
    </div>
    <div class="risk-box bg-success">
        <i class="fas fa-check-circle fa-2x mb-1"></i>
        <span class="badge badge-light text-dark">Low</span>
        <div class="mt-1">{{ $summary['low'] ?? 12 }} Hosts</div>
    </div>
    <div class="risk-box bg-secondary">
        <i class="fas fa-shield-alt fa-2x mb-1"></i>
        <span class="badge badge-light text-dark">None</span>
        <div class="mt-1">{{ $summary['none'] ?? 5 }} Hosts</div>
    </div>
</div>


    

    <!-- Chart Scripts -->
    <script>
        // Donut chart for Vulnerabilities
        const ctxVuln = document.getElementById('donutChartVuln').getContext('2d');
        new Chart(ctxVuln, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartLabels ?? ['Critical', 'High', 'Medium', 'Low']) !!},
                datasets: [{
                    data: {!! json_encode($chartData ?? [10, 20, 30, 40]) !!},
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#3c8dbc']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#fff'
                        }
                    }
                }
            }
        });

        // Donut chart for Ports
        const ctxPorts = document.getElementById('donutChartPorts').getContext('2d');
        new Chart(ctxPorts, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($portLabels ?? ['Open', 'Closed', 'Filtered']) !!},
                datasets: [{
                    data: {!! json_encode($portData ?? [5, 8, 2]) !!},
                    backgroundColor: ['#17a2b8', '#6c757d', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#fff'
                        }
                    }
                }
            }
        });
    </script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Scan Results</h1>
          </div>
        
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
           
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Scan Results with Report Generation</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
      <th>Port</th>
      <th>State</th>
      <th>Service</th>
      <th>Version</th>
      <th>Vulnerability</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>21/tcp</td>
      <td>open</td>
      <td>ftp</td>
      <td>vsftpd 2.3.4</td>
      <td>Backdoor Command Execution</td>
    </tr>
    <tr>
      <td>22/tcp</td>
      <td>open</td>
      <td>ssh</td>
      <td>OpenSSH 4.7p1 Debian 8ubuntu1</td>
      <td>Weak SSH Configuration</td>
    </tr>
    <tr>
      <td>23/tcp</td>
      <td>open</td>
      <td>telnet</td>
      <td>Linux telnetd</td>
      <td>Plaintext Credentials</td>
    </tr>
    <tr>
      <td>25/tcp</td>
      <td>open</td>
      <td>smtp</td>
      <td>Postfix smtpd</td>
      <td>SMTP User Enumeration</td>
    </tr>
    <tr>
      <td>80/tcp</td>
      <td>open</td>
      <td>http</td>
      <td>Apache httpd 2.2.8</td>
      <td>Multiple Web Vulnerabilities</td>
    </tr>
    <tr>
      <td>139/tcp</td>
      <td>open</td>
      <td>netbios-ssn</td>
      <td>Samba smbd 3.X</td>
      <td>SMB Enumeration</td>
    </tr>
    <tr>
      <td>445/tcp</td>
      <td>open</td>
      <td>microsoft-ds</td>
      <td>Samba smbd 3.X</td>
      <td>SMB Vulnerabilities</td>
    </tr>
    <tr>
      <td>3306/tcp</td>
      <td>open</td>
      <td>mysql</td>
      <td>MySQL 5.0.51a-3ubuntu5</td>
      <td>Weak Credentials</td>
    </tr>
    <tr>
      <td>5432/tcp</td>
      <td>open</td>
      <td>postgresql</td>
      <td>PostgreSQL DB</td>
      <td>Default Credentials</td>
    </tr>
    <tr>
      <td>5900/tcp</td>
      <td>open</td>
      <td>vnc</td>
      <td>VNC protocol</td>
      <td>No Authentication</td>
    </tr>
    <tr>
      <td>8180/tcp</td>
      <td>open</td>
      <td>http</td>
      <td>Apache Tomcat/Coyote JSP engine 1.1</td>
      <td>Tomcat Manager Access</td>
    </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @include('admin.footer')

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="admin/plugins/jszip/jszip.min.js"></script>
<script src="admin/plugins/pdfmake/pdfmake.min.js"></script>
<script src="admin/plugins/pdfmake/vfs_fonts.js"></script>
<script src="admin/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="admin/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="admin/dist/js/demo.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      buttons: [
        {
          extend: 'copy',
          title: 'Scan Results',
          filename: 'scan_results_copy'
        },
        {
          extend: 'csv',
          title: 'Scan Results',
          filename: 'scan_results_csv'
        },
        {
          extend: 'excel',
          title: 'Scan Results',
          filename: 'scan_results_excel'
        },
        {
          extend: 'pdf',
          title: 'Scan Results',
          filename: 'scan_results_pdf'
        },
        {
          extend: 'print',
          title: 'Scan Results'
        },
        {
          text: 'AI Report',
          action: function () {
            const link = document.createElement('a');
            link.href = '/admin/scans/nist_risk_report.pdf'; // Adjust path as needed
            link.download = 'ai_recommendation_report.pdf';
            link.click();
          }
        },
        'colvis'
      ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $('#example2').DataTable({
      paging: true,
      lengthChange: false,
      searching: false,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true
    });
  });
</script>



</body>
</html>
