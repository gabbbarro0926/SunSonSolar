<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sun Son Solar | Our Services</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

  <!-- Navigation Bar -->
  <header class="navbar">
    <div class="nav-container">
      <a href="<?= base_url('/') ?>" class="nav-logo">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Sun Son Solar Logo">
      </a>
      <button class="hamburger" id="hamburger-btn" aria-label="Toggle navigation">
        <span class="bar"></span><span class="bar"></span><span class="bar"></span>
      </button>
      <div class="nav-menu" id="nav-menu">
        <nav class="nav-links">
          <a href="<?= base_url('/') ?>">Home</a>
          <a href="<?= base_url('services') ?>" class="active">Services</a>
          <a href="<?= base_url('about') ?>">About</a>
          <a href="<?= base_url('contact') ?>">Contact</a>
        </nav>
        <div class="nav-actions">
          <a href="<?= base_url('login') ?>" class="btn-login">Log In</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Page Header (Gray Banner) -->
  <section class="page-header">
    <div class="container">
      <div class="section-tagline">
        <span class="sun-icon">☼</span> SOLAR, MADE SIMPLE
      </div>
      <h1 class="section-title">Our Solar Solutions</h1>
      <p class="section-subtitle">End-to-end support for your solar journey, from your first question to lasting peace of mind.</p>
    </div>
  </section>

  <!-- Services Cards Section -->
  <section class="services-section">
    <div class="container">
      <div class="services-grid">
        
        <!-- Card 1 -->
        <div class="service-card">
          <div class="service-icon"><i class="bi bi-chat-left-text"></i></div>
          <h3 class="service-title">Consultation</h3>
          <p class="service-description">Start with a conversation. We'll explore your energy needs and help you find the right solar solution.</p>
          <a href="#" class="service-link">Learn More →</a>
        </div>

        <!-- Card 2 -->
        <div class="service-card">
          <div class="service-icon"><i class="bi bi-tools"></i></div>
          <h3 class="service-title">Installation</h3>
          <p class="service-description">From planning to power-on, we take care of the details for a smooth, thoughtfully managed installation. Our team helps you make a confident switch to clean energy.</p>
          <a href="#" class="service-link">Learn More →</a>
        </div>

        <!-- Card 3 -->
        <div class="service-card">
          <div class="service-icon"><i class="bi bi-wrench-adjustable"></i></div>
          <h3 class="service-title">Maintenance</h3>
          <p class="service-description">Keep your system working at its best with ongoing care, routine checks and helpful support. We help you protect your solar investment for the long run.</p>
          <a href="#" class="service-link">Learn More →</a>
        </div>

      </div>
    </div>
  </section>

  <!-- Green CTA Banner -->
  <section class="cta-banner">
    <div class="container cta-container">
      <div class="cta-text">
        <h2>Ready to switch to solar? Request a Free Quote</h2>
        <p>Your home. Your goals. No obligation.</p>
      </div>
      <div class="cta-btn-wrapper">
        <a href="<?= base_url('register') ?>" class="btn-cta">Inquire Now!</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="container footer-container">
      <div class="footer-top">
        <div class="footer-col">
          <h4 class="footer-heading">Company Info</h4>
          <div class="footer-logo">Sun Son Solar</div>
          <p class="footer-text">A simpler path to clean energy. Helping you build a brighter future, one home at a time.</p>
          <a href="<?= base_url('about') ?>" class="footer-link">About us</a>
        </div>
        <div class="footer-col">
          <h4 class="footer-heading">Contact Details</h4>
          <ul class="footer-list">
            <li><span class="footer-icon">✉</span> hello@sunsonsolar.com</li>
            <li><span class="footer-icon">✆</span> 09291230983</li>
            <li><span class="footer-icon">📍</span> Pasig City</li>
          </ul>
        </div>
        <div class="footer-col">
          <h4 class="footer-heading">Social Links</h4>
          <ul class="footer-list">
            <li><a href="#" class="footer-social-link"><span class="footer-icon">📷</span> Instagram</a></li>
            <li><a href="#" class="footer-social-link"><span class="footer-icon">📘</span> Facebook</a></li>
            <li><a href="#" class="footer-social-link"><span class="footer-icon">💼</span> LinkedIn</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p class="copyright">© 2026 Sun Son Solar. All rights reserved.</p>
        <div class="legal-links">
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Use</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>