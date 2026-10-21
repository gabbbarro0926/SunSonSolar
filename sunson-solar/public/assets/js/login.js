document.addEventListener('DOMContentLoaded', function () {
  const loginForm = document.getElementById('login-form');
  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');
  const loginBtn = document.getElementById('login-btn');

  // Replace this with your actual API URL
  const API_URL_LOGIN = 'http://localhost:8080/api/auth/login';

  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();

      // 1. Basic client-side check
      if (!usernameInput.value || !passwordInput.value) {
        alert('Please enter both username/email and password.');
        return;
      }

      // 2. Show loading state
      const originalBtnText = loginBtn.textContent;
      loginBtn.textContent = 'Signing in...';
      loginBtn.disabled = true;

      // 3. Prepare data for the API
      const loginData = {
        username: usernameInput.value,
        password: passwordInput.value
      };

      // 4. Send fetch request
      fetch(API_URL_LOGIN, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(loginData)
      })
      .then(response => {
        // If backend returns 401 (Unauthorized), parse the JSON error
        return response.json().then(data => ({ status: response.status, data }));
      })
      .then(result => {
        loginBtn.textContent = originalBtnText;
        loginBtn.disabled = false;

        if (result.status === 200 && result.data.status === 200) {
          // SUCCESS! The backend sent a token.
          // Save the token and user data in localStorage
          localStorage.setItem('auth_token', result.data.token);
          localStorage.setItem('user_data', JSON.stringify(result.data.user));

          alert('Login successful!');
          
          // Redirect to the dashboard
          window.location.href = 'dashboard'; 
        } else {
          // FAILURE (Wrong password, deactivated account, etc.)
          alert(result.data.message || 'Login failed. Please try again.');
        }
      })
      .catch(error => {
        loginBtn.textContent = originalBtnText;
        loginBtn.disabled = false;
        console.error('Error:', error);
        alert('Could not connect to the server. Is the backend running?');
      });
    });
  }
});