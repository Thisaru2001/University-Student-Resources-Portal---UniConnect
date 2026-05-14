// Inject toast directly on document.body to escape all stacking contexts
(function() {
  const t = document.createElement('div');
  t.id = 'toast';
  document.body.appendChild(t);
})();

function handleSignIn(e) {
  e.preventDefault();
  const id     = document.getElementById('uid').value.trim();
  const pwd    = document.getElementById('pwd').value.trim();
  const remember = document.getElementById('remember').checked;
  if (!id || !pwd) { showToast('Please fill in both fields.'); return; }

  const btn = document.querySelector('.btn');
  btn.disabled = true;
  btn.querySelector('.btn-inner').innerHTML =
    `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
      style="animation:spin .7s linear infinite">
      <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83
               M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
    </svg> Signing in…`;

  validateCredentials(id, pwd, remember, function() {
    btn.disabled = false;
    btn.querySelector('.btn-inner').innerHTML =
      `Sign In <svg class="arrow" width="17" height="17" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M5 12h14M13 6l6 6-6 6"/></svg>`;
  });
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

document.getElementById('uid').addEventListener('keydown', e => {
  if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pwd').focus(); }
});

function validateCredentials(id, pwd, remember, onDone) {
  let form = new FormData();
  form.append("id", id);
  form.append("pwd", pwd);
  form.append("remember", remember);

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "signinProcess.php", true);

  xhr.onload = function() {
    onDone();
    if (xhr.status === 200 && xhr.readyState === 4) {
      try {
        const response = JSON.parse(xhr.responseText);
        if (response.success) {
          showToast('Welcome back, ' . id + ' 🎓');
        } else {
          showToast(response.message || 'Login failed. Please check your credentials.');
        }
      } catch(e) {
        console.error('Error parsing response:', e);
        showToast('An error occurred during login.');
      }
    } else {
      showToast('Server error. Please try again later.');
    }
  };

  xhr.onerror = function() {
    onDone();
    showToast('Network error. Please check your connection.');
  };

  xhr.send(form);
}