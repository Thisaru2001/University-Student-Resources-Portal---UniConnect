<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard · Resource Hub</title>
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
      background: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      padding: 15px;
      margin-top: 15px;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .history-table {
      width: 100%;
      border-collapse: collapse;
      color: #ffffff;
    }
    .history-table th {
      text-align: left;
      padding: 16px 12px;
      background: rgba(37, 99, 235, 0.5);
      font-weight: 700;
      color: #ffffff;
      border-bottom: 2px solid rgba(255, 255, 255, 0.3);
      font-size: 0.95rem;
    }
    .history-table td {
      padding: 14px 12px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      color: #ffffff;
      font-weight: 500;
      font-size: 0.9rem;
    }
    .history-table tbody tr:hover {
      background: rgba(255, 255, 255, 0.08);
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
      color: #3f5e4b;
      margin-bottom: 20px;
      letter-spacing: 0.5px;
    }
  </style>
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
    <div class="nav-item sidebar-footer" id="navLogout">
      <i class="fas fa-sign-out-alt"></i> Log out
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <!-- top right profile -->
    <div class="top-bar">
      <div class="profile-pill">
        <span class="name">Sarah J.</span>
        <span class="code">CSE-401</span>
      </div>
    </div>

    <!-- ========= VIEW 1: HOME (RECENT RESOURCES) ========= -->
    <div id="homeView" class="view-section">
      <div class="filter-grid">
        <div class="filter-item">
          <label>DEPARTMENT</label>
          <select><option>CSE</option><option>ECE</option><option>ME</option></select>
        </div>
        <div class="filter-item">
          <label>YEAR</label>
          <select><option>3rd Year</option><option>2nd Year</option><option>4th Year</option></select>
        </div>
        <div class="filter-item">
          <label>SEMESTER</label>
          <select><option>Sem 6</option><option>Sem 5</option><option>Sem 4</option></select>
        </div>
        <div class="filter-item">
          <label>COURSE</label>
          <select><option>All</option><option>CSE-401</option><option>CSL-400</option></select>
        </div>
        <div class="filter-item">
          <label>TYPE</label>
          <select><option>All</option><option>PDF</option><option>DOCX</option></select>
        </div>
      </div>

      <div class="search-btn-wrapper">
        <button class="search-btn"><i class="fas fa-search"></i> SEARCH</button>
      </div>

      <div class="section-title">RECENT RESOURCES</div>

      <div class="card-grid" id="resourceGrid">
        <!-- card 1: Compiler Design -->
        <div class="resource-card">
          <div class="file-icon pdf"><i class="fas fa-file-pdf"></i></div>
          <div class="card-title">Compiler Design Lecture Notes - Week 8</div>
          <div class="meta-line"><strong>hidden:</strong> CSE-KOE</div>
          <div class="uploader">Uploader: John Doe</div>
          <div class="date">Date &nbsp; 20 Oct 2023</div>
          <button class="download-btn">DOWNLOAD (3.1 MB)</button>
        </div>

        <!-- card 2: Algorithms -->
        <div class="resource-card">
          <div class="file-icon docx"><i class="fas fa-file-word"></i></div>
          <div class="card-title">Algorithms Final Project Guidelines</div>
          <div class="meta-line"><strong>hidden:</strong> Mary Smith</div>
          <div class="uploader">Uploader: Mary Smith</div>
          <div class="date">Date &nbsp; 18 Oct 2023</div>
          <button class="download-btn">DOWNLOAD (1.2 MB)</button>
        </div>

        <!-- card 3: DBMS -->
        <div class="resource-card">
          <div class="file-icon pptx"><i class="fas fa-file-powerpoint"></i></div>
          <div class="card-title">DBMS Normalization Techniques Slides</div>
          <div class="meta-line"><strong>Hidden:</strong> CSL-400</div>
          <div class="uploader">Uploader: Prof. Lee</div>
          <div class="date">Date &nbsp; 16 Oct 2023</div>
          <button class="download-btn">DOWNLOAD (15.7 MB)</button>
        </div>

        <!-- card 4: Computer Networks -->
        <div class="resource-card">
          <div class="file-icon pdf"><i class="fas fa-file-pdf"></i></div>
          <div class="card-title">Computer Networks Lab Manual - Ex 5</div>
          <div class="meta-line"><strong>Hidden:</strong> CSE-401</div>
          <div class="uploader">Uploader: Sarah Jenkins</div>
          <div class="date">Date &nbsp; 14 Oct 2023</div>
          <button class="download-btn">DOWNLOAD (2.8 MB)</button>
        </div>
      </div>
    </div>

    <!-- ========= VIEW 2: HISTORY (UPDATED WITH BEAUTIFUL DELETE BUTTONS) ========= -->
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
          <tbody>
            <tr>
              <td>Compiler Design Lecture Notes</td>
              <td>2024-12-25 14:30:00</td>
              <td><button class="delete-btn" onclick="deleteResource('Compiler Design Lecture Notes')"><i class="fas fa-trash-alt"></i> Delete</button></td>
            </tr>
            <tr>
              <td>Algorithms Final Project</td>
              <td>2024-12-25 14:30:00</td>
              <td><button class="delete-btn" onclick="deleteResource('Algorithms Final Project')"><i class="fas fa-trash-alt"></i> Delete</button></td>
            </tr>
            <tr>
              <td>DBMS Normalization Slides</td>
              <td>2024-12-25 14:30:00</td>
              <td><button class="delete-btn" onclick="deleteResource('DBMS Normalization Slides')"><i class="fas fa-trash-alt"></i> Delete</button></td>
            </tr>
            <tr>
              <td>Computer Networks Lab</td>
              <td>2024-12-25 14:30:00</td>
              <td><button class="delete-btn" onclick="deleteResource('Computer Networks Lab')"><i class="fas fa-trash-alt"></i> Delete</button></td>
            </tr>
            <tr>
              <td>Operating Systems Assignment</td>
              <td>2024-12-25 14:30:00</td>
              <td><button class="delete-btn" onclick="deleteResource('Operating Systems Assignment')"><i class="fas fa-trash-alt"></i> Delete</button></td>
            </tr>
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
          <input type="text" class="form-control" value="Advanced Algorithms Lecture Notes">
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea class="form-control" rows="2" placeholder="Add a short description..."></textarea>
        </div>

        <div class="form-row">
          <div class="form-col">
            <label>Department</label>
            <select><option>CSE</option><option>ECE</option></select>
          </div>
          <div class="form-col">
            <label>Academic Year</label>
            <select><option>3rd Year</option><option>2nd Year</option></select>
          </div>
          <div class="form-col">
            <label>Semester</label>
            <select><option>Sem 6</option><option>Sem 5</option></select>
          </div>
          <div class="form-col">
            <label>Course Field</label>
            <select><option>PHNS 32562-Elective</option><option>CSE-401</option></select>
          </div>
          <div class="form-col">
            <label>Resource Type</label>
            <select>
              <option>Lecture Notes</option>
              <option>Exam paper</option>
              <option>Assignment</option>
              <option>Tutorial</option>
            </select>
          </div>
        </div>

        <!-- drag & drop area -->
        <div class="drag-drop-area" id="dropArea">
          <i class="fas fa-cloud-upload-alt"></i>
          <p>DRAG & DROP FILES OR CLICK TO UPLOAD.</p>
          <small>Supported: PDF, DOCX, PPTX, JPG, PNG. Max size 20MB.</small>
          <div class="file-selected" id="fileSelected">No file selected</div>
        </div>

        <!-- bottom actions -->
        <div class="upload-actions">
          <div class="checkbox-group">
            <input type="checkbox" id="anonCheck">
            <label for="anonCheck">Anonymous upload</label>
          </div>
          <button class="btn-upload-submit" id="uploadSubmitBtn">UPLOAD</button>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ========= JAVASCRIPT: VIEW TOGGLE & INTERACTIONS ========= -->
<script>
  // Delete function for history items
  function deleteResource(resourceName) {
    if (confirm(`Are you sure you want to delete "${resourceName}"?`)) {
      alert(`✅ "${resourceName}" has been deleted successfully!`);
      // Here you can add actual delete logic (remove row, API call, etc.)
      // Example: event.target.closest('tr').remove();
    }
  }

  (function() {
    const homeView = document.getElementById('homeView');
    const historyView = document.getElementById('historyView');
    const uploadView = document.getElementById('uploadView');
    const navHome = document.getElementById('navHome');
    const navHISTORY = document.getElementById('navHISTORY');
    const navUpload = document.getElementById('navUpload');
    const navLogout = document.getElementById('navLogout');

    // Helper to switch views (now includes history)
    function showView(viewId) {
      // hide all views
      homeView.classList.add('hidden');
      historyView.classList.add('hidden');
      uploadView.classList.add('hidden');

      // show selected view
      if (viewId === 'home') {
        homeView.classList.remove('hidden');
      } else if (viewId === 'history') {
        historyView.classList.remove('hidden');
      } else if (viewId === 'upload') {
        uploadView.classList.remove('hidden');
      }

      // update sidebar active states (remove all first)
      navHome.classList.remove('active', 'upload-highlight');
      navHISTORY.classList.remove('active', 'upload-highlight');
      navUpload.classList.remove('active', 'upload-highlight');

      if (viewId === 'home') {
        navHome.classList.add('active');
      } else if (viewId === 'history') {
        navHISTORY.classList.add('active');
      } else if (viewId === 'upload') {
        navUpload.classList.add('upload-highlight'); // keep green highlight
      }
    }

    // Event listeners for navigation
    navHome.addEventListener('click', function(e) {
      showView('home');
    });

    navHISTORY.addEventListener('click', function(e) {
      showView('history');
    });

    navUpload.addEventListener('click', function(e) {
      showView('upload');
    });

    // Logout simulation
    navLogout.addEventListener('click', function(e) {
      alert('👋 Logging out ... (simulated)');
    });

    // Simulate file selection / drag & drop (simple)
    const dropArea = document.getElementById('dropArea');
    const fileSelected = document.getElementById('fileSelected');
    if (dropArea) {
      dropArea.addEventListener('click', function() {
        const fakeFiles = ['Lecture_Notes.pdf', 'Assignment.docx', 'Slides.pptx'];
        const picked = fakeFiles[Math.floor(Math.random() * fakeFiles.length)];
        fileSelected.innerText = '📎 ' + picked + ' (2.4 MB)';
      });
    }

    // Upload button simulation
    document.getElementById('uploadSubmitBtn')?.addEventListener('click', function() {
      alert('✅ Resource uploaded successfully!');
      showView('home');
    });

    // download buttons (existing)
    document.querySelectorAll('.download-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        alert(`⬇️ Downloading: ${this.innerText.trim()}`);
      });
    });

    // search button simulation
    document.querySelector('.search-btn')?.addEventListener('click', function(e) {
      e.preventDefault();
      alert('🔍 Search filters applied (demo)');
    });

  })();
</script>

</body>
</html>