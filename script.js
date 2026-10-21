// Sun Son Solar - Registration Page Script

document.addEventListener('DOMContentLoaded', function () {

  // FIELD REFERENCES
  const form = document.querySelector('.registration-form');
  const firstName = document.getElementById('first-name');
  const middleName = document.getElementById('middle-name');
  const lastName = document.getElementById('last-name');
  const birthDate = document.getElementById('birth-date');
  const gender = document.getElementById('gender');
  const email = document.getElementById('email');
  const phoneInput = document.getElementById('phone');
  const address = document.getElementById('address');
  const username = document.getElementById('username');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirm-password');
  const terms = document.getElementById('terms');

  const liveFields = [firstName, middleName, lastName, birthDate, gender, email, phoneInput, address, username, password, confirmPassword];

  // PASSWORD VISIBILITY TOGGLE
  const passwordToggles = document.querySelectorAll('.password-toggle');

  passwordToggles.forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      const targetId = this.dataset.target;
      const targetInput = document.getElementById(targetId);
      const icon = this.querySelector('i');

      if (targetInput.type === 'password') {
        targetInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
        this.setAttribute('aria-label', 'Hide password');
      } else {
        targetInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
        this.setAttribute('aria-label', 'Show password');
      }
    });
  });

  // PHONE FORMATTER
  phoneInput.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 11) {
      this.value = this.value.slice(0, 11);
    }
  });

  // VALIDATION RULES
  function validateName(value, fieldLabel) {
    const trimmed = value.trim();
    if (trimmed === '') return `Please enter your ${fieldLabel}`;
    if (!/^[A-Za-z\s\-']+$/.test(trimmed)) return 'Enter a valid name';
    if (trimmed.length < 2 || trimmed.length > 50) return 'Name must be 2–50 characters.';
    return null;
  }

  function validateBirthDate(value) {
    if (!value) return 'Please enter your birthdate.';
    const date = new Date(value);
    if (isNaN(date.getTime())) return 'Enter a valid birthdate.';
    const today = new Date();
    if (date > today) return 'Enter a valid birthdate.';

    let age = today.getFullYear() - date.getFullYear();
    const monthDiff = today.getMonth() - date.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < date.getDate())) {
      age--;
    }
    if (age < 18) return 'You must be at least 18 years old to register.';
    return null;
  }

  const rules = {
    firstName:       (v) => validateName(v, 'first name'),
    // MIDDLE NAME IS NOW REQUIRED
    middleName:      (v) => validateName(v, 'middle name'),
    lastName:        (v) => validateName(v, 'last name'),
    birthDate:       (v) => validateBirthDate(v),
    gender:          (v) => v === '' ? 'Select a gender.' : null,
    email:           (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? null : 'Enter a valid email address.',
    phoneInput:      (v) => /^(09\d{9}|\+639\d{9})$/.test(v) ? null : 'Enter an 11-digit number starting with 09.',
    address:         (v) => v.length >= 10 && v.length <= 200 ? null : 'Enter your complete address.',
    username:        (v) => /^[A-Za-z0-9_]{4,20}$/.test(v) ? null : 'Use 4–20 letters, numbers, or underscores.',
    password:        (v) => /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(v) ? null : 'Password needs 8+ characters with a capital, number and symbol.',
    confirmPassword: (v) => v === password.value && v !== '' ? null : "Passwords don't match."
  };

  // HELPERS
  function getErrorElement(input) {
    const group = input.closest('.field');
    return group ? group.querySelector('.error-message') : null;
  }

  function showError(input, message) {
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
    const errorEl = getErrorElement(input);
    if (errorEl) {
      errorEl.textContent = message;
      errorEl.classList.add('show');
    }
  }

  function showValid(input) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    const errorEl = getErrorElement(input);
    if (errorEl) {
      errorEl.textContent = '';
      errorEl.classList.remove('show');
    }
  }

  function getRuleFor(input) {
    if (input === firstName) return rules.firstName;
    if (input === middleName) return rules.middleName;
    if (input === lastName) return rules.lastName;
    if (input === birthDate) return rules.birthDate;
    if (input === gender) return rules.gender;
    if (input === email) return rules.email;
    if (input === phoneInput) return rules.phoneInput;
    if (input === address) return rules.address;
    if (input === username) return rules.username;
    if (input === password) return rules.password;
    if (input === confirmPassword) return rules.confirmPassword;
    return null;
  }

  // LIVE VALIDATION (fix-error-turns-green)
  liveFields.forEach(function (field) {
    if (!field) return;

    field.addEventListener('input', function () {
      const rule = getRuleFor(this);
      if (!rule) return;
      if (this.classList.contains('is-invalid')) {
        const error = rule(this.value);
        if (error === null) {
          showValid(this);
        } else {
          showError(this, error);
        }
      }
    });

    field.addEventListener('change', function () {
      const rule = getRuleFor(this);
      if (!rule) return;
      if (this.classList.contains('is-invalid')) {
        const error = rule(this.value);
        if (error === null) {
          showValid(this);
        } else {
          showError(this, error);
        }
      }
    });
  });

  // SUBMIT VALIDATION
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    let hasError = false;
    let firstErrorField = null;

    liveFields.forEach(function (field) {
      if (!field) return;
      const rule = getRuleFor(field);
      if (!rule) return;

      const error = rule(field.value);
      if (error !== null) {
        showError(field, error);
        hasError = true;
        if (!firstErrorField) firstErrorField = field;
      } else {
        showValid(field);
      }
    });

    // Terms checkbox
    const termsErrorEl = terms.closest('.checkbox-field').querySelector('.error-message');
    if (!terms.checked) {
      termsErrorEl.textContent = 'You must agree to the Terms and Conditions.';
      termsErrorEl.classList.add('show');
      hasError = true;
      if (!firstErrorField) firstErrorField = terms;
    } else {
      termsErrorEl.textContent = '';
      termsErrorEl.classList.remove('show');
    }

    if (hasError) {
      if (firstErrorField) firstErrorField.focus();
      return;
    }

    form.submit();
  });

});

// Mobile Menu Toggle
const hamburger = document.getElementById('hamburger-btn');
const navMenu = document.getElementById('nav-menu');

if (hamburger && navMenu) {
  hamburger.addEventListener('click', () => {
    // Toggle the 'active' class on both elements
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
  });

  // Optional: Close the menu when a link is clicked
  document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
      hamburger.classList.remove('active');
      navMenu.classList.remove('active');
    });
  });
}
