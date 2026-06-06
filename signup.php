<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UniConnect • Sign Up</title>
  <link rel="icon" type="image/png" href="./resources/logo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="./css/signin_signup_style.css" />
</head>

<body>

  <div class="card">

    <!-- ── LEFT: IMAGE ── -->
    <div class="img-panel">
      <!-- Replace with your own image path -->
      <img src="./resources/signin.png" alt="Campus" />

      <div class="img-logo">
        <span class="pulse"></span>
        UniConnect
      </div>

      <div class="img-body">
        <h2>Learn boldly,<br><em>Rise together</em></h2>
        <div class="img-divider"></div>
        <p>Your gateway to courses, community<br>and academic resources.</p>
        <div class="pills">
          <span class="pill">Courses</span>
          <span class="pill">Community</span>
          <span class="pill">Repository</span>
          <span class="pill">Support</span>
        </div>
      </div>
    </div>

    <!-- ── RIGHT: FORM ── -->
    <div class="form-panel">
      <div class="form-inner">

        <div class="brand">
          <div class="brand-mark">
            <svg viewBox="0 0 24 24">
              <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
              <path d="M6 12v5c3 3 9 3 12 0v-5" />
            </svg>
          </div>
          <div class="brand-text">Uni<span>Connect</span></div>
        </div>

        <h1 class="form-heading">Welcome👋</h1>
        <p class="form-sub">Sign Up to your student portal and continue your journey.</p>

        <form id="signupForm" onsubmit="handleSignUp(event)" novalidate>

          <div class="field">
            <label for="student_id">University ID</label>
            <div class="iw">
              <input type="text" id="student_id" name="student_id" placeholder="XX/XXXX/XXX" required autocomplete="off" />
              <svg viewBox="0 0 24 24">
                <rect x="3" y="5" width="18" height="14" rx="2" />
                <path d="M3 10h18M8 3v4M16 3v4" />
              </svg>
            </div>
          </div>

          <div class="field">
            <label for="fname">First Name</label>
            <div class="iw">
              <input type="text" id="fname" name="fname" placeholder="Isuru" required autocomplete="given-name" />
              <svg viewBox="0 0 24 24">
                <rect x="3" y="5" width="18" height="14" rx="2" />
                <path d="M3 10h18M8 3v4M16 3v4" />
              </svg>
            </div>
          </div>

          <div class="field">
            <label for="pwd">New Password</label>
            <div class="iw">
              <input type="password" id="pwd" name="pwd" placeholder="••••••••" required autocomplete="new-password" />
              <svg viewBox="0 0 24 24">
                <rect x="5" y="11" width="14" height="10" rx="2" />
                <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                <circle cx="12" cy="16" r="1" fill="#a3c9b0" stroke="none" />
              </svg>
            </div>
          </div>

          <div class="field">
            <label for="email">Email</label>
            <div class="iw">
              <input type="email" id="email" name="email" placeholder="Isuru@gmail.com" required autocomplete="email" />
              <svg viewBox="0 0 24 24">
                <rect x="3" y="5" width="18" height="14" rx="2" />
                <path d="M3 10h18M8 3v4M16 3v4" />
              </svg>
            </div>
          </div>

          <button type="submit" class="btn">
            <span class="btn-inner">
              Sign Up
              <svg class="arrow" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M13 6l6 6-6 6" />
              </svg>
            </span>
          </button>
          <span>Already have an account? <a href="index.php" class="createAcc">Sign In</a></span>

        </form>

        <div class="form-footer">
          <div class="secure">
            <span class="sdot"></span>
            Encrypted
          </div>
          <span>© 2026 UniConnect</span>
        </div>

      </div>
    </div>

  </div>



  <script src="./script/script.js"></script>
</body>

</html>