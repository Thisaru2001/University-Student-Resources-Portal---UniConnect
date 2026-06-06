<?php
session_start();
$prefilled_uid = $_GET['uid'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background-image: url('./resources/cover.png');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            width: 400px;
            max-width: 90%;
        }
        h2 {
            text-align: center;
            color: #1a1a2e;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: #2d6a4f;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #2d6a4f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1b4332;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: none;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
            display: block;
            color: #2d6a4f;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .otp-section {
            display: none;
        }
        .timer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }
        .resend-btn {
            background: transparent;
            color: #2d6a4f;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: underline;
        }
        .resend-btn:disabled {
            color: #999;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🔐 Forgot Password</h2>
    
    <div id="message" class="message"></div>
    
    <!-- Step 1: Enter Student ID -->
    <div id="step1">
        <div class="form-group">
            <label>Student ID</label>
            <input type="text" id="student_id" placeholder="Enter your student ID" required
                   value="<?php echo htmlspecialchars($prefilled_uid); ?>">
        </div>
        <button class="btn" onclick="sendOTP()">
            <i class="fas fa-paper-plane"></i> Send OTP
        </button>
    </div>
    
    <!-- Step 2: Enter OTP and New Password -->
    <div id="step2" class="otp-section">
        <div class="form-group">
            <label>OTP Code</label>
            <input type="text" id="otp_code" placeholder="Enter OTP sent to your email" maxlength="6">
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" id="new_password" placeholder="Enter new password">
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" id="confirm_password" placeholder="Confirm new password">
        </div>
        <button class="btn" onclick="verifyOTP()">
            <i class="fas fa-key"></i> Reset Password
        </button>
        <div class="timer">
            <span id="timerDisplay">60</span> seconds remaining
            <br>
            <button class="resend-btn" id="resendBtn" onclick="resendOTP()" disabled>Resend OTP</button>
        </div>
    </div>
    
    <a href="index.php" class="back-link">← Back to Sign In</a>
</div>

<script>
    let timerInterval;
    let timeLeft = 60;
    let studentId = '';

    function showMessage(text, type) {
        const msg = document.getElementById('message');
        msg.className = 'message ' + type;
        msg.textContent = text;
        msg.style.display = 'block';
        setTimeout(() => {
            msg.style.display = 'none';
        }, 5000);
    }

    function sendOTP() {
        studentId = document.getElementById('student_id').value.trim();
        if (!studentId) {
            showMessage('Please enter your student ID', 'error');
            return;
        }

        const btn = document.querySelector('#step1 .btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        fetch('send_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: studentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('OTP sent to your email!', 'success');
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
                startTimer();
            } else {
                showMessage(data.message || 'Failed to send OTP', 'error');
            }
        })
        .catch(error => {
            showMessage('Network error. Please try again.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
        });
    }

    function startTimer() {
        timeLeft = 60;
        document.getElementById('resendBtn').disabled = true;
        document.getElementById('timerDisplay').textContent = timeLeft;
        
        if (timerInterval) clearInterval(timerInterval);
        
        timerInterval = setInterval(() => {
            timeLeft--;
            document.getElementById('timerDisplay').textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById('resendBtn').disabled = false;
            }
        }, 1000);
    }

    function resendOTP() {
        sendOTP();
    }

    function verifyOTP() {
        const otp = document.getElementById('otp_code').value.trim();
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (!otp) {
            showMessage('Please enter the OTP', 'error');
            return;
        }
        if (newPassword.length < 6) {
            showMessage('Password must be at least 6 characters', 'error');
            return;
        }
        if (newPassword !== confirmPassword) {
            showMessage('Passwords do not match', 'error');
            return;
        }

        const btn = document.querySelector('#step2 .btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';

        fetch('verify_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                student_id: studentId,
                otp: otp,
                new_password: newPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('✅ Password updated successfully! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            } else {
                showMessage(data.message || 'Failed to reset password', 'error');
            }
        })
        .catch(error => {
            showMessage('Network error. Please try again.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-key"></i> Reset Password';
        });
    }
</script>

</body>
</html>