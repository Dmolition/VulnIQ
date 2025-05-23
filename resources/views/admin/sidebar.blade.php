 <!-- Main Sidebar Container -->
 <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="\home" class="brand-link">
      <img src="admin/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"> VulnIQ </span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="admin/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"> {{ Auth::user()->name }}</a>
          <p style="font-size: 12px;">{{ Auth::user()->email }}</p>
        </div>
</div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="\home" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Top Management</p>
                </a>
              </li>
           
              <li class="nav-item">
                <a href="\home" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Vulneribility Analysis</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                My Scans
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right">6</span>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('create_scan')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Scan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('display_scan')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Scan</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{url('scan_detail')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Scan Details</p>
                </a>
              </li>
             
</ul>
<li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-bomb"></i>
              <p>
                Attack Simulation 
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right">7</span>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('brute_force')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Brute Force</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('xss')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>XSS</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('sql_injection')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sql Injection</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{url('dictionary_attack')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dictionary Attack</p>
                </a>
              </li>
              
             
</ul>

<li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Interactive Shell
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right">6</span>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('shell')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>My Shell</p>
                </a>
              </li>
</ul>
<ul>
          
          <li class="nav-header">LABELS</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-circle text-danger"></i>
              <p class="text">Important</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-circle text-warning"></i>
              <p>Warning</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-circle text-success"></i>
              <p>Informational</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>