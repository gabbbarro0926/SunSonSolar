document.addEventListener('DOMContentLoaded', function () {
  
  // 1. AUTHENTICATION CHECK
  const token = localStorage.getItem('auth_token');
  const userDataString = localStorage.getItem('user_data');

  if (!token || !userDataString) {
    window.location.href = 'login'; 
    return;
  }

  const user = JSON.parse(userDataString);
  if (user && user.first_name) {
    const welcomeName = document.querySelector('.user-first-name');
    if (welcomeName) welcomeName.textContent = user.first_name;
    
    const profileName = document.querySelector('.profile-info div strong');
    if (profileName) profileName.textContent = `${user.first_name} ${user.last_name}`;
    
    const avatarInitials = document.querySelector('.avatar-initials');
    if (avatarInitials) {
      avatarInitials.textContent = `${user.first_name.charAt(0)}${user.last_name.charAt(0)}`.toUpperCase();
    }
  }

  // 2. TAB SWITCHING LOGIC
  const menuLinks = document.querySelectorAll('.sidebar-menu a');
  const views = document.querySelectorAll('.dashboard-view');
  const breadcrumbText = document.getElementById('breadcrumb-text');

  menuLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault(); // Stop the '#' from jumping to top
      
      // Get target view ID from the data-target attribute
      const targetId = this.getAttribute('data-target');
      if (!targetId) return; // If link has no target (like external links), ignore

      // Remove active from all menu links
      menuLinks.forEach(l => l.classList.remove('active'));
      // Add active to clicked link
      this.classList.add('active');

      // Hide all views
      views.forEach(v => v.style.display = 'none');
      
      // Show the target view
      const targetView = document.getElementById(targetId);
      if (targetView) {
        targetView.style.display = 'block';
        // Trigger animation
        targetView.classList.add('active'); 
      }

      // Update Breadcrumb
      if (targetId === 'view-overview') {
        breadcrumbText.textContent = 'Dashboard';
      } else {
        breadcrumbText.textContent = this.textContent.trim();
      }
    });
  });

});