// Sun Son Solar - Registration Page Script

document.addEventListener('DOMContentLoaded', function () {

 
// ROLE TOGGLE (Customer / Employee)
  
const roleTabs = document.querySelectorAll('.role-tab');
const userTypeInput = document.getElementById('user-type');
const employeeFields = document.getElementById('employee-fields');
const departmentInput = document.getElementById('department');
const loadingOverlay = document.getElementById('loading-overlay');

let currentRole = 'customer'; // tracks the currently active role

  roleTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      const newRole = this.dataset.role;

      // If they clicked the tab that's already active, do nothing
      if (newRole === currentRole) return;

      // Show the loading spinner
      loadingOverlay.classList.remove('d-none');

      // Small delay so the user actually sees the spinner
      setTimeout(function () {
        // Update active tab styling
        roleTabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');

        // Update hidden field
        userTypeInput.value = newRole;
        currentRole = newRole;

        // Show or hide department
        if (newRole === 'employee') {
          employeeFields.classList.remove('d-none');
          departmentInput.setAttribute('required', 'required');
        } else {
          employeeFields.classList.add('d-none');
          departmentInput.removeAttribute('required');
          departmentInput.value = '';
          clearFieldState(departmentInput);
        }

        // Hide the spinner
        loadingOverlay.classList.add('d-none');
      }, 300); // 300ms delay
    });
  });

  // ================================
  // PHONE FORMATTER
  // ================================
  const phoneInput = document.getElementById('phone');

  phoneInput.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 11) {
      this.value = this.value.slice(0, 11);
    }
  });

  // ================================
  // FIELD REFERENCES
  // ================================
  const form = document.querySelector('.registration-form');
  const firstName = document.getElementById('first-name');
  const middleName = document.getElementById('middle-name');
  const lastName = document.getElementById('last-name');
  const birthDate = document.getElementById('birth-date');
  const gender = document.getElementById('gender');
  const email = document.getElementById('email');
  const address = document.getElementById('address');
  const username = document.getElementById('username');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirm-password');
  const terms = document.getElementById('terms');

  // All fields that need live validation
  const liveFields = [firstName, middleName, lastName, birthDate, gender, email, phoneInput, address, username, password, confirmPassword, departmentInput];

  // ================================
  // VALIDATION RULES
  // ================================
  const rules = {
    firstName:      { test: (v) => /^[A-Za-z\s\-']{2,50}$/.test(v), message: 'Please enter your first name' },
    middleName:     { test: (v) => /^[A-Za-z\s\-']{2,50}$/.test(v), message: 'Please enter your middle name' },
    lastName:       { test: (v) => /^[A-Za-z\s\-']{2,50}$/.test(v), message: 'Please enter your last name' },
    birthDate:      { test: (v) => isValidBirthDate(v), message: 'Enter a valid birthdate.' },
    gender:         { test: (v) => v !== '', message: 'Select a gender.' },
    email:          { test: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), message: 'Enter a valid email address.' },
    phoneInput:     { test: (v) => /^(09\d{9}|\+639\d{9})$/.test(v), message: 'Enter an 11-digit number starting with 09.' },
    address:        { test: (v) => v.length >= 10 && v.length <= 200, message: 'Enter your complete address.' },
    username:       { test: (v) => /^[A-Za-z0-9_]{4,20}$/.test(v), message: 'Use 4–20 letters, numbers, or underscores.' },
    password:       { test: (v) => /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(v), message: 'Password needs 8+ characters with a capital, number and symbol.' },
    confirmPassword:{ test: (v) => v === password.value && v !== '', message: "Passwords don't match." },
    departmentInput:{ test: (v) => v.trim().length >= 2, message: 'Enter your department.' }
  };

  // ================================
  // HELPERS
  // ================================
  function isValidBirthDate(value) {
    if (!value) return false;
    const date = new Date(value);
    if (isNaN(date.getTime())) return false;
    const today = new Date();
    if (date > today) return false;
    // Minimum age: 13 (adjust later if Kat confirms otherwise)
    const age = today.getFullYear() - date.getFullYear();
    return age >= 13;
  }

  function getErrorElement(input) {
    const group = input.closest('.mb-3, .col-12, .col-md-4, .col-md-6');
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

  function clearFieldState(input) {
    input.classList.remove('is-invalid', 'is-valid');
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
    if (input === departmentInput) return rules.departmentInput;
    return null;
  }

  // ================================
  // LIVE VALIDATION (fix-error-turns-green)
  // ================================
  liveFields.forEach(function (field) {
    if (!field) return;

    field.addEventListener('input', function () {
      const rule = getRuleFor(this);
      if (!rule) return;

      // Only re-validate if the field is currently marked invalid
      if (this.classList.contains('is-invalid')) {
        if (rule.test(this.value)) {
          showValid(this);
        }
      }
    });

    field.addEventListener('change', function () {
      const rule = getRuleFor(this);
      if (!rule) return;
      if (this.classList.contains('is-invalid')) {
        if (rule.test(this.value)) {
          showValid(this);
        }
      }
    });
  });

  // ================================
  // SUBMIT VALIDATION
  // ================================
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    let hasError = false;
    let firstErrorField = null;

    liveFields.forEach(function (field) {
      if (!field) return;
      const rule = getRuleFor(field);
      if (!rule) return;

      // Skip department if the user is a customer
      if (field === departmentInput && userTypeInput.value !== 'employee') {
        clearFieldState(field);
        return;
      }

      if (!rule.test(field.value)) {
        showError(field, rule.message);
        hasError = true;
        if (!firstErrorField) firstErrorField = field;
      } else {
        showValid(field);
      }
    });

    // Terms checkbox
    const termsErrorEl = terms.closest('.form-check').querySelector('.error-message');
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

    // All valid — submit to backend
    form.submit();
  });

});