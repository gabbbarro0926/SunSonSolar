<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sun Son Solar | About Us</title>

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

  <!-- Navigation Bar (Update 'active' class to About) -->
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
          <a href="<?= base_url('about') ?>" class="active">About</a>
          <a href="<?= base_url('contact') ?>">Contact</a>

        </nav>
        <div class="nav-actions">
          <a href="<?= base_url('login') ?>" class="btn-login">Log In</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Page Header (Reused from Services) -->
  <section class="page-header">
    <div class="container">
      <div class="section-tagline">
        <span class="sun-icon">☼</span> A BRIGHTER EVERYDAY
      </div>
      <h1 class="section-title">Who We Are</h1>
      <p class="section-subtitle">A simpler path to clean energy, built around people.</p>
    </div>
  </section>

  <!-- Mission Section -->
  <section class="section-padding bg-white">
    <div class="container split-layout">
      <div class="split-content">
        <div class="section-tagline">
          <span class="sun-icon">☼</span> OUR MISSION
        </div>
        <h2 class="section-title">Clean energy. Real everyday impact.</h2>
        <p class="text-gray">At Sun Son Solar, our mission is to make solar energy a simple, practical choice for your home. We bring guidance, installation and ongoing care together, so you feel supported at every step.</p>
        <p class="text-gray">We help you explore ways to spend less on electricity while building a more sustainable future. By turning sunlight into everyday power, we can leave a lighter footprint, one home at a time.</p>
      </div>
        <div class="split-image">
            <img src="<?= base_url('assets/images/abtpanel.jpg') ?>" alt="Sun Son Solar Team" class="about-image">
        </div>
          <i class="bi bi-image" style="font-size: 32px; color: #A0AAB2;"></i>
          <p>Team or solar installation image<br><small>Image placeholder</small></p>
        </div>
      </div>
    </div>
  </section>

  <!-- Values Section -->
  <section class="section-padding bg-light">
    <div class="container">
      <div class="section-header">
        <div class="section-tagline">
          <span class="sun-icon">☼</span> WHAT GUIDES US
        </div>
        <h2 class="section-title">Our Values</h2>
      </div>
      
      <div class="values-grid">
        <div class="value-card">
          <div class="service-icon"><i class="bi bi-shield-check"></i></div>
          <h3 class="service-title">Trust</h3>
          <p class="text-gray">Clear advice, honest conversations and dependable support. We put your home and your goals first.</p>
        </div>
        
        <div class="value-card">
          <div class="service-icon"><i class="bi bi-lightbulb"></i></div>
          <h3 class="service-title">Innovation</h3>
          <p class="text-gray">Thoughtful solar solutions for everyday life. We embrace better ways to make clean energy simple and accessible.</p>
        </div>
        
        <div class="value-card">
          <div class="service-icon"><i class="bi bi-leaf"></i></div>
          <h3 class="service-title">Sustainability</h3>
          <p class="text-gray">A lighter footprint starts with the energy we use. We help you make choices that are better for the long run.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <section class="section-padding bg-white">
    <div class="container">
      <div class="section-header">
        <div class="section-tagline">
          <span class="sun-icon">☼</span> THE PEOPLE BEHIND THE PROMISE
        </div>
        <h2 class="section-title">Meet the Team</h2>
        <p class="section-subtitle">Working together for your brighter future.</p>
      </div>
      
            <div class="team-grid">
        <div class="team-member">
          <div class="team-avatar">
            <img src="<?= base_url('assets/images/kat.jpg') ?>" alt="Katherine Sinagaraw">
          </div>
          <h3 class="team-name">Katherine Sinagaraw</h3>
          <p class="team-role">Founder</p>
        </div>
        
        <div class="team-member">
          <div class="team-avatar">
            <img src="<?= base_url('assets/images/santino.jpg') ?>" alt="Santino Sinagaraw">
          </div>
          <h3 class="team-name">Santino Sinagaraw</h3>
          <p class="team-role">Co-Founder</p>
        </div>
        
        <div class="team-member">
          <div class="team-avatar">
            <img src="<?= base_url('assets/images/sol.jpg') ?>" alt="Sol Solis">
          </div>
          <h3 class="team-name">Sol Solis</h3>
          <p class="team-role">IT Head</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer (Same as services.php) -->
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