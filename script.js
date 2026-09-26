// Sun Son Solar - Registration Page Script

document.addEventListener('DOMContentLoaded', function () {

  // ROLE TOGGLE
  const roleTabs = document.querySelectorAll('.role-tab');
  const userTypeInput = document.getElementById('user-type');
  const employeeFields = document.getElementById('employee-fields');
  const departmentInput = document.getElementById('department');
  const loadingOverlay = document.getElementById('loading-overlay');

  // TAB STATE PRESERVATION
  let currentRole = 'customer';

  const savedState = {
    customer: { fields: {}, states: {}, termsChecked: false, termsError: false },
    employee: { fields: {}, states: {}, termsChecked: false, termsError: false }
  };

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

  const liveFields = [firstName, middleName, lastName, birthDate, gender, email, phoneInput, address, username, password, confirmPassword, departmentInput];

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
    if (!/^[A-Za-z\s\-']+$/.test(trimmed)) return 'Enter a valid name using letters only.';
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
    middleName:      (v) => v.trim() === '' ? null : validateName(v, 'middle name'),
    lastName:        (v) => validateName(v, 'last name'),
    birthDate:       (v) => validateBirthDate(v),
    gender:          (v) => v === '' ? 'Select a gender.' : null,
    email:           (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? null : 'Enter a valid email address.',
    phoneInput:      (v) => /^(09\d{9}|\+639\d{9})$/.test(v) ? null : 'Enter an 11-digit number starting with 09.',
    address:         (v) => v.length >= 10 && v.length <= 200 ? null : 'Enter your complete address.',
    username:        (v) => /^[A-Za-z0-9_]{4,20}$/.test(v) ? null : 'Use 4–20 letters, numbers, or underscores.',
    password:        (v) => /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(v) ? null : 'Password needs 8+ characters with a capital, number and symbol.',
    confirmPassword: (v) => v === password.value && v !== '' ? null : "Passwords don't match.",
    departmentInput: (v) => v.trim().length >= 2 ? null : 'Enter your department.'
  };

  // HELPERS
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

  // SAVE / RESTORE FORM STATE
  function saveFormState(role) {
    savedState[role].fields = {};
    savedState[role].states = {};

    liveFields.forEach(function (field) {
      if (!field) return;
      savedState[role].fields[field.name] = field.value;

      if (field.classList.contains('is-invalid')) {
        savedState[role].states[field.name] = 'invalid';
      } else if (field.classList.contains('is-valid')) {
        savedState[role].states[field.name] = 'valid';
      } else {
        savedState[role].states[field.name] = null;
      }
    });

    savedState[role].termsChecked = terms.checked;
    const termsErrorEl = terms.closest('.form-check').querySelector('.error-message');
    savedState[role].termsError = termsErrorEl.classList.contains('show');
  }

  function restoreFormState(role) {
    const fields = savedState[role].fields;
    const states = savedState[role].states;

    liveFields.forEach(function (field) {
      if (!field) return;

      // Restore value
      field.value = (field.name in fields) ? fields[field.name] : '';

      // Restore visual state
      const state = states[field.name];
      const errorEl = getErrorElement(field);

      if (state === 'invalid') {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        const rule = getRuleFor(field);
        if (rule && errorEl) {
          const error = rule(field.value);
          errorEl.textContent = error || '';
          errorEl.classList.add('show');
        }
      } else if (state === 'valid') {
        field.classList.add('is-valid');
        field.classList.remove('is-invalid');
        if (errorEl) {
          errorEl.textContent = '';
          errorEl.classList.remove('show');
        }
      } else {
        field.classList.remove('is-invalid', 'is-valid');
        if (errorEl) {
          errorEl.textContent = '';
          errorEl.classList.remove('show');
        }
      }
    });

    // Restore terms
    terms.checked = savedState[role].termsChecked || false;
    const termsErrorEl = terms.closest('.form-check').querySelector('.error-message');
    if (savedState[role].termsError) {
      termsErrorEl.textContent = 'You must agree to the Terms and Conditions.';
      termsErrorEl.classList.add('show');
    } else {
      termsErrorEl.textContent = '';
      termsErrorEl.classList.remove('show');
    }
  }

  // INITIAL STATE: department disabled (customer default)
  departmentInput.disabled = true;

  // TAB CLICK HANDLER
  roleTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      const newRole = this.dataset.role;
      if (newRole === currentRole) return;

      // Save current tab's state
      saveFormState(currentRole);

      // Show spinner
      loadingOverlay.classList.remove('d-none');

      setTimeout(function () {
        // Update active tab
        roleTabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');

        // Update hidden field
        userTypeInput.value = newRole;

        // Show/hide department + disable when hidden
        if (newRole === 'employee') {
          employeeFields.classList.remove('d-none');
          departmentInput.setAttribute('required', 'required');
          departmentInput.disabled = false;
        } else {
          employeeFields.classList.add('d-none');
          departmentInput.removeAttribute('required');
          departmentInput.disabled = true;
        }

        // Restore new tab's state
        restoreFormState(newRole);

        // Update tracker
        currentRole = newRole;

        // Hide spinner
        loadingOverlay.classList.add('d-none');
      }, 300);
    });
  });

  // LIVE VALIDATION
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

      // Skip department if customer
      if (field === departmentInput && userTypeInput.value !== 'employee') {
        clearFieldState(field);
        return;
      }

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

    form.submit();
  });

});
