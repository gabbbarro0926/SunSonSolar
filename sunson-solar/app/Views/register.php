<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sun Son Solar | Registration</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons (icon font only) -->
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
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>

      <div class="nav-menu" id="nav-menu">
        <nav class="nav-links">
          <a href="<?= base_url('/') ?>">Home</a>
          <a href="<?= base_url('services') ?>">Services</a>
          <a href="<?= base_url('about') ?>">About</a>
          <a href="<?= base_url('contact') ?>">Contact</a>
        </nav>
      </div>

    </div>
  </header>

  <main class="registration-page">
    <section class="registration-card">
      
      <!-- Logo -->
      <div class="logo">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Sun Son Solar Logo">
      </div>

      <!-- Title -->
      <h1 class="form-title">Create your account</h1>

      <form class="registration-form" id="registration-form" novalidate>

  <!-- Name -->
  <div class="field">
    <label class="field-label">Name:</label>
    <div class="form-row">
      <div class="field">
        <input type="text" class="form-input" id="first-name" name="first_name" placeholder="First Name" required>
        <div class="error-message"></div>
      </div>
      <div class="field">
        <input type="text" class="form-input" id="middle-name" name="middle_name" placeholder="Middle Name" required>
        <div class="error-message"></div>
      </div>
      <div class="field">
        <input type="text" class="form-input" id="last-name" name="last_name" placeholder="Last Name" required>
        <div class="error-message"></div>
      </div>
    </div>
  </div>

  <!-- Birthday & Gender -->
  <div class="form-row">
    <div class="field">
      <label class="field-label">Birthday:</label>
      <!-- NOTICE: name is birthdate, not birth_date -->
      <input type="date" class="form-input" id="birth-date" name="birthdate" required>
      <div class="error-message"></div>
    </div>
    <div class="field">
      <label class="field-label">Gender:</label>
      <select class="form-select" id="gender" name="gender" required>
        <option value="" disabled selected>Select your gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="others">Others</option>
      </select>
      <div class="error-message"></div>
    </div>
  </div>

  <!-- Email & Contact -->
  <div class="form-row">
    <div class="field">
      <label class="field-label">Email:</label>
      <input type="email" class="form-input" id="email" name="email" placeholder="Email" required>
      <div class="error-message"></div>
    </div>
    <div class="field">
      <label class="field-label">Contact Number:</label>
      <!-- NOTICE: name is phone, not phone_no -->
      <input type="tel" class="form-input" id="phone" name="phone" placeholder="Mobile Number" inputmode="numeric" required>
      <div class="error-message"></div>
    </div>
  </div>

  <!-- Address -->
  <div class="field">
    <label class="field-label">Address:</label>
    <textarea class="form-input" id="address" name="address" rows="3" required></textarea>
    <div class="error-message"></div>
  </div>

  <!-- Username -->
  <div class="field">
    <label class="field-label">Username:</label>
    <input type="text" class="form-input" id="username" name="username" required>
    <div class="error-message"></div>
  </div>

  <!-- Password -->
  <div class="field">
    <label class="field-label">Password:</label>
    <div class="password-wrapper">
      <input type="password" class="form-input" id="password" name="password" required>
      <button type="button" class="password-toggle" data-target="password" aria-label="Show password">
        <i class="bi bi-eye"></i>
      </button>
    </div>
    <div class="error-message"></div>
  </div>

  <!-- Confirm Password -->
  <div class="field">
    <label class="field-label">Confirm Password:</label>
    <div class="password-wrapper">
      <input type="password" class="form-input" id="confirm-password" name="confirm_password" required>
      <button type="button" class="password-toggle" data-target="confirm-password" aria-label="Show password">
        <i class="bi bi-eye"></i>
      </button>
    </div>
    <div class="error-message"></div>
  </div>

  <!-- Terms -->
  <div class="checkbox-field">
    <input type="checkbox" id="terms" name="terms" required>
    <label for="terms">I Agree to <a href="#">Terms and Conditions</a></label>
    <div class="error-message"></div>
  </div>

  <!-- Submit -->
  <button type="submit" class="signup-btn">Sign-Up</button>
  
  <p class="login-prompt">
    Already have an account? <a href="<?= base_url('login') ?>">Log-in</a>
  </p>

  </form>
    </section>
  </main>
  
  <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>