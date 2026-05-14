  function handleSignIn(e) {
    e.preventDefault();
    const id  = document.getElementById('uid').value.trim();
    const pwd = document.getElementById('pwd').value.trim();
    if (!id || !pwd) { showToast('Please fill in both fields.'); return; }

    const btn = document.querySelector('.btn');
    btn.disabled = true;
    btn.querySelector('.btn-inner').innerHTML =
      `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        style="animation:spin .7s linear infinite">
        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83
                 M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
      </svg> Signing in…`;

    setTimeout(() => {
      showToast('Welcome back, ' + id + ' 🎓');
      btn.disabled = false;
      btn.querySelector('.btn-inner').innerHTML =
        `Sign In <svg class="arrow" width="17" height="17" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14M13 6l6 6-6 6"/></svg>`;
    }, 1400);
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