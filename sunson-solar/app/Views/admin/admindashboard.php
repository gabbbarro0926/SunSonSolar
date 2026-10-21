<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sun Son Solar | Admin Portal</title>

  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- We will use a separate CSS file for the admin to keep things clean -->
  <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="admin-body">

  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-logo">
      <img src="<?= base_url('assets/images/logo.png') ?>" alt="Sun Son Solar">
      <span>ADMIN</span>
    </div>
    
    <nav class="admin-menu">
      <a href="#" class="active" data-target="admin-overview"><i class="bi bi-speedometer2"></i> Overview</a>
      <a href="#" data-target="admin-users"><i class="bi bi-people"></i> User Management</a>
      <a href="#" data-target="admin-schedule"><i class="bi bi-calendar3"></i> Schedule</a>
      <a href="#" data-target="admin-reports"><i class="bi bi-bar-chart"></i> Reports</a>
      <a href="#" data-target="admin-settings"><i class="bi bi-gear"></i> Settings</a>
    </nav>

    <div class="admin-logout">
      <a href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="admin-main">
    
    <!-- Header -->
    <header class="admin-header">
      <h1>Admin Dashboard</h1>
      <div class="admin-profile">
        <span>Sol Solis (IT Head)</span>
        <div class="avatar">SS</div>
      </div>
    </header>

    <!-- Content Area -->
    <div class="admin-content">
      
      <!-- VIEW: OVERVIEW -->
      <div id="admin-overview" class="admin-view active">
        <div class="admin-stats-grid">
          <div class="admin-stat-card">
            <h3>Total Revenue</h3>
            <h2>$24,500</h2>
            <span class="trend up"><i class="bi bi-arrow-up-right"></i> 12% this month</span>
          </div>
          <div class="admin-stat-card">
            <h3>Active Bookings</h3>
            <h2>18</h2>
            <span class="trend up"><i class="bi bi-arrow-up-right"></i> 4 new today</span>
          </div>
          <div class="admin-stat-card">
            <h3>Total Customers</h3>
            <h2>142</h2>
            <span class="trend neutral">+2 this week</span>
          </div>
        </div>

        <!-- Placeholder for a Chart -->
        <div class="admin-card">
          <h3>Revenue Overview (Sample Data)</h3>
          <div class="chart-placeholder">
            <!-- In a real app, you would use Chart.js here -->
            <div class="bar" style="height: 40%"></div>
            <div class="bar" style="height: 60%"></div>
            <div class="bar" style="height: 35%"></div>
            <div class="bar" style="height: 80%"></div>
            <div class="bar" style="height: 55%"></div>
            <div class="bar" style="height: 90%"></div>
          </div>
        </div>
      </div>

      <!-- VIEW: USER MANAGEMENT -->
      <div id="admin-users" class="admin-view" style="display: none;">
        <div class="view-header">
          <h2>User Management</h2>
          <button class="admin-btn-primary"><i class="bi bi-plus"></i> Add Employee</button>
        </div>
        
        <div class="admin-card">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Katherine Sinagaraw</td>
                <td><span class="badge admin">Admin</span></td>
                <td>katherine@sunsonsolar.com</td>
                <td><span class="text-green">Active</span></td>
                <td><button class="btn-icon"><i class="bi bi-pencil"></i></button></td>
              </tr>
              <tr>
                <td>Apollo Santos</td>
                <td><span class="badge employee">Employee</span></td>
                <td>apollo@sunsonsolar.com</td>
                <td><span class="text-green">Active</span></td>
                <td>
                  <button class="btn-icon"><i class="bi bi-pencil"></i></button>
                  <button class="btn-icon text-red"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
              <tr>
                <td>Maya Chen</td>
                <td><span class="badge customer">Customer</span></td>
                <td>maya@example.com</td>
                <td><span class="text-green">Active</span></td>
                <td>
                  <button class="btn-icon"><i class="bi bi-pencil"></i></button>
                  <button class="btn-icon text-red"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

  <script>
    // Admin Tab Switching
    document.addEventListener('DOMContentLoaded', function() {
      const menuLinks = document.querySelectorAll('.admin-menu a');
      const views = document.querySelectorAll('.admin-view');

      menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const targetId = this.getAttribute('data-target');
          if (!targetId) return;

          menuLinks.forEach(l => l.classList.remove('active'));
          this.classList.add('active');

          views.forEach(v => v.style.display = 'none');
          
          const targetView = document.getElementById(targetId);
          if (targetView) targetView.style.display = 'block';
        });
      });
    });
  </script>
</body>
</html>