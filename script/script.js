// ============ TOAST SYSTEM ============
(function() {
  const toastStyles = document.createElement('style');
  toastStyles.textContent = `
    #toast {
      position: fixed;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%) translateY(100px);
      background: rgba(166, 29, 14, 0.95);
      color: white;
      padding: 14px 28px;
      border-radius: 50px;
      font-size: 15px;
      font-weight: 600;
      z-index: 99999;
      pointer-events: none;
      opacity: 0;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      white-space: nowrap;
      letter-spacing: 0.3px;
      border: 1px solid rgba(46, 204, 113, 0.3);
    }
    #toast.show {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }
  `;
  document.head.appendChild(toastStyles);
  
  const t = document.createElement('div');
  t.id = 'toast';
  document.body.appendChild(t);
})();

function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// ============ SIGN IN FUNCTION ============
function handleSignIn(e) {
  e.preventDefault();
  const id = document.getElementById('uid').value.trim();
  const pwd = document.getElementById('pwd').value.trim();
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

// ============ ENTER KEY HANDLER ============
document.addEventListener('DOMContentLoaded', function() {
    const uidInput = document.getElementById('uid');
    if (uidInput) {
        uidInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') { 
                e.preventDefault(); 
                document.getElementById('pwd').focus(); 
            }
        });
    }
});

// ============ VALIDATE CREDENTIALS ============
async function validateCredentials(id, pwd, remember, onDone) {
  const formData = new FormData();
  formData.append("id", id);
  formData.append("pwd", pwd);
  formData.append("remember", remember);

  try {
    const response = await fetch("./backend/signinProcess.php", { method: "POST", body: formData });
    onDone();
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const data = await response.json();
    if (data.success) {
     showToast(`Welcome back, ${data.fname} 🎓`);
      setTimeout(() => { window.location.href = 'student.php'; }, 500);
    } else {
      showToast(data.message || 'Login failed. Please check your credentials.');
    }
  } catch (error) {
    onDone();
    if (error.name === 'TypeError') {
      showToast('Network error. Please check your connection.');
    } else {
      console.error('Error:', error);
      showToast('An error occurred during login.');
    }
  }
}

// ============ SIGN UP FUNCTION ============
function handleSignUp(e) {
  e.preventDefault();
  const student_id = document.getElementById('student_id').value.trim();
  const fname = document.getElementById('fname').value.trim();
  const email = document.getElementById('email').value.trim();
  const pwd = document.getElementById('pwd').value.trim();
  
  if (!student_id || !fname || !email || !pwd) {
    showToast('Please fill in all fields.');
    return;
  }

  const btn = document.querySelector('.btn');
  btn.disabled = true;
  btn.querySelector('.btn-inner').innerHTML =
    `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
      style="animation:spin .7s linear infinite">
      <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83
               M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
    </svg> Signing up…`;

  const formData = new FormData();
  formData.append("student_id", student_id);
  formData.append("fname", fname);
  formData.append("email", email);
  formData.append("pwd", pwd);

  fetch("./backend/signupProcess.php", { method: "POST", body: formData })
  .then(response => response.json())
  .then(data => {
    btn.disabled = false;
    btn.querySelector('.btn-inner').innerHTML =
      `Sign Up <svg class="arrow" width="17" height="17" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M5 12h14M13 6l6 6-6 6"/></svg>`;
    if (data.success) {
      showToast(data.message);
      setTimeout(() => { window.location.href = data.redirect || 'student.php'; }, 1000);
    } else {
      showToast(data.message);
    }
  })
  .catch(error => {
    btn.disabled = false;
    btn.querySelector('.btn-inner').innerHTML =
      `Sign Up <svg class="arrow" width="17" height="17" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M5 12h14M13 6l6 6-6 6"/></svg>`;
    showToast('Network error. Please check your connection.');
  });
}

// ============ COURSE FILTER FUNCTION ============
function filterCourses() {
    const deptId = document.getElementById('department').value;
    const yearId = document.getElementById('year').value;
    const semesterId = document.getElementById('semester').value;
    const courseSelect = document.getElementById('course_id');
    const allOptions = courseSelect.querySelectorAll('.course-option');

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

// ============ DELETE FUNCTION FOR HISTORY ============
function deleteResource(resourceName) {
    if (confirm(`Are you sure you want to delete "${resourceName}"?`)) {
        fetch('delete_resource.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ resource_name: resourceName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`✅ "${resourceName}" has been deleted successfully!`);
                const buttons = document.querySelectorAll('.delete-btn');
                buttons.forEach(btn => {
                    if (btn.onclick && btn.onclick.toString().includes(resourceName)) {
                        btn.closest('tr').remove();
                    }
                });
            } else {
                alert('Error deleting resource. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        });
    }
}

// ============ ADMIN PANEL FUNCTIONS (GLOBAL SCOPE) ============

function loadAdminData() {
    fetch('./backend/get_admin_data.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalStudents').textContent = data.student_count;
            document.getElementById('totalResources').textContent = data.resource_count;
            document.getElementById('totalAdmins').textContent = data.admin_count;
            populateAdminTable(data.students);
            drawAdminChart(data.months, data.counts);
        }
    })
    .catch(error => console.error('Error loading admin data:', error));
}

function populateAdminTable(students) {
    const tbody = document.getElementById('adminStudentTableBody');
    if (!tbody) return;
    
    if (!students || students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:30px;">No students found</td></tr>';
        return;
    }
    
    let html = '';
    students.forEach(student => {
        let statusClass = '';
        let statusText = '';
        let buttonHtml = '';
        let isAdmin = student.admin == 1;
        let isCurrentUser = student.id == currentUserId;//this variable is set in the PHP code to identify the logged-in user
        
        if (student.status == 0) {
            statusClass = 'admin-badge-deactivated';
            statusText = 'Deactivated';
            if (!isAdmin || isCurrentUser) {
                buttonHtml = `<button class="admin-action-btn admin-btn-promote" onclick="toggleAdminStatus(${student.id}, 'activate')">
                    <i class="fas fa-check"></i> Activate
                </button>`;
            }
        } else if (isAdmin) {
            statusClass = 'admin-badge-admin';
            statusText = 'Admin';
            if (!isCurrentUser) {
                buttonHtml = `<button class="admin-action-btn admin-btn-demote" onclick="toggleAdminStatus(${student.id}, 'demote')">
                    <i class="fas fa-user-minus"></i> Remove Admin
                </button>`;
            }
        } else {
            statusClass = 'admin-badge-student';
            statusText = 'Student';
            buttonHtml = `
                <button class="admin-action-btn admin-btn-promote" onclick="toggleAdminStatus(${student.id}, 'promote')">
                    <i class="fas fa-user-plus"></i> Make Admin
                </button>
                <button class="admin-action-btn admin-btn-demote" onclick="toggleAdminStatus(${student.id}, 'deactivate')" style="background:rgba(239,68,68,0.3);color:#ef4444;">
                    <i class="fas fa-ban"></i> Deactivate
                </button>
            `;
        }
        
        // Format DateTime properly
        let createdDate = 'N/A';
        if (student.created_at) {
            const dateObj = new Date(student.created_at);
            createdDate = dateObj.toLocaleDateString() + ' ' + dateObj.toLocaleTimeString();
        }
        
        html += `
            <tr>
                <td><strong>${escapeHtml(student.student_id)}</strong></td>
                <td>${escapeHtml(student.fname)}</td>
                <td><span class="admin-badge ${statusClass}">${statusText}</span></td>
                <td>${student.resource_count}</td>
                <td><small>${createdDate}</small></td>
                <td>${buttonHtml}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function drawAdminChart(months, counts) {
    const canvas = document.getElementById('adminStudentChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    if (window.adminChart) {
        window.adminChart.destroy();
    }
    
    // Check if all values are 0
    const hasData = counts.some(count => count > 0);
    
    // If no data, show a message
    if (!hasData) {
        const parent = canvas.parentElement;
        parent.innerHTML = `
            <div style="display:flex; align-items:center; justify-content:center; height:280px; color:#666;">
                <div style="text-align:center;">
                    <i class="fas fa-chart-line" style="font-size:48px; color:#ccc; margin-bottom:15px;"></i>
                    <p>No registration data available yet</p>
                    <p style="font-size:14px; opacity:0.7;">Data will appear when students register</p>
                </div>
            </div>
        `;
        return;
    }
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(102, 126, 234, 0.4)');
    gradient.addColorStop(1, 'rgba(102, 126, 234, 0)');
    
    window.adminChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Students',
                data: counts,
                borderColor: '#667eea',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' student' + (context.parsed.y !== 1 ? 's' : '');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: '#666',
                        font: { size: 12 }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    title: {
                        display: true,
                        text: 'Number of Students',
                        color: '#666',
                        font: { size: 12 }
                    }
                },
                x: {
                    ticks: {
                        color: '#666',
                        font: { size: 11 },
                        maxRotation: 45,
                        minRotation: 0
                    },
                    grid: { display: false },
                    title: {
                        display: true,
                        text: 'Month',
                        color: '#666',
                        font: { size: 12 }
                    }
                }
            }
        }
    });
}

function toggleAdminStatus(studentId, action) {
    console.log('toggleAdminStatus called:', studentId, action); // Debug
    
    const messages = {
        'promote': 'Make this student an admin?',
        'demote': 'Remove admin privileges from this user?',
        'activate': 'Activate this student account?',
        'deactivate': 'Deactivate this student account?'
    };
    
    if (confirm(messages[action])) {
        fetch('./backend/update_student_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: studentId, action: action })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response:', data); // Debug
            if (data.success) {
                showToast(data.message || 'Status updated!');
                loadAdminData(); // Refresh table
            } else {
                showToast('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Network error. Please try again.');
        });
    }
}

// ============ MAIN STUDENT DASHBOARD ============
document.addEventListener('DOMContentLoaded', function() {
    const homeView = document.getElementById('homeView');
    if (!homeView) return;

    const historyView = document.getElementById('historyView');
    const uploadView = document.getElementById('uploadView');
    const navHome = document.getElementById('navHome');
    const navHISTORY = document.getElementById('navHISTORY');
    const navUpload = document.getElementById('navUpload');
    const navLogout = document.getElementById('navLogout');

    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('fileInput');
    const fileSelected = document.getElementById('fileSelected');
    const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');
    let selectedFile = null;

    function showView(viewId) {
        homeView.classList.add('hidden');
        historyView.classList.add('hidden');
        uploadView.classList.add('hidden');
        const adminView = document.getElementById('adminView');
        if (adminView) adminView.classList.add('hidden');

        if (viewId === 'home') homeView.classList.remove('hidden');
        else if (viewId === 'history') historyView.classList.remove('hidden');
        else if (viewId === 'upload') uploadView.classList.remove('hidden');
        else if (viewId === 'admin' && adminView) {
            adminView.classList.remove('hidden');
            loadAdminData();
        }

        navHome.classList.remove('active', 'upload-highlight', 'admin-highlight');
        navHISTORY.classList.remove('active', 'upload-highlight', 'admin-highlight');
        navUpload.classList.remove('active', 'upload-highlight', 'admin-highlight');
        const navAdmin = document.getElementById('navAdmin');
        if (navAdmin) navAdmin.classList.remove('active', 'upload-highlight', 'admin-highlight');

        if (viewId === 'home') navHome.classList.add('active');
        else if (viewId === 'history') navHISTORY.classList.add('active');
        else if (viewId === 'upload') navUpload.classList.add('upload-highlight');
        else if (viewId === 'admin' && navAdmin) navAdmin.classList.add('admin-highlight');
    }

    const navAdmin = document.getElementById('navAdmin');
    if (navAdmin) {
        navAdmin.addEventListener('click', () => showView('admin'));
    }

    navHome.addEventListener('click', () => showView('home'));
    navHISTORY.addEventListener('click', () => showView('history'));
    navUpload.addEventListener('click', () => showView('upload'));
    navLogout.addEventListener('click', () => {
        if (confirm('Are you sure you want to logout?')) {
           window.location.href = '../backend/logout.php';
        }
    });

    // History delete
    const historyTableBody = document.querySelector('#historyTableBody');
    if (historyTableBody) {
        historyTableBody.addEventListener('click', function(e) {
            const btn = e.target.closest('.delete-btn');
            if (!btn) return;
            const resourceId = btn.dataset.id;
            const row = document.getElementById('row-' + resourceId);
            if (!row) return;
            const fileName = row.querySelector('td').textContent;
            if (!confirm(`Are you sure you want to delete "${fileName}"?`)) return;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            fetch('delete_resource.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ resource_id: resourceId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    const tbody = document.getElementById('historyTableBody');
                    if (tbody.querySelectorAll('tr').length === 0) {
                        tbody.innerHTML = `<tr id="emptyRow"><td colspan="3" style="text-align:center; padding:30px; opacity:0.7;">No uploads yet.</td></tr>`;
                    }
                } else {
                    alert('Error: ' + (data.message || 'Could not delete resource.'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trash-alt"></i> Delete';
                }
            })
            .catch(() => {
                alert('Network error. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-alt"></i> Delete';
            });
        });
    }

    // File upload handlers
    if (dropArea) {
        dropArea.addEventListener('click', function(e) {
            if (!e.target.closest('.file-selected')) fileInput.click();
        });
    }
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) {
                selectedFile = this.files[0];
                fileSelected.innerHTML = `📎 ${selectedFile.name} (${(selectedFile.size / 1024 / 1024).toFixed(1)} MB)`;
            }
        });
    }
    if (dropArea) {
        dropArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#2d6a4f';
            this.style.backgroundColor = 'rgba(45, 106, 79, 0.1)';
        });
        dropArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#2d6a4f';
            this.style.backgroundColor = 'transparent';
        });
        dropArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#2d6a4f';
            this.style.backgroundColor = 'transparent';
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                selectedFile = files[0];
                fileSelected.innerHTML = `📎 ${selectedFile.name} (${(selectedFile.size / 1024 / 1024).toFixed(1)} MB)`;
            }
        });
    }
    if (uploadSubmitBtn) {
        uploadSubmitBtn.addEventListener('click', function() {
            if (!selectedFile) {
                showToast('Please select a file to upload');
                return;
            }
           const customFileName = document.getElementById('customFileName').value.trim();
            const typeId = document.getElementById('type_id').value;
            const courseId = document.getElementById('course_id').value;
            const isAnonymous = document.getElementById('anonCheck').checked;
            if (!typeId || !courseId) {
                showToast('Please select both Course and Resource Type');
                return;
            }
            const formData = new FormData();
            formData.append('file', selectedFile);
           formData.append('custom_file_name', customFileName);
            formData.append('type_id', typeId);
            formData.append('course_id', courseId);
            formData.append('anonymous_upload', isAnonymous ? '1' : '0');
            this.disabled = true;
            const originalText = this.textContent;
            this.textContent = 'UPLOADING...';
            fetch('./backend/resourceRegisterProcess.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Resource uploaded successfully!');
                    document.getElementById('customFileName').value = '';
                    document.getElementById('type_id').value = '';
                    document.getElementById('course_id').value = '';
                    document.getElementById('anonCheck').checked = false;
                    fileSelected.innerHTML = 'No file selected';
                    selectedFile = null;
                    if (fileInput) fileInput.value = '';
                } else {
                    showToast('Upload failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showToast('Error uploading: ' + error.message);
            })
            .finally(() => {
                this.disabled = false;
                this.textContent = originalText;
            });
        });
    }

    showView('home');
});