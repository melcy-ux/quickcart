// QuickCart — client-side scripts

// Login form validation
(function () {
  var loginForm = document.getElementById('loginForm');
  if (!loginForm) return;

  loginForm.addEventListener('submit', function (e) {
    var valid = true;

    var email = document.getElementById('email');
    var emailErr = document.getElementById('emailErr');
    if (email.value.trim() === '') {
      emailErr.textContent = 'Email address is required.';
      valid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      emailErr.textContent = 'Please enter a valid email address.';
      valid = false;
    } else {
      emailErr.textContent = '';
    }

    var password = document.getElementById('password');
    var passErr = document.getElementById('passErr');
    if (password.value === '') {
      passErr.textContent = 'Password is required.';
      valid = false;
    } else {
      passErr.textContent = '';
    }

    if (!valid) e.preventDefault();
  });
})();

// Register form — live password strength checker
(function () {
  var passField = document.getElementById('reg_password');
  if (!passField) return;

  var bar = document.getElementById('strength-bar');
  var label = document.getElementById('strength-label');

  passField.addEventListener('input', function () {
    var val = passField.value;
    var score = 0;

    if (val.length >= 8) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    var colors = ['', '#dc3545', '#fd7e14', '#198754'];
    var labels = ['', 'Weak', 'Medium', 'Strong'];

    if (val.length === 0) {
      bar.style.width = '0%';
      bar.style.backgroundColor = '';
      label.textContent = '';
    } else {
      bar.style.width = (score * 33.3) + '%';
      bar.style.backgroundColor = colors[score] || '#198754';
      label.textContent = labels[score] || 'Strong';
    }
  });

  // Register form validation
  var regForm = document.getElementById('registerForm');
  if (!regForm) return;

  regForm.addEventListener('submit', function (e) {
    var valid = true;

    var username = document.getElementById('reg_username');
    var usernameErr = document.getElementById('usernameErr');
    if (username.value.trim().length < 3) {
      usernameErr.textContent = 'Username must be at least 3 characters.';
      valid = false;
    } else {
      usernameErr.textContent = '';
    }

    var email = document.getElementById('reg_email');
    var emailErr = document.getElementById('reg_emailErr');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      emailErr.textContent = 'Please enter a valid email address.';
      valid = false;
    } else {
      emailErr.textContent = '';
    }

    var passErr = document.getElementById('reg_passErr');
    if (passField.value.length < 8) {
      passErr.textContent = 'Password must be at least 8 characters.';
      valid = false;
    } else {
      passErr.textContent = '';
    }

    if (!valid) e.preventDefault();
  });
})();

// Quantity controls on cart page
document.querySelectorAll('.qty-btn').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var input = document.querySelector('input[name="qty_' + btn.dataset.id + '"]');
    if (!input) return;
    var val = parseInt(input.value, 10) || 1;
    if (btn.dataset.action === 'inc') input.value = val + 1;
    if (btn.dataset.action === 'dec' && val > 1) input.value = val - 1;
  });
});
