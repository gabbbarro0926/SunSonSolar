<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sun Son Solar | Customer Dashboard</title>

  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="dashboard-body">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon"><img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" style="height:32px;"></div>
      <div class="logo-text">
        <h2>Sun Son Solar</h2>
        <span>CUSTOMER PORTAL</span>
      </div>
    </div>
    
    <nav class="sidebar-menu">
      <a href="#" class="active" data-target="view-overview"><i class="bi bi-grid"></i> Dashboard</a>
      <a href="#" data-target="view-schedule"><i class="bi bi-calendar-event"></i> My Schedule</a>
      <a href="#" data-target="view-history"><i class="bi bi-clock-history"></i> Service History</a>
      <a href="#" data-target="view-profile"><i class="bi bi-person"></i> Profile</a>
      <a href="#" data-target="view-support"><i class="bi bi-question-circle"></i> Support</a>
    </nav>

    <!-- Help Box -->
    <div class="sidebar-help">
      <i class="bi bi-headset"></i>
      <h4>Here to help</h4>
      <p>Your solar journey, supported every step of the way.</p>
      <a href="#">Contact support →</a>
    </div>

    <div class="sidebar-footer">
      <i class="bi bi-house-door"></i>
      <div>
        <strong>124 Oakwood Drive</strong>
        <p>Austin, TX 78704</p>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="dashboard-main">
    
    <!-- Top Header -->
    <header class="dashboard-header">
      <div class="breadcrumbs">
        <span>Customer portal</span> <i class="bi bi-chevron-right"></i> <strong id="breadcrumb-text">Dashboard</strong>
      </div>
      <div class="user-profile">
        <i class="bi bi-bell header-icon"></i>
        <div class="profile-info">
          <div class="avatar-initials">AM</div>
          <div>
            <strong>Alex Morgan</strong>
            <span>Homeowner</span>
          </div>
          <i class="bi bi-chevron-down"></i>
        </div>
      </div>
    </header>

    <!-- Dashboard Content Container -->
    <div class="dashboard-content">
      
      <!-- ========================================== -->
      <!-- VIEW 1: OVERVIEW                           -->
      <!-- ========================================== -->
      <div id="view-overview" class="dashboard-view active">
        
        <div class="welcome-row">
          <p class="date-text">Tuesday, October 20, 2026</p>
          <h1>Welcome back, <span class="user-first-name">Alex</span></h1>
          <p class="subtitle">A brighter home starts with a little peace of mind.</p>
        </div>

        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-header"><span>System status</span> <i class="bi bi-sun"></i></div>
            <h2 class="text-green">Active</h2>
          </div>
          <div class="stat-card">
            <div class="stat-header"><span>Completed services</span> <i class="bi bi-clipboard-check"></i></div>
            <h2>4</h2>
          </div>
          <div class="stat-card">
            <div class="stat-header"><span>Customer since</span> <i class="bi bi-seedling"></i></div>
            <h2>2024</h2>
          </div>
        </div>

        <div class="main-grid">
          <!-- Left Column -->
          <div class="left-col">
            <div class="dash-card schedule-card">
              <div class="card-top">
                <h4 class="card-label">NEXT SCHEDULED SERVICE</h4>
                <span class="status-badge confirmed"><span class="dot green"></span> Confirmed</span>
              </div>
              <h2>Battery upgrade consultation</h2>
              <p class="text-gray">A little planning. A more resilient home.</p>
              
              <div class="schedule-details">
                <div class="detail-item">
                  <i class="bi bi-calendar"></i>
                  <div><span>Date & time</span><strong>Mon, Oct 26 · 10–11 AM</strong></div>
                </div>
                <div class="detail-item">
                  <i class="bi bi-person"></i>
                  <div><span>Assigned technician</span><strong>Daniel Brooks</strong></div>
                </div>
              </div>
              <div class="card-actions">
                <button class="btn-primary" style="padding: 10px 20px;">View appointment</button>
                <a href="#" class="text-link">Reschedule</a>
              </div>
            </div>

            <div class="dash-card notifications-card">
              <div class="card-top">
                <h2>Notifications</h2>
                <a href="#" class="text-link">Mark all as read</a>
              </div>
              <ul class="notification-list">
                <li>
                  <div class="notif-icon"><i class="bi bi-calendar-check"></i></div>
                  <div class="notif-content">
                    <strong>Your schedule is confirmed</strong>
                    <p>Daniel Brooks will visit Oct 26, 10–11 AM.</p>
                  </div>
                  <span class="notif-time">Today</span>
                </li>
                <li>
                  <div class="notif-icon"><i class="bi bi-receipt"></i></div>
                  <div class="notif-content">
                    <strong>Your receipt is ready</strong>
                    <p>Download your Oct 15 maintenance receipt.</p>
                  </div>
                  <span class="notif-time">Oct 15</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Right Column -->
          <div class="right-col">
            <div class="dash-card home-card">
              <img src="<?= base_url('assets/images/abtpanel.jpg') ?>" alt="My Solar Home" class="home-img">
              <div class="home-details">
                <div class="card-top">
                  <h3>My solar home</h3>
                  <i class="bi bi-house"></i>
                </div>
                <p class="text-gray">124 Oakwood Drive Austin, TX 78704</p>
                <div class="system-info">
                  <span>8.4 kW solar system</span>
                  <span class="text-green font-bold">Active</span>
                </div>
              </div>
            </div>

            <div class="dash-card latest-service-card">
              <div class="card-top">
                <h2>Latest service</h2>
                <i class="bi bi-check-circle text-green"></i>
              </div>
              <h3>Maintenance</h3>
              <p class="text-gray">Oct 15, 2026 · Daniel Brooks</p>
              <div class="price-row">
                <h2>$149.00</h2>
                <span class="status-badge completed">• Completed</span>
              </div>
              <button class="btn-outline w-100"><i class="bi bi-download"></i> Download receipt</button>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- VIEW 2: MY SCHEDULE                        -->
      <!-- ========================================== -->
      <div id="view-schedule" class="dashboard-view" style="display: none;">
        
        <div class="schedule-header-row">
          <div>
            <h1>My schedule</h1>
            <p class="subtitle">Your next steps toward a brighter home, all in one place.</p>
          </div>
          <button class="btn-primary"><i class="bi bi-plus"></i> Request New Service</button>
        </div>

        <div class="schedule-filters">
          <button class="filter-btn active">All bookings <span class="count">3</span></button>
          <button class="filter-btn">Upcoming <span class="count">2</span></button>
          <button class="filter-btn">Completed <span class="count">1</span></button>
        </div>

        <div class="schedule-layout">
          <!-- Left Column -->
          <div class="schedule-list">
            <div class="list-header">
              <h3>October–November 2026</h3>
              <i class="bi bi-filter text-gray"></i>
            </div>

            <div class="booking-card">
              <div class="booking-date">
                <span class="month">OCT</span>
                <span class="day">26</span>
              </div>
              <div class="booking-info">
                <div class="booking-top">
                  <h3>Battery upgrade consultation</h3>
                  <span class="status-badge confirmed"><span class="dot green"></span> Confirmed</span>
                </div>
                <p class="text-gray" style="font-size: 14px; margin-bottom: 16px;">Monday, October 26, 2026</p>
                <div class="booking-details">
                  <div class="detail-item"><i class="bi bi-clock"></i><div><span>Time</span><strong>10:00–11:00 AM</strong></div></div>
                  <div class="detail-item"><i class="bi bi-person"></i><div><span>Technician</span><strong>Daniel Brooks</strong></div></div>
                </div>
                <p class="text-gray" style="font-size: 14px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #E0E0E0;">We'll review your energy needs and battery options.</p>
                <div class="booking-footer">
                  <a href="#" class="text-link font-bold">View details →</a>
                  <a href="#" class="text-link text-gray">Reschedule</a>
                </div>
              </div>
            </div>

            <div class="booking-card">
              <div class="booking-date">
                <span class="month">NOV</span>
                <span class="day">06</span>
              </div>
              <div class="booking-info">
                <div class="booking-top">
                  <h3>Battery installation</h3>
                  <span class="status-badge pending"><span class="dot yellow"></span> Pending</span>
                </div>
                <p class="text-gray" style="font-size: 14px; margin-bottom: 16px;">Friday, November 6, 2026</p>
                <div class="booking-details">
                  <div class="detail-item"><i class="bi bi-clock"></i><div><span>Time</span><strong>9:00 AM–1:00 PM</strong></div></div>
                  <div class="detail-item"><i class="bi bi-person"></i><div><span>Technician</span><strong>Maya Chen</strong></div></div>
                </div>
                <p class="text-gray" style="font-size: 14px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #E0E0E0;">Tentative booking. We'll confirm after your consultation.</p>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="schedule-sidebar">
            <div class="dash-card calendar-widget">
              <div class="calendar-header">
                <h3>October 2026</h3>
                <div class="calendar-nav"><i class="bi bi-chevron-left"></i><i class="bi bi-chevron-right"></i></div>
              </div>
              <div class="calendar-grid">
                <span class="day-name">M</span><span class="day-name">T</span><span class="day-name">W</span><span class="day-name">T</span><span class="day-name">F</span><span class="day-name">S</span><span class="day-name">S</span>
                <span class="text-gray">28</span><span class="text-gray">29</span><span class="text-gray">30</span><span>1</span><span>2</span><span>3</span><span>4</span>
                <span>5</span><span>6</span><span>7</span><span>8</span><span>9</span><span>10</span><span>11</span>
                <span>12</span><span>13</span><span>14</span><span>15</span><span>16</span><span>17</span><span>18</span>
                <span>19</span><span class="today">20</span><span>21</span><span>22</span><span>23</span><span>24</span><span>25</span>
                <span class="active-day">26</span><span>27</span><span>28</span><span>29</span><span>30</span><span>31</span><span class="text-gray">1</span>
              </div>
              <div class="calendar-footer">
                <span class="dot green"></span> Next visit · October 26
              </div>
            </div>

            <div class="dash-card help-widget">
              <i class="bi bi-clipboard-check"></i>
              <h3>Ready for your visit?</h3>
              <p class="text-gray">Please keep your electrical panel accessible. An adult should be home during the appointment.</p>
              <a href="#" class="text-link font-bold">Questions? Contact support →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer inside main -->
      <footer class="dashboard-footer">
        <p>© 2026 Sun Son Solar</p>
        <div>
          <a href="#">Privacy</a> · <a href="#">Terms</a>
        </div>
      </footer>
    </div>
  </main>
  
  <script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
</body>
</html>