<?php
session_start();

$fname = $_SESSION['fname'] ?? 'User';
$student_id = $_SESSION['student_id'] ?? 'XXX';
require_once './backend/connection.php';
Database::setUpConnection();

// Fetch departments
$dept_query = "SELECT department_id, department_name FROM departments ORDER BY department_name";
$dept_result = Database::$connection->query($dept_query);

// Fetch academic years
$year_query = "SELECT year_id, year FROM academic_years ORDER BY year_id";
$year_result = Database::$connection->query($year_query);

// Fetch courses with semester
$course_query = "SELECT c.course_id, c.course_code, c.course_name, c.department_id, c.year_id, c.semester 
                 FROM courses c 
                 ORDER BY c.course_code";
$course_result = Database::$connection->query($course_query);

// Fetch resource types
$type_query = "SELECT id, type FROM type ORDER BY type";
$type_result = Database::$connection->query($type_query);

$history_query = "SELECT r.resource_id, r.file_name, r.uploaded_at 
                  FROM resources r 
                  WHERE r.students_id = ? 
                  ORDER BY r.uploaded_at DESC";
$stmt = Database::$connection->prepare($history_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$history_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard · Resource Hub</title>
  <link rel="icon" type="image/png" href="./resources/logo.png" />
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="./css/student_style.css" />
  <style>
    body {
      background-image: url('./resources/cover.png');
    }

    /* Inline style to support history view without changing external CSS */
    #historyView {
      display: block;
    }

    #historyView.hidden {
      display: none !important;
    }

    .history-table-wrapper {
      background: #ffffff;
      backdrop-filter: none;
      border-radius: 16px;
      padding: 15px;
      margin-top: 15px;
      border: 1px solid rgba(0, 0, 0, 0.08);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .history-table {
      width: 100%;
      border-collapse: collapse;
      color: #333;
    }

    .history-table th {
      text-align: left;
      padding: 16px 12px;
      background: #f8f9fa;
      font-weight: 700;
      color: #1a1a2e;
      border-bottom: 2px solid #e9ecef;
      font-size: 0.95rem;
    }

    .history-table td {
      padding: 14px 12px;
      border-bottom: 1px solid #f0f0f0;
      color: #333;
      font-weight: 500;
      font-size: 0.9rem;
    }

    .history-table tbody tr:hover {
      background: #f8f9fa;
    }

    /* Beautiful red delete button with icon */
    .delete-btn {
      background: #ef4444;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    .delete-btn:hover {
      background: #dc2626;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5);
    }

    .delete-btn:active {
      transform: translateY(0);
      box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }

    .delete-btn i {
      font-size: 0.9rem;
    }

    .history-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1a1a2e;
      margin-bottom: 20px;
      letter-spacing: 0.5px;
    }


    /* Prevent layout shift on student dashboard */
    .dashboard-container {
      max-width: 1400px;
      width: 100%;
      margin: 0 auto;
    }

    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    /* Ensure toast is always on top */
    #toast {
      z-index: 999999 !important;
    }

    /* Admin Panel Styles */
    .admin-highlight {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2)) !important;
      border-left: 3px solid #667eea !important;
    }

    .admin-highlight:hover {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.3), rgba(118, 75, 162, 0.3)) !important;
    }

    #adminView {
      display: block;
    }

    #adminView.hidden {
      display: none !important;
    }

    .admin-dashboard {
      padding: 10px;
    }

    .admin-stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 25px;
    }

    .admin-stat-card {
      background: #ffffff;
      border: 1px solid rgba(0, 0, 0, 0.08);
      border-radius: 16px;
      padding: 25px;
      text-align: center;
      transition: all 0.3s;
      color: #1a1a2e;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .admin-stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1), 0 15px 40px rgba(0, 0, 0, 0.15);
      background: #ffffff;
    }

    .admin-stat-card i {
      font-size: 32px;
      margin-bottom: 12px;
    }

    .admin-stat-value {
      font-size: 36px;
      font-weight: 700;
      color: #1a1a2e;
      margin-bottom: 5px;
    }

    .admin-stat-label {
      font-size: 13px;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 600;
    }

    .admin-chart-section {
      background: #ffffff;
      border: 1px solid rgba(0, 0, 0, 0.08);
      border-radius: 16px;
      padding: 25px;
      margin-bottom: 25px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .admin-chart-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 20px;
      color: #1a1a2e;
    }

    .admin-chart-title i {
      color: #667eea;
      margin-right: 10px;
    }

    .admin-chart-container {
      height: 280px;
      width: 100%;
    }

    .admin-table-section {
      background: #ffffff;
      border: 1px solid rgba(0, 0, 0, 0.08);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .admin-table-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 20px;
      color: #1a1a2e;
    }

    .admin-table-title i {
      color: #667eea;
      margin-right: 10px;
    }

    .admin-table-wrapper {
      overflow-x: auto;
    }

    .admin-table {
      width: 100%;
      border-collapse: collapse;
      color: #fff;
    }

    .admin-table th {
      text-align: left;
      padding: 14px 15px;
      background: #f8f9fa;
      font-weight: 600;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #1a1a2e;
      border-bottom: 2px solid #e9ecef;
    }

    .admin-table td {
      padding: 14px 15px;
      border-bottom: 1px solid #f0f0f0;
      color: #333;
    }

    .admin-table tbody tr:hover {
      background: #f8f9fa;
    }

    .admin-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      display: inline-block;
    }

    .admin-badge-admin {
      background: rgba(237, 137, 54, 0.2);
      color: #ed8936;
      border: 1px solid rgba(237, 137, 54, 0.3);
    }

    .admin-badge-student {
      background: rgba(72, 187, 120, 0.2);
      color: #48bb78;
      border: 1px solid rgba(72, 187, 120, 0.3);
    }

    .admin-badge-deactivated {
      background: rgba(239, 68, 68, 0.2);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .admin-action-btn {
      padding: 7px 14px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      font-size: 12px;
      font-weight: 600;
      transition: all 0.3s;
      margin-right: 5px;
    }

    .admin-btn-promote {
      background: rgba(102, 126, 234, 0.3);
      color: #667eea;
      border: 1px solid rgba(102, 126, 234, 0.3);
    }

    .admin-btn-promote:hover {
      background: rgba(102, 126, 234, 0.5);
    }

    .admin-btn-demote {
      background: rgba(237, 137, 54, 0.3);
      color: #ed8936;
      border: 1px solid rgba(237, 137, 54, 0.3);
    }

    .admin-btn-demote:hover {
      background: rgba(237, 137, 54, 0.5);
    }

    .admin-tabs {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin: 20px 0 10px 0;
    }

    .admin-tab-button {
      background: #ffffff;
      border: 1px solid #d1d5db;
      color: #111827;
      padding: 10px 18px;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.2s ease;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .admin-tab-button.active {
      background: #1f5335;
      color: white;
      border-color: #1f5335;
    }

    .admin-panel-section {
      display: none;
    }

    .admin-panel-section.active {
      display: block;
    }

    .admin-table-section .admin-table th,
    .admin-table-section .admin-table td {
      white-space: nowrap;
    }

    .modal {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.55);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      padding: 20px;
    }

    .modal.active {
      display: flex;
    }

    .modal-card {
      width: min(760px, 100%);
      background: white;
      border-radius: 18px;
      padding: 24px;
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
      position: relative;
      max-height: calc(100vh - 80px);
      overflow-y: auto;
    }

    .modal-card h3 {
      margin-top: 0;
      margin-bottom: 14px;
      font-size: 22px;
      color: #0f172a;
    }

    .modal-close {
      position: absolute;
      top: 18px;
      right: 18px;
      border: none;
      background: transparent;
      font-size: 18px;
      cursor: pointer;
      color: #4b5563;
    }

    .modal-field {
      margin-bottom: 12px;
      font-size: 14px;
      line-height: 1.6;
    }

    .modal-field strong {
      display: inline-block;
      width: 130px;
      color: #111827;
    }

    .modal-textarea {
      width: 100%;
      min-height: 120px;
      border: 1px solid #d1d5db;
      border-radius: 12px;
      padding: 12px 14px;
      font-size: 14px;
      resize: vertical;
      font-family: inherit;
    }

    .modal-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 18px;
    }

    .modal-button {
      border: none;
      border-radius: 12px;
      padding: 10px 16px;
      cursor: pointer;
      font-weight: 700;
      transition: background 0.2s ease;
    }

    .modal-button.primary {
      background: #1f5335;
      color: white;
    }

    .modal-button.danger {
      background: #ef4444;
      color: white;
    }

    .modal-button.secondary {
      background: #f3f4f6;
      color: #111827;
    }

    @media (max-width: 768px) {
      .admin-stats-grid {
        grid-template-columns: 1fr;
      }

      .admin-chart-container {
        height: 220px;
      }
    }

    #pressing.hidden {
      display: none !important;
    }

    /* --- AI CODE BLOCK STYLES --- */
    pre {
      background: #f7f7f8 !important;
      border-radius: 8px !important;
      padding: 20px 15px !important;
      border: 1px solid #e5e5e5 !important;
      position: relative !important;
      margin: 10px 0 !important;
      overflow-x: auto !important;
    }

    code {
      font-family: 'Consolas', 'Courier New', monospace !important;
      font-size: 14px !important;
    }

    .code-copy-wrapper {
      position: relative;
      margin: 15px 0;
    }

    .copy-code-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: #ffffff;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      padding: 4px 12px;
      font-size: 12px;
      font-weight: 600;
      color: #374151;
      cursor: pointer;
      opacity: 0.7;
      transition: all 0.2s ease;
      z-index: 10;
    }

    .copy-code-btn:hover {
      opacity: 1;
      background: #f3f4f6;
    }

    .response-time {
      display: block;
      margin-top: 8px;
      font-size: 12px;
      color: #888;
      opacity: 0.8;
    }

    /* --- Add Clean Language Label (e.g., "python", "php", "js") --- */
    pre[class*="language-"]::before {
      content: attr(class);
      /* This regex removes "language-" so only "python" or "php" remains */
      text-transform: lowercase;
      position: absolute;
      top: -1px;
      left: -1px;
      padding: 20px 35px 20px 15px !important;
      background: #e5e5e5;
      color: #555;
      font-size: 11px;
      font-weight: 600;
      font-family: 'Consolas', 'Courier New', monospace;
      border-top-left-radius: 8px;
      border-bottom-right-radius: 8px;
      border: 1px solid #e5e5e5;
      pointer-events: none;
      /* Allows clicking through the label */
      z-index: 5;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

</head>

<body>

  <div class="dashboard-container">
    <!-- SIDEBAR -->
    <div class="sidebar">
      <div class="logo-area">
        <i class="fas fa-graduation-cap"></i>
        <span>STUDENT<br>DASHBOARD</span>
      </div>

      <!-- navigation items with click handlers (JS) -->
      <div class="nav-item active" id="navHome">
        <i class="fas fa-home"></i> HOME
      </div>
      <div class="nav-item" id="navHISTORY">
        <i class="fas fa-history"></i> HISTORY
      </div>
      <div class="nav-item upload-highlight" id="navUpload">
        <i class="fas fa-plus-circle"></i> UPLOAD RESOURCE
      </div>
      <div class="nav-item" id="navChatbot">
        <i class="fas fa-robot"></i> AI ChatBot
      </div>
      <!-- Admin button - only visible for admins -->
      <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
        <div class="nav-item admin-highlight" id="navAdmin">
          <i class="fas fa-shield-alt"></i> ADMIN PANEL
        </div>
      <?php endif; ?>
      <div class="nav-item sidebar-footer" id="navLogout">
        <i class="fas fa-sign-out-alt"></i> Log out
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
      <!-- top right profile -->
      <div class="top-bar press-chatbot" id="pressing">



        <div class="profile-pill">
          <span class="name"><?php echo htmlspecialchars($fname); ?></span>
          <span class="code"><?php echo htmlspecialchars($student_id); ?></span>
        </div>
      </div>

      <!-- ========= VIEW 1: HOME (RECENT RESOURCES) ========= -->
      <!-- ========= VIEW 1: HOME ========= -->
      <div id="homeView" class="view-section">
        <!-- DIRECT SEARCH (No Reset Button) -->
        <div class="direct-search-wrapper" style="margin-bottom: 20px; background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid rgba(0, 0, 0, 0.06); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);">
          <label style="display: block; font-size: 13px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">
            🔍 DIRECT COURSE SEARCH
          </label>

          <div style="display: flex; align-items: center; position: relative;">
            <input type="text" id="directSearchInput"
              placeholder="Type course code ,name or keywords"
              style="
                width: 100%;
                padding: 12px 120px 12px 16px;
                border: 2px solid #e0e0e0;
                border-radius: 10px;
                font-size: 14px;
                outline: none;
                transition: all 0.3s;
                background: #f8f9fa;
                height: 48px;
            ">

            <button onclick="directSearch()" style="
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: #2d6a4f;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
            height: 38px;
        ">
              <i class="fas fa-search"></i> SEARCH
            </button>
          </div>
        </div>

        <!-- FILTERS SECTION -->
        <!-- FILTERS SECTION -->
        <div class="filter-grid">
          <div class="filter-item">
            <label>DEPARTMENT</label>
            <select id="filterDepartment">
              <option value="">All Departments</option>
              <?php
              mysqli_data_seek($dept_result, 0);
              while ($dept = $dept_result->fetch_assoc()):
              ?>
                <option value="<?php echo $dept['department_id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="filter-item">
            <label>YEAR</label>
            <select id="filterYear">
              <option value="">All Years</option>
              <?php
              mysqli_data_seek($year_result, 0);
              while ($year = $year_result->fetch_assoc()):
              ?>
                <option value="<?php echo $year['year_id']; ?>"><?php echo htmlspecialchars($year['year']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="filter-item">
            <label>SEMESTER</label>
            <select id="filterSemester">
              <option value="">All Semesters</option>
              <?php for ($i = 1; $i <= 8; $i++): ?>
                <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="filter-item">
            <label>TYPE</label>
            <select id="filterType">
              <option value="">All Types</option>
              <?php
              mysqli_data_seek($type_result, 0);
              while ($type = $type_result->fetch_assoc()):
              ?>
                <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['type']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="filter-item">
            <label>COURSE</label>
            <select id="filterCourse" style="width: 400px;">
              <option value="">All Courses</option>
              <!-- Courses will be loaded here via JavaScript -->
            </select>
          </div>
        </div>

        <div class="search-btn-wrapper" style="margin-top: 15px;">
          <button class="search-btn" onclick="searchResources()"><i class="fas fa-filter"></i> APPLY FILTERS & SEARCH</button>
        </div>

        <div class="section-title" id="sectionTitle">RECENT RESOURCES</div>
        <div class="card-grid" id="resourceGrid">
          <!-- Resources loaded here -->
        </div>
      </div>

      <!-- ========= VIEW 2: HISTORY ========= -->
      <div id="historyView" class="view-section hidden">
        <div class="history-title">UPLOAD HISTORY</div>
        <div class="history-table-wrapper">
          <table class="history-table">
            <thead>
              <tr>
                <th>Resource Name</th>
                <th>Date & Time</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="historyTableBody">
              <?php if ($history_result && $history_result->num_rows > 0): ?>
                <?php while ($row = $history_result->fetch_assoc()): ?>
                  <tr id="row-<?php echo $row['resource_id']; ?>">
                    <td><?php echo htmlspecialchars($row['file_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
                    <td>
                      <button class="delete-btn" data-id="<?php echo $row['resource_id']; ?>">
                        <i class="fas fa-trash-alt"></i> Delete
                      </button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr id="emptyRow">
                  <td colspan="3" style="text-align:center; padding: 30px; opacity: 0.7;">
                    No uploads yet.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ========= VIEW 3: UPLOAD RESOURCE ========= -->
      <div id="uploadView" class="view-section hidden">
        <div class="upload-container">
          <div class="upload-title">Upload Resource</div>
          <div class="upload-sub">Submit a New Learning Resource</div>

          <div class="form-group">
            <label>File Name</label>
            <textarea class="form-control" id="customFileName" name="customFileName" rows="1" placeholder="Enter custom file name (without extension)..."></textarea>
          </div>

          <div class="form-row">
            <div class="form-col">
              <label>Department</label>
              <select id="department" name="department" onchange="filterUploadCourses()">
                <option value="">Select Department</option>
                <?php
                // Reset pointer before looping
                mysqli_data_seek($dept_result, 0);
                while ($dept = $dept_result->fetch_assoc()):
                ?>
                  <option value="<?php echo $dept['department_id']; ?>">
                    <?php echo htmlspecialchars($dept['department_name']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-col">
              <label>Academic Year</label>
              <select id="year" name="year" onchange="filterUploadCourses()">
                <option value="">Select Year</option>
                <?php
                mysqli_data_seek($year_result, 0);
                while ($year = $year_result->fetch_assoc()):
                ?>
                  <option value="<?php echo $year['year_id']; ?>">
                    <?php echo htmlspecialchars($year['year']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-col">
              <label>Semester</label>
              <select id="semester" name="semester" onchange="filterUploadCourses()">
                <option value="">Select Semester</option>
                <?php for ($i = 1; $i <= 8; $i++): ?>
                  <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="form-col">
              <label>Course</label>
              <select id="course_id" name="course_id" required>
                <option value="">Select Department & Year & Semester First</option>
                <?php
                mysqli_data_seek($course_result, 0);
                while ($course = $course_result->fetch_assoc()):
                ?>
                  <option value="<?php echo $course['course_id']; ?>"
                    data-dept="<?php echo $course['department_id']; ?>"
                    data-year="<?php echo $course['year_id']; ?>"
                    data-semester="<?php echo $course['semester']; ?>"
                    class="course-option">
                    <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-col">
              <label>Resource Type</label>
              <select id="type_id" name="type_id" required>
                <option value="">Select Type</option>
                <?php
                // Reset pointer before looping
                mysqli_data_seek($type_result, 0);
                while ($type = $type_result->fetch_assoc()):
                ?>
                  <option value="<?php echo $type['id']; ?>">
                    <?php echo htmlspecialchars($type['type']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>

          <!-- drag & drop area -->
          <div class="drag-drop-area" id="dropArea">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>DRAG & DROP FILES OR CLICK TO UPLOAD.</p>
            <small>Supported: PDF, DOC, DOCX, PPT, PPTX, TXT. Max size 50MB.</small>
            <div class="file-selected" id="fileSelected">No file selected</div>
            <input type="file" id="fileInput" style="display: none;" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt">
          </div>
          <!-- bottom actions -->
          <div class="upload-actions">
            <div class="checkbox-group">
              <input type="checkbox" id="anonCheck" name="anonymous_upload">
              <label for="anonCheck">Anonymous upload</label>
            </div>
            <button class="btn-upload-submit" id="uploadSubmitBtn">UPLOAD</button>
          </div>
        </div>
      </div>



      <!-- ========= VIEW 4: AI Assistant ========= -->
      <div id="chatbotView" class="view-section hidden">
        <div class="chatgpt-container">

          <!-- Sidebar -->
<div class="chat-sidebar">
  <div class="chat-sidebar-header">Chats</div>
  <div class="new-chat-btn" id="newChatBtn">
    <i class="fas fa-plus"></i> New Chat
  </div>
  <div class="chat-history" id="chatSessionList">
    <!-- loaded by JS -->
  </div>
  <div style="padding: 10px 12px; border-top: 1px solid rgba(255,255,255,0.08); margin-top: auto;">
    <button id="deleteAllChatsBtn" style="width:100%; background:rgba(239,68,68,0.2); color:#fca5a5; border:1px solid rgba(239,68,68,0.3); border-radius:8px; padding:8px; font-size:12px; font-weight:600; cursor:pointer;">
      <i class="fas fa-trash-alt"></i> Delete All Chats
    </button>
  </div>
</div>

          <!-- Main Chat -->
          <div class="chat-main">

            <!-- Top bar: model selector -->
            <div class="chat-topbar">
              <span class="chat-topbar-title"><i class="fas fa-robot"></i> &nbsp;AI ChatBot</span>
              <div class="model-select-wrapper">
                <label>Model:</label>
                <select id="modelSelect">
                  <option value="qwen3.5:4b" selected>General Chat (Local Qwen)</option>
                  <option value="openai/gpt-oss-120b">GPT-OSS(Groq)</option>
                  <option value="llama-3.3-70b-versatile">Llama 3.3(Smart & Reasoning)</option>
                  <option value="llama-3.1-8b-instant">Llama 3.1(Fast & Lightweight)</option>
                  <option value="meta-llama/llama-4-scout-17b-16e-instruct">Llama 4 Scout (Vision / Image Upload)</option>
                </select>

                <button id="deepThinkBtn" class="deepthink-btn">
                  <i class="fas fa-brain"></i>
                  <span class="btn-text">DeepThink</span>
                </button>

                <button id="webSearchBtn" class="websearch-btn" title="Enable web search for this message">
                  <i class="fas fa-globe"></i>
                  <span class="btn-text">Web Search</span>
                </button>
              </div>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chatMessages">
              <div class="message ai">
                <div class="avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                  Hello! I'm your AI study assistant. Ask me anything about your courses, resources, or topics you're studying.
                </div>
              </div>
            </div>

            <!-- Input -->
            <div class="chat-input-area">
              <div class="chat-input-box">
                <textarea id="chatInput" placeholder="Ask anything..." rows="1"></textarea>
                <div class="input-actions">
                  <label class="attach-btn" title="Attach file">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="chatFileInput" style="display:none;" accept="image/png, image/jpeg, image/gif, image/webp">
                  </label>

                  <!-- ADD THIS LINE -->
                  <img id="imagePreviewThumb" src="" alt="preview" style="display:none; width:34px; height:34px; border-radius:6px; object-fit:cover; border:1px solid #ccc; cursor:pointer;" title="Click to remove" onclick="clearImagePreview()">

                  <button class="send-btn" id="sendBtn" title="Send">
                    <i class="fas fa-paper-plane"></i>
                  </button>
                </div>
              </div>
              <div class="input-hint">AI can make mistakes. Verify important information.</div>
            </div>

          </div>
        </div>
      </div>



      <!-- ========= VIEW 5: ADMIN PANEL ========= -->
      <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
        <div id="adminView" class="view-section hidden">
          <div class="admin-dashboard">
            <!-- Stats Cards -->
            <div class="admin-stats-grid">
              <div class="admin-stat-card">
                <i class="fas fa-users" style="color: #667eea;"></i>
                <div class="admin-stat-value" id="totalStudents">0</div>
                <div class="admin-stat-label">Total Students</div>
              </div>
              <div class="admin-stat-card">
                <i class="fas fa-file-alt" style="color: #48bb78;"></i>
                <div class="admin-stat-value" id="totalResources">0</div>
                <div class="admin-stat-label">Total Resources</div>
              </div>
              <div class="admin-stat-card">
                <i class="fas fa-user-shield" style="color: #ed8936;"></i>
                <div class="admin-stat-value" id="totalAdmins">0</div>
                <div class="admin-stat-label">Admins</div>
              </div>
            </div>

            <!-- Chart -->
            <div class="admin-chart-section">
              <div class="admin-chart-title">
                <i class="fas fa-chart-line"></i> Student Registrations (Last 12 Months)
              </div>
              <div class="admin-chart-container">
                <canvas id="adminStudentChart"></canvas>
              </div>
            </div>

            <!-- Students Table -->
            <div class="admin-tabs">
              <button class="admin-tab-button active" id="adminTabOverview">Overview</button>
              <button class="admin-tab-button" id="adminTabHistory">System History</button>
              <button class="admin-tab-button" id="adminTabReports">Reported Resources</button>
            </div>

            <div class="admin-panel-section active" id="adminOverviewSection">
              <div class="admin-table-section">
                <div class="admin-table-title">
                  <i class="fas fa-list"></i> All Students
                </div>
                <div class="admin-table-wrapper">
                  <table class="admin-table">
                    <thead>
                      <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Resources</th>
                        <th>Joined</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody id="adminStudentTableBody">
                      <tr>
                        <td colspan="6" style="text-align:center; padding:30px;">Loading...</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="admin-panel-section" id="adminHistorySection">
              <div class="admin-table-section">
                <div class="admin-table-title">
                  <i class="fas fa-history"></i> Resource Upload History
                </div>
                <div class="admin-table-wrapper">
                  <table class="admin-table">
                    <thead>
                      <tr>
                        <th>Resource</th>
                        <th>Owner</th>
                        <th>Course</th>
                        <th>Type</th>
                        <th>Uploaded At</th>
                        <th>Reports</th>
                        <th>Details</th>
                      </tr>
                    </thead>
                    <tbody id="adminHistoryTableBody">
                      <tr>
                        <td colspan="7" style="text-align:center; padding:30px;">Loading...</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="admin-panel-section" id="adminReportsSection">
              <div class="admin-table-section">
                <div class="admin-table-title">
                  <i class="fas fa-flag"></i> Reported Resources
                </div>
                <div class="admin-table-wrapper">
                  <table class="admin-table">
                    <thead>
                      <tr>
                        <th>Resource</th>
                        <th>Owner</th>
                        <th>Course</th>
                        <th>Reported By</th>
                        <th>Reason</th>
                        <th>Reported At</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody id="adminReportsTableBody">
                      <tr>
                        <td colspan="7" style="text-align:center; padding:30px;">Loading...</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
  <script>
    // ============ PASSED FROM PHP ============
    const currentUserId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
  </script>
  <script src="./script/script.js"></script>

  <div class="modal" id="resourceDetailsModal">
    <div class="modal-card">
      <button class="modal-close" type="button" onclick="closeModal('resourceDetailsModal')">&times;</button>
      <h3>Resource Details</h3>
      <div id="resourceDetailsContent">
        <p>Loading resource details...</p>
      </div>
      <div class="modal-actions">
        <button class="modal-button secondary" type="button" onclick="closeModal('resourceDetailsModal')">Close</button>
      </div>
    </div>
  </div>

  <div class="modal" id="resourceReportModal">
    <div class="modal-card">
      <button class="modal-close" type="button" onclick="closeModal('resourceReportModal')">&times;</button>
      <h3>Report Resource</h3>
      <div class="modal-field">
        <strong>Resource ID:</strong> <span id="reportResourceId">N/A</span>
      </div>
      <div class="modal-field">
        <strong>Reason:</strong>
        <textarea id="reportReason" class="modal-textarea" placeholder="Explain why this resource is inappropriate..."></textarea>
      </div>
      <div class="modal-actions">
        <button class="modal-button secondary" type="button" onclick="closeModal('resourceReportModal')">Cancel</button>
        <button class="modal-button primary" type="button" onclick="submitResourceReport()">Submit Report</button>
      </div>
    </div>
  </div>

  <script>
    // ============ DIRECT SEARCH (No Reset) ============
    // ============ DIRECT SEARCH (Universal Search) ============
    function directSearch() {
      const searchTerm = document.getElementById('directSearchInput').value.trim();

      if (!searchTerm) {
        loadRecentResources();
        return;
      }

      document.getElementById('resourceGrid').innerHTML = `
        <div style="grid-column: 1/-1; text-align:center; padding: 40px;">
            <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #2d6a4f;"></i>
            <p style="margin-top: 15px;">Searching for "${searchTerm}" across all resources...</p>
        </div>
    `;

      fetch(`./backend/searchAll.php?keyword=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('sectionTitle').textContent = `SEARCH RESULTS (${data.count} found)`;
            displayResources(data.resources);
          } else {
            throw new Error(data.message || 'Search failed');
          }
        })
        .catch(error => {
          console.error('Search error:', error);
          document.getElementById('resourceGrid').innerHTML = `
                <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #c0392b;">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px;"></i>
                    <p>Error searching. Please try again.</p>
                </div>
            `;
        });
    }

    // ============ APPLY FILTERS SEARCH ============
    // ============ APPLY FILTERS SEARCH ============
    function searchResources() {
      const department_id = document.getElementById('filterDepartment').value;
      const year_id = document.getElementById('filterYear').value;
      const semester = document.getElementById('filterSemester').value;
      const course_id = document.getElementById('filterCourse').value;
      const type_id = document.getElementById('filterType').value;

      const params = new URLSearchParams();
      if (department_id) params.append('department_id', department_id);
      if (year_id) params.append('year_id', year_id);
      if (semester) params.append('semester', semester);
      if (course_id) params.append('course_id', course_id);
      if (type_id) params.append('type_id', type_id);

      document.getElementById('resourceGrid').innerHTML = `
        <div style="grid-column: 1/-1; text-align:center; padding: 40px;">
            <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #2d6a4f;"></i>
            <p style="margin-top: 15px;">Applying filters...</p>
        </div>
    `;

      fetch('./backend/searchResources.php?' + params.toString())
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('sectionTitle').textContent = `FILTER RESULTS (${data.count} found)`;
            displayResources(data.resources);
          } else {
            throw new Error(data.message || 'Search failed');
          }
        })
        .catch(error => {
          console.error('Filter search error:', error);
          document.getElementById('resourceGrid').innerHTML = `
                <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #c0392b;">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px;"></i>
                    <p>Error applying filters. Please try again.</p>
                </div>
            `;
        });
    }
    // ============ LOAD RECENT RESOURCES ============
    function loadRecentResources() {
      fetch('./backend/get_recent_resources.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('sectionTitle').textContent = 'RECENT RESOURCES';
            displayResources(data.resources);
          }
        })
        .catch(error => console.error('Error loading recent resources:', error));
    }

    // ============ SEARCH RESOURCES ============
    // function searchResources() {
    //   const department_id = document.getElementById('filterDepartment').value;
    //   const year_id = document.getElementById('filterYear').value;
    //   const semester = document.getElementById('filterSemester').value;
    //   const course_id = document.getElementById('filterCourse').value;
    //   const type_id = document.getElementById('filterType').value;

    //   const params = new URLSearchParams();
    //   if (department_id) params.append('department_id', department_id);
    //   if (year_id) params.append('year_id', year_id);
    //   if (semester) params.append('semester', semester);
    //   if (course_id) params.append('course_id', course_id);
    //   if (type_id) params.append('type_id', type_id);

    //   document.getElementById('resourceGrid').innerHTML = `
    //     <div style="grid-column: 1/-1; text-align:center; padding: 40px;">
    //         <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #2d6a4f;"></i>
    //         <p style="margin-top: 15px;">Searching resources...</p>
    //     </div>
    // `;

    //   fetch('searchResources.php?' + params.toString())
    //     .then(response => {
    //       if (!response.ok) throw new Error('Network response was not ok');
    //       return response.json();
    //     })
    //     .then(data => {
    //       if (data.success) {
    //         document.getElementById('sectionTitle').textContent = `SEARCH RESULTS (${data.count} found)`;
    //         displayResources(data.resources);
    //       } else {
    //         throw new Error(data.message || 'Search failed');
    //       }
    //     })
    //     .catch(error => {
    //       console.error('Search error:', error);
    //       document.getElementById('resourceGrid').innerHTML = `
    //         <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #c0392b;">
    //             <i class="fas fa-exclamation-circle" style="font-size: 48px;"></i>
    //             <p>Error searching resources. Please try again.</p>
    //         </div>
    //     `;
    //     });
    // }

    // ============ DISPLAY RESOURCES ============
    function displayResources(resources) {
      const grid = document.getElementById('resourceGrid');

      if (!resources || resources.length === 0) {
        grid.innerHTML = `
            <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #547a61;">
                <i class="fas fa-folder-open" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                <p style="font-size: 16px; font-weight: 500;">No resources found</p>
                <p style="font-size: 14px; opacity: 0.7;">Try different search keywords</p>
            </div>
        `;
        return;
      }

      let html = '';
      resources.forEach(resource => {
        let iconClass = 'pdf';
        let iconIcon = 'fa-file-pdf';
        if (resource.file_ext === 'doc' || resource.file_ext === 'docx') {
          iconClass = 'docx';
          iconIcon = 'fa-file-word';
        } else if (resource.file_ext === 'ppt' || resource.file_ext === 'pptx') {
          iconClass = 'pptx';
          iconIcon = 'fa-file-powerpoint';
        } else if (resource.file_ext === 'txt') {
          iconClass = 'pdf';
          iconIcon = 'fa-file-alt';
        }

        html += `
            <div class="resource-card">
                <div class="file-icon ${iconClass}">
                    <i class="fas ${iconIcon}"></i>
                </div>
                <div class="card-title">${escapeHtml(resource.file_name)}</div>
                <div class="meta-line"><strong>Course:</strong> ${escapeHtml(resource.course_code)} - ${escapeHtml(resource.course_name)}</div>
              
                <div class="meta-line"><strong>Type:</strong> ${escapeHtml(resource.resource_type)}</div>
                <div class="uploader">Uploader: ${escapeHtml(resource.uploader_display)}</div>
                <div class="date">Date &nbsp; ${resource.date_display}</div>
                <div class="resource-actions" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:12px;">
                  <button class="download-btn" onclick="window.location.href='${escapeHtml(resource.file_path)}'">
                    <i class="fas fa-download"></i> DOWNLOAD ${resource.file_size ? '(' + resource.file_size + ')' : ''}
                  </button>
                  <button class="download-btn" style="background:#2563eb;" onclick="showResourceDetails(${resource.resource_id})">
                    <i class="fas fa-info-circle"></i> DETAILS
                  </button>
                  <button class="download-btn" style="background:#ef4444;" onclick="openReportModal(${resource.resource_id})">
                    <i class="fas fa-flag"></i> REPORT
                  </button>
                </div>
            </div>
        `;
      });

      grid.innerHTML = html;
    }

    function escapeHtml(text) {
      if (!text) return '';
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    // ============ SEARCH COURSE LOADING ============
    // ============ SEARCH COURSE LOADING (Updated) ============
    function loadSearchCourses() {
      const department_id = document.getElementById('filterDepartment').value;
      const year_id = document.getElementById('filterYear').value;
      const semester = document.getElementById('filterSemester').value;

      const courseSelect = document.getElementById('filterCourse');
      courseSelect.innerHTML = '<option value="">All Courses</option>';

      if (!department_id || !year_id || !semester) return;

      fetch(`./backend/get_courses.php?department_id=${department_id}&year_id=${year_id}&semester=${semester}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            data.courses.forEach(course => {
              // Display both course code AND course name
              courseSelect.innerHTML += `<option value="${course.course_id}">${course.course_code} - ${course.course_name}</option>`;
            });
          }
        })
        .catch(error => console.error('Error loading courses:', error));
    }

    // ============ UPLOAD COURSE FILTER (FIXED) ============
    function filterUploadCourses() {
      const deptId = document.getElementById('department').value;
      const yearId = document.getElementById('year').value;
      const semesterId = document.getElementById('semester').value;
      const courseSelect = document.getElementById('course_id');
      const allOptions = courseSelect.querySelectorAll('.course-option');

      // Hide and disable all course options
      allOptions.forEach(opt => {
        opt.disabled = true;
        opt.hidden = true;
      });

      const defaultOption = courseSelect.querySelector('option:first-child');

      if (!deptId || !yearId || !semesterId) {
        defaultOption.textContent = 'Select Department & Year & Semester First';
        courseSelect.value = '';
        return;
      }

      let hasOptions = false;
      allOptions.forEach(opt => {
        const optDept = opt.getAttribute('data-dept');
        const optYear = opt.getAttribute('data-year');
        const optSemester = opt.getAttribute('data-semester');
        if (optDept === deptId && optYear === yearId && optSemester === semesterId) {
          opt.disabled = false;
          opt.hidden = false;
          hasOptions = true;
        }
      });

      if (!hasOptions) {
        defaultOption.textContent = 'No courses available for this selection';
      } else {
        defaultOption.textContent = 'Select Course';
      }
      courseSelect.value = '';
    }

    // ============ INITIALIZATION ============
    document.addEventListener('DOMContentLoaded', function() {
      // Load recent resources
      loadRecentResources();

      // Search course loading for dropdown
      const filterDept = document.getElementById('filterDepartment');
      const filterYear = document.getElementById('filterYear');
      const filterSem = document.getElementById('filterSemester');

      if (filterDept) filterDept.addEventListener('change', loadSearchCourses);
      if (filterYear) filterYear.addEventListener('change', loadSearchCourses);
      if (filterSem) filterSem.addEventListener('change', loadSearchCourses);

      // Enter key for direct search
      const directSearchInput = document.getElementById('directSearchInput');
      if (directSearchInput) {
        directSearchInput.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            directSearch();
          }
        });
      }

      // Enter key for filter search
      document.querySelectorAll('#homeView select').forEach(select => {
        select.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            searchResources();
          }
        });
      });
    });
  </script>

</body>

</html>