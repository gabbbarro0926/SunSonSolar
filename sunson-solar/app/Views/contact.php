<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sun Son Solar | Contact Us</title>

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
          <a href="<?= base_url('services') ?>">Services</a>
          <a href="<?= base_url('about') ?>">About</a>
          <a href="<?= base_url('contact') ?>" class="active">Contact</a>
        </nav>
        <div class="nav-actions">
          <a href="<?= base_url('login') ?>" class="btn-login">Log In</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Page Header -->
  <section class="page-header">
    <div class="container">
      <div class="section-tagline">
        <span class="sun-icon">☼</span> GET IN TOUCH
      </div>
      <h1 class="section-title">Contact Us</h1>
      <p class="section-subtitle">Have a question about going solar? We're here to help.</p>
    </div>
  </section>

  <!-- Contact Content -->
  <section class="section-padding bg-white">
    <div class="container split-layout">
      
      <!-- Left Side: Contact Info -->
      <div class="split-content">
        <h2 class="section-title">Let's talk about your energy goals.</h2>
        <p class="text-gray" style="margin-bottom: 40px;">Whether you're ready to request a quote or just want to learn more about how solar works, our team is ready to answer your questions.</p>
        
        <div class="contact-info-list">
          <div class="contact-info-item">
            <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
            <div>
              <h4>Our Office</h4>
              <p>Pasig City, Metro Manila</p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="contact-icon"><i class="bi bi-telephone"></i></div>
            <div>
              <h4>Phone</h4>
              <p>09291230983</p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="contact-icon"><i class="bi bi-envelope"></i></div>
            <div>
              <h4>Email</h4>
              <p>hello@sunsonsolar.com</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side: Contact Form -->
      <div class="split-image">
        <div class="contact-form-card">
          <form action="#" method="POST">
            
            <div class="field">
              <label class="field-label">Full Name</label>
              <input type="text" class="form-input" placeholder="Your Name" required>
            </div>

            <div class="field">
              <label class="field-label">Email Address</label>
              <input type="email" class="form-input" placeholder="you@example.com" required>
            </div>

            <div class="field">
              <label class="field-label">Subject</label>
              <input type="text" class="form-input" placeholder="How can we help?" required>
            </div>

            <div class="field">
              <label class="field-label">Message</label>
              <textarea class="form-input" rows="4" placeholder="Write your message here..." required></textarea>
            </div>

            <button type="submit" class="signup-btn" style="margin-top: 10px;">Send Message</button>
          </form>
        </div>
      </div>

    </div>
  </section>

  <!-- Map Placeholder -->
  <section class="map-section">
    <div class="map-placeholder">
      <i class="bi bi-map" style="font-size: 48px; color: #A0AAB2; margin-bottom: 16px; display: block;"></i>
      <p>Map Integration Coming Soon</p>
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