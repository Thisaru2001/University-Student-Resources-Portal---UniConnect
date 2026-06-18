<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniConnect • Terms & Privacy</title>
    <link rel="icon" type="image/png" href="./resources/logo.png" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        :root {
            --primary: #1f5335;
            --primary-light: #2d6a4f;
            --primary-lighter: #e8f5e9;
            --primary-bg: #f0fdf4;
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --info: #0ea5e9;
            --info-light: #f0f9ff;
            --card: #ffffff;
            --text: #1a1a2e;
            --text-light: #4b5563;
            --text-muted: #6b7280;
            --border: #d1fae5;
            --radius: 16px;
            --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
            --shadow: 0 4px 6px rgba(0,0,0,0.04), 0 10px 30px rgba(0,0,0,0.06);
            --shadow-lg: 0 20px 60px rgba(0,0,0,0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(180deg, #f0fdf4 0%, #f8faf8 50%, #f0fdf4 100%);
            background-attachment: fixed;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        /* BACK BUTTON */
        .back-button {
            position: fixed;
            top: 25px;
            left: 25px;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: var(--primary);
            border: 2px solid var(--border);
            border-radius: 50px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .back-button:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
            box-shadow: var(--shadow);
        }

        .back-button i {
            font-size: 16px;
        }

        .main-container {
            max-width: 950px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            padding-top: 20px;
        }

        /* TOP HEADER */
        .top-header {
            background: linear-gradient(135deg, #1f5335 0%, #2d6a4f 50%, #3b7a5e 100%);
            border-radius: var(--radius);
            padding: 35px 40px;
            text-align: center;
            color: white;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .top-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: patternMove 20s linear infinite;
        }

        @keyframes patternMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }

        .top-header > * {
            position: relative;
            z-index: 1;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.12);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            border: 2px solid rgba(255,255,255,0.25);
            animation: pulse-icon 3s ease-in-out infinite;
        }

        @keyframes pulse-icon {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,255,0.2); }
            50% { box-shadow: 0 0 0 20px rgba(255,255,255,0); }
        }

        .top-header h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .top-header .subtitle {
            font-size: 15px;
            opacity: 0.9;
            font-weight: 500;
        }

        .badge-row {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            background: rgba(255,255,255,0.15);
            color: #d1fae5;
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* TABS */
        .tabs-container {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            border: 2px solid #d1fae5;
            border-radius: 50px;
            background: white;
            color: var(--text-light);
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .tab-btn i {
            font-size: 16px;
        }

        .tab-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            border-color: #86efac;
        }

        .tab-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .tab-btn.active i {
            color: white;
        }

        /* TAB CONTENT */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Last updated */
        .last-updated {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-lighter);
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        /* CONTENT CARD */
        .content-card {
            background: var(--card);
            border-radius: var(--radius);
            padding: 40px;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            border: 1px solid var(--border);
        }

        /* SECTION STYLING */
        .section {
            margin-bottom: 25px;
            padding: 20px 25px;
            border-radius: var(--radius-sm);
            border: 1px solid #d1fae5;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            background: #fafdfb;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section:hover {
            border-color: #86efac;
            box-shadow: 0 2px 8px rgba(31,83,53,0.06);
            transform: translateX(3px);
            background: #f8fdf9;
        }

        .section::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #1f5335, #2d6a4f);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .section-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            color: white;
            flex-shrink: 0;
            background: linear-gradient(135deg, #1f5335, #2d6a4f);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }

        .section-icon {
            font-size: 20px;
            width: 35px;
            text-align: center;
            flex-shrink: 0;
            color: #1f5335;
        }

        .section p {
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .section ul {
            list-style: none;
            padding-left: 0;
            margin: 10px 0;
        }

        .section ul li {
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.9;
            padding: 6px 0 6px 30px;
            position: relative;
            transition: var(--transition);
        }

        .section ul li:hover {
            color: var(--text);
            transform: translateX(4px);
        }

        .section ul li::before {
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            content: '\f058';
            position: absolute;
            left: 0;
            top: 6px;
            font-size: 14px;
            color: #22c55e;
        }

        /* HIGHLIGHT BOXES */
        .highlight {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin: 14px 0;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.6;
        }

        .highlight i {
            font-size: 17px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .highlight-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }
        .highlight-warning i { color: #f59e0b; }

        .highlight-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .highlight-danger i { color: #dc2626; }

        .highlight-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #075985;
        }
        .highlight-info i { color: #0ea5e9; }

        /* FOOTER */
        .footer-card {
            background: var(--card);
            border-radius: var(--radius);
            padding: 25px 40px;
            box-shadow: var(--shadow-sm);
            text-align: center;
            border: 1px solid var(--border);
        }

        .footer-card p {
            font-size: 14px;
            color: var(--text-muted);
            margin: 0;
        }

        .footer-card .footer-links {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .footer-card .footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: var(--transition);
        }

        .footer-card .footer-links a:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            body {
                padding: 15px 10px;
            }

            .back-button {
                top: 15px;
                left: 15px;
                padding: 10px 16px;
                font-size: 13px;
            }

            .top-header {
                padding: 25px 20px;
            }

            .top-header h1 {
                font-size: 24px;
            }

            .content-card {
                padding: 20px 15px;
            }

            .section {
                padding: 15px;
            }

            .footer-card {
                padding: 20px;
            }

            .badge-row {
                gap: 6px;
            }

            .badge {
                font-size: 10px;
                padding: 5px 10px;
            }

            .tab-btn {
                padding: 10px 18px;
                font-size: 13px;
            }
        }

        @media print {
            body {
                background: white;
            }
            .back-button {
                display: none;
            }
            .content-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>

    <!-- BACK BUTTON -->
    <!-- <a href="signup.php" class="back-button" title="Go Back">
        <i class="fas fa-arrow-left"></i>
        Back
    </a> -->

    <div class="main-container">

        <!-- TOP HEADER -->
        <div class="top-header">
            <div class="header-icon">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h1>Legal Policies</h1>
            <p class="subtitle">University Resource Sharing Platform</p>
            <div class="badge-row">
                <span class="badge">
                    <i class="fas fa-gavel"></i> LEGAL DOCUMENT
                </span>
                <span class="badge">
                    <i class="fas fa-exclamation-triangle"></i> BINDING AGREEMENT
                </span>
                <span class="badge">
                    <i class="fas fa-calendar-check"></i> UPDATED 2026
                </span>
            </div>
        </div>

        <!-- TABS -->
        <div class="tabs-container">
            <button class="tab-btn active" data-tab="terms">
                <i class="fas fa-file-contract"></i> Terms & Conditions
            </button>
            <button class="tab-btn" data-tab="privacy">
                <i class="fas fa-lock"></i> Privacy Policy
            </button>
        </div>

        <!-- Last Updated -->
        <div style="text-align:center;">
            <span class="last-updated" id="lastUpdated">
                <i class="fas fa-clock-rotate-left"></i>
                Last updated: January 2026 • Version 2.0
            </span>
        </div>

        <!-- CONTENT CARD -->
        <div class="content-card">

            <!-- ====== TERMS TAB ====== -->
            <div id="terms" class="tab-content active">
                
                <div class="section">
                    <div class="section-header">
                        <span class="section-number">1</span>
                        <span class="section-icon"><i class="fas fa-handshake"></i></span>
                        <span class="section-title">Acceptance of Terms</span>
                    </div>
                    <p>By registering for, accessing, or using this Platform, you acknowledge that you have <strong>read, understood, and agreed</strong> to these Terms and Conditions. This constitutes a legally binding agreement between you and the Institution.</p>
                    <div class="highlight highlight-warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>If you do not agree with any part of these Terms, you must not register for or use the Platform.</span>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">2</span>
                        <span class="section-icon"><i class="fas fa-user-check"></i></span>
                        <span class="section-title">User Eligibility & Registration</span>
                    </div>
                    <ul>
                        <li>You must be at least <strong>16 years of age</strong> to use this Platform.</li>
                        <li>You must provide <strong>accurate and complete</strong> registration information.</li>
                        <li>You must keep your account credentials <strong>strictly confidential</strong>.</li>
                        <li>You are <strong>prohibited</strong> from sharing, selling, or transferring your account.</li>
                        <li>You must not use another user's account under any circumstances.</li>
                        <li>You are <strong>solely responsible</strong> for all activities under your account.</li>
                    </ul>
                    <div class="highlight highlight-danger">
                        <i class="fas fa-ban"></i>
                        <span><strong>PROHIBITED:</strong> Registering with false information, impersonation, creating multiple accounts, or sharing credentials. Violations result in immediate account termination.</span>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">3</span>
                        <span class="section-icon"><i class="fas fa-tasks"></i></span>
                        <span class="section-title">User Responsibilities</span>
                    </div>
                    <ul>
                        <li>You are <strong>fully responsible</strong> for all content you upload and download.</li>
                        <li>You must comply with all applicable local, national, and international laws.</li>
                        <li>You must have proper authorization or ownership for all uploaded materials.</li>
                        <li>You are responsible for all activities performed using your account credentials.</li>
                        <li>You must immediately report any unauthorized access to administrators.</li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">4</span>
                        <span class="section-icon"><i class="fas fa-shield-alt"></i></span>
                        <span class="section-title">Prohibited Activities</span>
                    </div>
                    <ul>
                        <li>Uploading <strong>copyrighted content</strong> without explicit permission from the owner.</li>
                        <li>Uploading <strong>illegal, harmful, offensive, obscene, or pornographic</strong> content.</li>
                        <li>Distributing <strong>malware, viruses, ransomware, spyware,</strong> or any malicious software.</li>
                        <li>Engaging in <strong>hacking, phishing, identity theft,</strong> or any form of cybercrime.</li>
                        <li><strong>Unauthorized access</strong> to other accounts, systems, or restricted areas.</li>
                        <li>Uploading content that facilitates <strong>cheating, plagiarism, or academic dishonesty.</strong></li>
                        <li><strong>Harassment, bullying, stalking,</strong> or threatening behavior towards other users.</li>
                        <li>Using automated scripts, bots, or scraping tools to access the Platform.</li>
                    </ul>
                    <div class="highlight highlight-danger">
                        <i class="fas fa-skull"></i>
                        <span><strong>ZERO TOLERANCE:</strong> Any of the above activities will result in immediate account termination, referral to academic authorities, and potential criminal prosecution.</span>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">5</span>
                        <span class="section-icon"><i class="fas fa-copyright"></i></span>
                        <span class="section-title">Intellectual Property</span>
                    </div>
                    <p>Users retain ownership of their original content. By uploading, you grant the Platform a license to host and distribute your content within the Platform for educational purposes.</p>
                    <div class="highlight highlight-info">
                        <i class="fas fa-info-circle"></i>
                        <span>The Platform Owners and Administrators are <strong>not responsible</strong> for copyright violations committed by users. Copyright holders may submit takedown requests to the Administrator.</span>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">6</span>
                        <span class="section-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <span class="section-title">Content Disclaimer</span>
                    </div>
                    <p>The Platform only provides storage and sharing facilities. <strong>All content is user-generated.</strong> Users access resources entirely at their own risk. The Institution does not guarantee the accuracy, completeness, or quality of any uploaded resource.</p>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">7</span>
                        <span class="section-icon"><i class="fas fa-scale-balanced"></i></span>
                        <span class="section-title">Limitation of Liability</span>
                    </div>
                    <p>To the maximum extent permitted by law, the <strong>Platform Owners, Administrators, Developers, Contributors, and Affiliates shall not be liable</strong> for any losses, damages, legal claims, or consequences resulting from Platform use. This includes but is not limited to:</p>
                    <ul>
                        <li>Loss of data, profits, or academic opportunities</li>
                        <li>Damages from reliance on uploaded content</li>
                        <li>Any indirect, incidental, or consequential damages</li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">8</span>
                        <span class="section-icon"><i class="fas fa-lock"></i></span>
                        <span class="section-title">Security Disclaimer</span>
                    </div>
                    <p>No online system can guarantee <strong>absolute security</strong>. The Platform Owners and Administrators are <strong>not responsible</strong> for hacking attempts, unauthorized access, cyberattacks, data breaches, or security incidents caused by third parties.</p>
                    <div class="highlight highlight-warning">
                        <i class="fas fa-key"></i>
                        <span>Users are responsible for maintaining the security of their own devices, internet connections, and account credentials.</span>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">9</span>
                        <span class="section-icon"><i class="fas fa-user-shield"></i></span>
                        <span class="section-title">User Indemnification</span>
                    </div>
                    <p>Users agree to <strong>indemnify, defend, and hold harmless</strong> the Platform Owners, Administrators, Developers, and Contributors from any claims, damages, liabilities, or legal expenses arising from user actions, content uploads, or violations of these Terms.</p>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">10</span>
                        <span class="section-icon"><i class="fas fa-user-slash"></i></span>
                        <span class="section-title">Account Suspension & Termination</span>
                    </div>
                    <p>Accounts may be <strong>suspended or permanently terminated</strong> without prior notice for violations of these Terms and Conditions. The Platform reserves the absolute right to:</p>
                    <ul>
                        <li>Remove any content at its sole discretion</li>
                        <li>Suspend or terminate accounts without explanation</li>
                        <li>Report violations to academic authorities or law enforcement</li>
                        <li>Retain evidence of violations for legal proceedings</li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">11</span>
                        <span class="section-icon"><i class="fas fa-circle-exclamation"></i></span>
                        <span class="section-title">No Warranty</span>
                    </div>
                    <p>The Platform is provided <strong>"AS IS" and "AS AVAILABLE"</strong> without warranties of any kind, either express or implied. We do not warrant that the Platform will be uninterrupted, error-free, secure, or free from viruses.</p>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">12</span>
                        <span class="section-icon"><i class="fas fa-eye"></i></span>
                        <span class="section-title">Privacy & Monitoring</span>
                    </div>
                    <p>Users should not upload sensitive personal information. The Platform is not responsible for information voluntarily shared by users. <strong>By using the Platform, you consent to:</strong></p>
                    <ul>
                        <li>Electronic monitoring of all activities</li>
                        <li>Logging of uploads, downloads, and searches</li>
                        <li>Review of account activity by administrators</li>
                        <li>Disclosure of information to academic authorities when required</li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">13</span>
                        <span class="section-icon"><i class="fas fa-rotate"></i></span>
                        <span class="section-title">Changes to Terms</span>
                    </div>
                    <p>The Platform may update these Terms at any time without prior notice. <strong>Continued use after changes constitutes acceptance</strong> of the updated Terms. It is your responsibility to review these Terms periodically.</p>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">14</span>
                        <span class="section-icon"><i class="fas fa-check-double"></i></span>
                        <span class="section-title">Acknowledgement</span>
                    </div>
                    <p>By creating an account or using this Platform, you <strong>acknowledge</strong> that you are solely responsible for your activities and agree <strong>not to hold</strong> the Platform Owners, Administrators, Developers, or Contributors liable for consequences arising from your use.</p>
                    <div class="highlight highlight-info">
                        <i class="fas fa-gavel"></i>
                        <span>This acknowledgement survives the termination of your account and these Terms.</span>
                    </div>
                </div>

            </div><!-- /terms -->

            <!-- ====== PRIVACY TAB ====== -->
            <div id="privacy" class="tab-content">
                
                <div class="section">
                    <div class="section-header">
                        <span class="section-number">1</span>
                        <span class="section-icon"><i class="fas fa-lock"></i></span>
                        <span class="section-title">Information We Collect</span>
                    </div>
                    <p>We collect information you provide directly to us, such as when you create an account, upload resources, or contact support. This includes your name, email address, university ID, and any content you upload.</p>
                    <ul>
                        <li><strong>Account Information:</strong> Name, email, university affiliation, and profile data.</li>
                        <li><strong>Usage Data:</strong> Pages visited, resources viewed, search queries, and download history.</li>
                        <li><strong>Device Information:</strong> IP address, browser type, operating system, and device identifiers.</li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">2</span>
                        <span class="section-icon"><i class="fas fa-people-arrows"></i></span>
                        <span class="section-title">How We Use Your Information</span>
                    </div>
                    <ul>
                        <li>To provide, maintain, and improve the Platform's functionality.</li>
                        <li>To personalize your experience and recommend relevant resources.</li>
                        <li>To monitor usage patterns and enhance security.</li>
                        <li>To communicate important updates, policy changes, or administrative notices.</li>
                        <li>To comply with legal obligations and institutional policies.</li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">3</span>
                        <span class="section-icon"><i class="fas fa-share"></i></span>
                        <span class="section-title">Information Sharing</span>
                    </div>
                    <p>We <strong>do not sell, rent, or trade</strong> your personal information to third parties. Your information is shared only in the following limited circumstances:</p>
                    <ul>
                        <li>With university administrators for academic and security purposes.</li>
                        <li>With service providers who assist in operating the Platform (bound by confidentiality).</li>
                        <li>When required by law or to protect the rights and safety of the Platform and its users.</li>
                        <li>With your explicit consent or at your direction.</li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">4</span>
                        <span class="section-icon"><i class="fas fa-database"></i></span>
                        <span class="section-title">Data Retention</span>
                    </div>
                    <p>We retain your personal information for as long as your account is active or as needed to provide services. After account deletion, certain data may be retained for legal, audit, or institutional purposes.</p>
                    <div class="highlight highlight-info">
                        <i class="fas fa-clock"></i>
                        <span>Users may request deletion of their account and associated data by contacting the Administrator.</span>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">5</span>
                        <span class="section-icon"><i class="fas fa-shield-virus"></i></span>
                        <span class="section-title">Security Measures</span>
                    </div>
                    <p>We implement appropriate technical and organizational measures to protect your data. These include encryption, access controls, secure server infrastructure, and regular security audits. However, no method of transmission over the internet is 100% secure.</p>
                    <div class="highlight highlight-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>While we strive to protect your data, we cannot guarantee absolute security. Users should exercise caution when sharing sensitive information.</span>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">6</span>
                        <span class="section-icon"><i class="fas fa-cookie-bite"></i></span>
                        <span class="section-title">Cookies & Tracking</span>
                    </div>
                    <p>We use cookies and similar tracking technologies to enhance user experience, analyze usage, and maintain session state. You can control cookie preferences through your browser settings.</p>
                    <ul>
                        <li><strong>Essential Cookies:</strong> Required for core functionality (login, session management).</li>
                        <li><strong>Analytics Cookies:</strong> Help us understand how users interact with the Platform.</li>
                        <li><strong>Preference Cookies:</strong> Remember your settings and preferences.</li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">7</span>
                        <span class="section-icon"><i class="fas fa-user-lock"></i></span>
                        <span class="section-title">Your Rights</span>
                    </div>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access the personal information we hold about you.</li>
                        <li>Request correction of inaccurate or incomplete data.</li>
                        <li>Request deletion of your account and associated data.</li>
                        <li>Withdraw consent for certain data processing activities.</li>
                        <li>Request a copy of your data in a portable format.</li>
                    </ul>
                    <p>To exercise any of these rights, please contact the Administrator.</p>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">8</span>
                        <span class="section-icon"><i class="fas fa-child"></i></span>
                        <span class="section-title">Children's Privacy</span>
                    </div>
                    <p>This Platform is not intended for users under the age of 16. We do not knowingly collect personal information from children. If you believe a child has provided us with personal data, please contact us immediately.</p>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">9</span>
                        <span class="section-icon"><i class="fas fa-link"></i></span>
                        <span class="section-title">Third-Party Links</span>
                    </div>
                    <p>The Platform may contain links to third-party websites or services. We are not responsible for the privacy practices or content of these external sites. We encourage you to review their privacy policies before providing any personal information.</p>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">10</span>
                        <span class="section-icon"><i class="fas fa-rotate"></i></span>
                        <span class="section-title">Policy Updates</span>
                    </div>
                    <p>We reserve the right to update this Privacy Policy at any time. Changes will be posted on this page with an updated effective date. Continued use of the Platform after changes constitutes acceptance of the revised policy.</p>
                    <div class="highlight highlight-info">
                        <i class="fas fa-bell"></i>
                        <span>We encourage users to review this Privacy Policy periodically to stay informed about how we protect your information.</span>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">11</span>
                        <span class="section-icon"><i class="fas fa-envelope"></i></span>
                        <span class="section-title">Contact Us</span>
                    </div>
                    <p>If you have any questions, concerns, or requests regarding this Privacy Policy or your data, please contact the Platform Administrator:</p>
                    <ul>
                        <li><strong>Email:</strong> privacy@uniconnect.edu</li>
                        <li><strong>Office:</strong> University Administration Building, Room 210</li>
                        <li><strong>Response Time:</strong> We aim to respond to all inquiries within 7 business days.</li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-header">
                        <span class="section-number">12</span>
                        <span class="section-icon"><i class="fas fa-check-circle"></i></span>
                        <span class="section-title">Consent</span>
                    </div>
                    <p>By using the Platform, you consent to the collection, use, and disclosure of your information as described in this Privacy Policy. If you do not consent to these terms, please discontinue use of the Platform.</p>
                    <div class="highlight highlight-warning">
                        <i class="fas fa-gavel"></i>
                        <span>This Privacy Policy is a legally binding document. Your continued use constitutes acceptance of all terms outlined herein.</span>
                    </div>
                </div>

            </div><!-- /privacy -->

        </div><!-- /content-card -->

        <!-- FOOTER -->
        <div class="footer-card">
            <p>
                <i class="fas fa-copyright"></i> 
                <script>document.write(new Date().getFullYear());</script> 
                University Resource Management System • All Rights Reserved
            </p>
            <div class="footer-links">
                <a href="contact.php"><i class="fas fa-envelope"></i> Contact Us</a>
                <a href="faq.php"><i class="fas fa-question-circle"></i> FAQ</a>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-btn');
            const contents = document.querySelectorAll('.tab-content');
            const lastUpdated = document.getElementById('lastUpdated');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all buttons and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    // Add active class to clicked button
                    this.classList.add('active');

                    // Show corresponding content
                    const targetId = this.getAttribute('data-tab');
                    document.getElementById(targetId).classList.add('active');

                    // Update last updated text
                    if (targetId === 'terms') {
                        lastUpdated.innerHTML = `<i class="fas fa-clock-rotate-left"></i> Last updated: January 2026 • Version 2.0`;
                    } else if (targetId === 'privacy') {
                        lastUpdated.innerHTML = `<i class="fas fa-clock-rotate-left"></i> Last updated: January 2026 • Version 1.2`;
                    }
                });
            });
        });
    </script>

</body>
</html>