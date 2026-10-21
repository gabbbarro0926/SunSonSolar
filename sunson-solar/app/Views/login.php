<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sun Son Solar | Log In</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="login-body">

  <header class="navbar">
    <div class="nav-container">
      <a href="index.html" class="nav-logo">
        <img src="<?= base_url('assets/images/logo.png') ?>">
      </a>
      
      <!-- Keep the nav links so users can go back -->
      <div class="nav-menu" id="nav-menu">
        <nav class="nav-links">
          <a href="<?= base_url('/') ?>">Home</a>
          <a href="<?= base_url('services') ?>">Services</a>
          <a href="<?= base_url('about') ?>">About</a>
          <a href="<?= base_url('contact') ?>">Contact</a>
        </nav>
        <!-- Note: We remove the "Log In" button here because we are ON the login page -->
      </div>

      <button class="hamburger" id="hamburger-btn" aria-label="Toggle navigation">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>
    </div>
  </header>

  <!-- Main Login Layout -->
  <main class="login-container">
    
    <!-- Left Side: Form -->
    <div class="login-form-side">
      <div class="login-form-wrapper">
        <h1 class="login-title">Hello! Welcome👋</h1>
        <p class="login-subtitle">Today is a new day. It's your day. You shape it.<br>Sign in to start managing your projects.</p>

        <form class="login-form" id="login-form" novalidate>
  
  <div class="field">
    <label class="field-label">Username or Email</label>
    <!-- Added id="username" -->
    <input type="text" class="form-input" id="username" placeholder="Example@email.com" required>
  </div>

  <div class="field">
    <label class="field-label">Password</label>
    <!-- Added id="password" -->
    <input type="password" class="form-input" id="password" placeholder="At least 8 characters" required>
  </div>

  <div class="forgot-password-wrapper">
    <a href="#" class="forgot-link">Forgot Password?</a>
  </div>

  <!-- Added id="login-btn" -->
  <button type="submit" class="btn-signin" id="login-btn">Sign in</button>

  <!-- ... keep your dividers and social buttons ... -->

  </form>

        <p class="signup-prompt">Don't you have an account? 
          <a href="<?= base_url('register') ?>">Sign up</a>
      </div>
    </div>

    <!-- Right Side: Image -->
    <div class="login-image-side">
      <!-- The image is applied via CSS background to ensure it fills the space perfectly -->
    </div>

  </main>

  <script src="<?= base_url('assets/js/login.js') ?>"></script>
</body>
</html>