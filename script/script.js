// ============ TOAST SYSTEM ============
(function () {
  const toastStyles = document.createElement("style");
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

  const t = document.createElement("div");
  t.id = "toast";
  document.body.appendChild(t);
})();

function showToast(msg) {
  const t = document.getElementById("toast");
  if (!t) return;
  t.textContent = msg;
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 3000);
}
// Function to add copy buttons to all code blocks
function addCopyButtonsToCodeBlocks() {
  document.querySelectorAll("#chatMessages pre").forEach((preBlock) => {
    // Prevent adding duplicate buttons
    if (preBlock.parentElement.classList.contains("code-copy-wrapper")) return;

    // Wrap the pre in a div so we can position the button properly
    const wrapper = document.createElement("div");
    wrapper.className = "code-copy-wrapper";
    preBlock.parentNode.insertBefore(wrapper, preBlock);
    wrapper.appendChild(preBlock);

    // Create the button
    const btn = document.createElement("button");
    btn.className = "copy-code-btn";
    btn.innerHTML = "📋 Copy";

    btn.onclick = function () {
      const code = preBlock.querySelector("code").innerText;
      navigator.clipboard.writeText(code).then(() => {
        btn.innerHTML = "✅ Copied!";
        setTimeout(() => {
          btn.innerHTML = "📋 Copy";
        }, 2000);
      });
    };
    wrapper.prepend(btn);
  });
}

// Call this function AFTER your typeWord() finishes or after setting innerHTML
// For example, add it at the very end of your sendMessage success block:
// addCopyButtonsToCodeBlocks();
function escapeHtml(text) {
  if (!text) return "";
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

// ============ SIGN IN FUNCTION ============
function handleSignIn(e) {
  e.preventDefault();
  const id = document.getElementById("uid").value.trim();
  const pwd = document.getElementById("pwd").value.trim();
  const remember = document.getElementById("remember").checked;
  if (!id || !pwd) {
    showToast("Please fill in both fields.");
    return;
  }

  const btn = document.querySelector(".btn");
  btn.disabled = true;
  btn.querySelector(".btn-inner").innerHTML =
    `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
      style="animation:spin .7s linear infinite">
      <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83
               M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
    </svg> Signing in…`;

  validateCredentials(id, pwd, remember, function () {
    btn.disabled = false;
    btn.querySelector(".btn-inner").innerHTML =
      `Sign In <svg class="arrow" width="17" height="17" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M5 12h14M13 6l6 6-6 6"/></svg>`;
  });
}

// ============ ENTER KEY HANDLER ============
document.addEventListener("DOMContentLoaded", function () {
  const uidInput = document.getElementById("uid");
  if (uidInput) {
    uidInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        document.getElementById("pwd").focus();
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
    const response = await fetch("./backend/signinProcess.php", {
      method: "POST",
      body: formData,
    });
    onDone();
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const data = await response.json();
    if (data.success) {
      showToast(`Welcome back, ${data.fname} 🎓`);
      setTimeout(() => {
        window.location.href = "student.php";
      }, 500);
    } else {
      showToast(data.message || "Login failed. Please check your credentials.");
    }
  } catch (error) {
    onDone();
    if (error.name === "TypeError") {
      showToast("Network error. Please check your connection.");
    } else {
      console.error("Error:", error);
      showToast("An error occurred during login.");
    }
  }
}

// ============ SIGN UP FUNCTION ============
function handleSignUp(e) {
  e.preventDefault();
  const student_id = document.getElementById("student_id").value.trim();
  const fname = document.getElementById("fname").value.trim();
  const email = document.getElementById("email").value.trim();
  const pwd = document.getElementById("pwd").value.trim();
  const tcp=document.getElementById("tcp").checked;

  if (!student_id || !fname || !email || !pwd) {
    showToast("Please fill in all fields.");
    return;
  }

  const btn = document.querySelector(".btn");
  btn.disabled = true;
  btn.querySelector(".btn-inner").innerHTML =
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
  formData.append("tcp", tcp ? "1" : "0");

  fetch("./backend/signupProcess.php", { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      btn.disabled = false;
      btn.querySelector(".btn-inner").innerHTML =
        `Sign Up <svg class="arrow" width="17" height="17" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M5 12h14M13 6l6 6-6 6"/></svg>`;
      if (data.success) {
        showToast(data.message);
        setTimeout(() => {
          window.location.href = data.redirect || "student.php";
        }, 1000);
      } else {
        showToast(data.message);
      }
    })
    .catch((error) => {
      btn.disabled = false;
      btn.querySelector(".btn-inner").innerHTML =
        `Sign Up <svg class="arrow" width="17" height="17" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M5 12h14M13 6l6 6-6 6"/></svg>`;
      showToast("Network error. Please check your connection.");
    });
}

// ============ COURSE FILTER FUNCTION ============
function filterCourses() {
  const deptId = document.getElementById("department").value;
  const yearId = document.getElementById("year").value;
  const semesterId = document.getElementById("semester").value;
  const courseSelect = document.getElementById("course_id");
  const allOptions = courseSelect.querySelectorAll(".course-option");

  allOptions.forEach((opt) => {
    opt.disabled = true;
    opt.hidden = true;
  });

  const defaultOption = courseSelect.querySelector("option:first-child");

  if (!deptId || !yearId || !semesterId) {
    defaultOption.textContent = "Select Department & Year & Semester First";
    courseSelect.value = "";
    return;
  }

  let hasOptions = false;
  allOptions.forEach((opt) => {
    const optDept = opt.getAttribute("data-dept");
    const optYear = opt.getAttribute("data-year");
    const optSemester = opt.getAttribute("data-semester");
    if (
      optDept === deptId &&
      optYear === yearId &&
      optSemester === semesterId
    ) {
      opt.disabled = false;
      opt.hidden = false;
      hasOptions = true;
    }
  });

  if (!hasOptions) {
    defaultOption.textContent = "No courses available for this selection";
  } else {
    defaultOption.textContent = "Select Course";
  }
  courseSelect.value = "";
}

// ============ DELETE FUNCTION FOR HISTORY ============
function deleteResource(resourceName) {
  if (confirm(`Are you sure you want to delete "${resourceName}"?`)) {
    fetch("delete_resource.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ resource_name: resourceName }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(`✅ "${resourceName}" has been deleted successfully!`);
          const buttons = document.querySelectorAll(".delete-btn");
          buttons.forEach((btn) => {
            if (btn.onclick && btn.onclick.toString().includes(resourceName)) {
              btn.closest("tr").remove();
            }
          });
        } else {
          alert("Error deleting resource. Please try again.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Network error. Please try again.");
      });
  }
}

// ============ ADMIN PANEL FUNCTIONS (GLOBAL SCOPE) ============

function loadAdminData() {
  fetch("./backend/get_admin_data.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("totalStudents").textContent =
          data.student_count;
        document.getElementById("totalResources").textContent =
          data.resource_count;
        document.getElementById("totalAdmins").textContent = data.admin_count;
        populateAdminTable(data.students);
        drawAdminChart(data.months, data.counts);
      }
    })
    .catch((error) => console.error("Error loading admin data:", error));
}

function populateAdminTable(students) {
  const tbody = document.getElementById("adminStudentTableBody");
  if (!tbody) return;

  if (!students || students.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="6" style="text-align:center; padding:30px;">No students found</td></tr>';
    return;
  }

  let html = "";
  students.forEach((student) => {
    let statusClass = "";
    let statusText = "";
    let buttonHtml = "";
    let isAdmin = student.admin == 1;
    let isCurrentUser = student.id == currentUserId; //this variable is set in the PHP code to identify the logged-in user

    if (student.status == 0) {
      statusClass = "admin-badge-deactivated";
      statusText = "Deactivated";
      if (!isAdmin || isCurrentUser) {
        buttonHtml = `<button class="admin-action-btn admin-btn-promote" onclick="toggleAdminStatus(${student.id}, 'activate')">
                    <i class="fas fa-check"></i> Activate
                </button>`;
      }
    } else if (isAdmin) {
      statusClass = "admin-badge-admin";
      statusText = "Admin";
      if (!isCurrentUser) {
        buttonHtml = `<button class="admin-action-btn admin-btn-demote" onclick="toggleAdminStatus(${student.id}, 'demote')">
                    <i class="fas fa-user-minus"></i> Remove Admin
                </button>`;
      }
    } else {
      statusClass = "admin-badge-student";
      statusText = "Student";
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
    let createdDate = "N/A";
    if (student.created_at) {
      const dateObj = new Date(student.created_at);
      createdDate =
        dateObj.toLocaleDateString() + " " + dateObj.toLocaleTimeString();
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
  const canvas = document.getElementById("adminStudentChart");
  if (!canvas) return;

  const ctx = canvas.getContext("2d");

  if (window.adminChart) {
    window.adminChart.destroy();
  }

  // Check if all values are 0
  const hasData = counts.some((count) => count > 0);

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
  gradient.addColorStop(0, "rgba(102, 126, 234, 0.4)");
  gradient.addColorStop(1, "rgba(102, 126, 234, 0)");

  window.adminChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: months,
      datasets: [
        {
          label: "New Students",
          data: counts,
          borderColor: "#667eea",
          backgroundColor: gradient,
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: "#667eea",
          pointBorderColor: "#fff",
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 7,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function (context) {
              return (
                context.parsed.y +
                " student" +
                (context.parsed.y !== 1 ? "s" : "")
              );
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            color: "#666",
            font: { size: 12 },
          },
          grid: {
            color: "rgba(0,0,0,0.05)",
          },
          title: {
            display: true,
            text: "Number of Students",
            color: "#666",
            font: { size: 12 },
          },
        },
        x: {
          ticks: {
            color: "#666",
            font: { size: 11 },
            maxRotation: 45,
            minRotation: 0,
          },
          grid: { display: false },
          title: {
            display: true,
            text: "Month",
            color: "#666",
            font: { size: 12 },
          },
        },
      },
    },
  });
}

function toggleAdminStatus(studentId, action) {
  console.log("toggleAdminStatus called:", studentId, action); // Debug

  const messages = {
    promote: "Make this student an admin?",
    demote: "Remove admin privileges from this user?",
    activate: "Activate this student account?",
    deactivate: "Deactivate this student account?",
  };

  if (confirm(messages[action])) {
    fetch("./backend/update_student_status.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ student_id: studentId, action: action }),
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("Response:", data); // Debug
        if (data.success) {
          showToast(data.message || "Status updated!");
          loadAdminData(); // Refresh table
        } else {
          showToast("Error: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showToast("Network error. Please try again.");
      });
  }
}

// ============ MAIN STUDENT DASHBOARD ============
document.addEventListener("DOMContentLoaded", function () {
  const homeView = document.getElementById("homeView");
  if (!homeView) return;

  const pressingChatBot = document.getElementById("pressing");

  const historyView = document.getElementById("historyView");
  const uploadView = document.getElementById("uploadView");
  const chatbotView = document.getElementById("chatbotView");
  const navHome = document.getElementById("navHome");
  const navHISTORY = document.getElementById("navHISTORY");
  const navUpload = document.getElementById("navUpload");
  const navChatbot = document.getElementById("navChatbot");
  const navLogout = document.getElementById("navLogout");

  const dropArea = document.getElementById("dropArea");
  const fileInput = document.getElementById("fileInput");
  const fileSelected = document.getElementById("fileSelected");
  const uploadSubmitBtn = document.getElementById("uploadSubmitBtn");
  let selectedFile = null;

  function showView(viewId) {
    homeView.classList.add("hidden");
    historyView.classList.add("hidden");
    uploadView.classList.add("hidden");
    chatbotView.classList.add("hidden");
    const adminView = document.getElementById("adminView");
    if (adminView) adminView.classList.add("hidden");
    pressingChatBot.classList.remove("hidden");
    if (viewId === "home") {
      homeView.classList.remove("hidden");
    } else if (viewId === "history") {
      historyView.classList.remove("hidden");
    } else if (viewId === "upload") {
      uploadView.classList.remove("hidden");
    } else if (viewId === "chatbot") {
      chatbotView.classList.remove("hidden");
      pressingChatBot.classList.add("hidden");
    } else if (viewId === "admin" && adminView) {
      adminView.classList.remove("hidden");
      loadAdminData();
      setActiveAdminSection("adminOverviewSection");
    }

    navHome.classList.remove("active", "admin-highlight");
    navHISTORY.classList.remove("active", "admin-highlight");
    navChatbot.classList.remove("active", "admin-highlight");
    navUpload.classList.remove("active", "admin-highlight");
    const navAdmin = document.getElementById("navAdmin");
    if (navAdmin)
      navAdmin.classList.remove(
        "active",
        "upload-highlight",
        "admin-highlight",
      );

    if (viewId === "home") navHome.classList.add("active");
    else if (viewId === "history") navHISTORY.classList.add("active");
    else if (viewId === "upload") navUpload.classList.add("active");
    else if (viewId === "chatbot") navChatbot.classList.add("active");
    else if (viewId === "admin" && navAdmin)
      navAdmin.classList.add("admin-highlight");
  }

  const navAdmin = document.getElementById("navAdmin");
  if (navAdmin) {
    navAdmin.addEventListener("click", () => showView("admin"));
  }

  const adminTabOverview = document.getElementById("adminTabOverview");
  const adminTabHistory = document.getElementById("adminTabHistory");
  const adminTabReports = document.getElementById("adminTabReports");

  if (adminTabOverview) {
    adminTabOverview.addEventListener("click", () =>
      setActiveAdminSection("adminOverviewSection"),
    );
  }
  if (adminTabHistory) {
    adminTabHistory.addEventListener("click", () =>
      setActiveAdminSection("adminHistorySection"),
    );
  }
  if (adminTabReports) {
    adminTabReports.addEventListener("click", () =>
      setActiveAdminSection("adminReportsSection"),
    );
  }

  navHome.addEventListener("click", () => showView("home"));
  navHISTORY.addEventListener("click", () => showView("history"));
  navUpload.addEventListener("click", () => showView("upload"));
  navChatbot.addEventListener("click", () => showView("chatbot"));
  navLogout.addEventListener("click", () => {
    if (confirm("Are you sure you want to logout?")) {
      window.location.href = "./backend/logout.php";
    }
  });

  // History delete
  const historyTableBody = document.querySelector("#historyTableBody");
  if (historyTableBody) {
    historyTableBody.addEventListener("click", function (e) {
      const btn = e.target.closest(".delete-btn");
      if (!btn) return;
      const resourceId = btn.dataset.id;
      const row = document.getElementById("row-" + resourceId);
      if (!row) return;
      const fileName = row.querySelector("td").textContent;
      if (!confirm(`Are you sure you want to delete "${fileName}"?`)) return;
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
      fetch("delete_resource.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ resource_id: resourceId }),
      })
        .then((r) => r.json())
        .then((data) => {
          if (data.success) {
            row.remove();
            const tbody = document.getElementById("historyTableBody");
            if (tbody.querySelectorAll("tr").length === 0) {
              tbody.innerHTML = `<tr id="emptyRow"><td colspan="3" style="text-align:center; padding:30px; opacity:0.7;">No uploads yet.</td></tr>`;
            }
          } else {
            alert("Error: " + (data.message || "Could not delete resource."));
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash-alt"></i> Delete';
          }
        })
        .catch(() => {
          alert("Network error. Please try again.");
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-trash-alt"></i> Delete';
        });
    });
  }

  function openReportModal(resourceId) {
    const modal = document.getElementById("resourceReportModal");
    const resourceIdSpan = document.getElementById("reportResourceId");
    const reportReason = document.getElementById("reportReason");
    resourceIdSpan.textContent = resourceId;
    reportReason.value = "";
    modal.classList.add("active");
  }

  function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove("active");
  }

  function submitResourceReport() {
    const modal = document.getElementById("resourceReportModal");
    const resourceId = parseInt(
      document.getElementById("reportResourceId").textContent,
      10,
    );
    const reason = document.getElementById("reportReason").value.trim();

    if (!reason) {
      alert("Please provide a reason for the report.");
      return;
    }

    fetch("./backend/report_resource.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ resource_id: resourceId, reason }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(data.message);
          closeModal("resourceReportModal");
        } else {
          alert("Error: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Report error:", error);
        alert("Network error while submitting the report.");
      });
  }

  function showResourceDetails(resourceId) {
    fetch(`./backend/get_resource_details.php?resource_id=${resourceId}`)
      .then((response) => response.json())
      .then((data) => {
        if (!data.success) {
          throw new Error(data.message || "Could not load details.");
        }

        const resource = data.resource;
        const content = document.getElementById("resourceDetailsContent");
        content.innerHTML = `
          <div class="modal-field"><strong>File Name:</strong> ${escapeHtml(resource.file_name)}</div>
          <div class="modal-field"><strong>Uploader:</strong> ${escapeHtml(resource.uploader_display)}</div>
          <div class="modal-field"><strong>Uploaded At:</strong> ${escapeHtml(resource.uploaded_at)}</div>
          <div class="modal-field"><strong>Course:</strong> ${escapeHtml(resource.course_code)} - ${escapeHtml(resource.course_name)}</div>
          <div class="modal-field"><strong>Type:</strong> ${escapeHtml(resource.resource_type)}</div>
          <div class="modal-field"><strong>Size:</strong> ${escapeHtml(resource.file_size)}</div>
          <div class="modal-field"><strong>File Path:</strong> ${escapeHtml(resource.file_path)}</div>
          <div class="modal-field"><strong>Download:</strong> <a href="${escapeHtml(resource.file_path)}" target="_blank" rel="noopener noreferrer">Open file</a></div>
        `;
        document.getElementById("resourceDetailsModal").classList.add("active");
      })
      .catch((error) => {
        console.error("Details error:", error);
        alert(error.message || "Unable to load resource details.");
      });
  }

  function setActiveAdminSection(sectionId) {
    const sections = [
      "adminOverviewSection",
      "adminHistorySection",
      "adminReportsSection",
    ];
    const tabs = ["adminTabOverview", "adminTabHistory", "adminTabReports"];

    sections.forEach((id) => {
      const section = document.getElementById(id);
      if (!section) return;
      section.classList.toggle("active", id === sectionId);
    });

    tabs.forEach((id) => {
      const tab = document.getElementById(id);
      if (!tab) return;
      tab.classList.toggle(
        "active",
        id === `adminTab${sectionId.replace("Section", "")}`,
      );
    });

    if (sectionId === "adminHistorySection") {
      loadAdminResourceHistory();
    } else if (sectionId === "adminReportsSection") {
      loadAdminReportedResources();
    }
  }

  function loadAdminResourceHistory() {
    fetch("./backend/admin_get_resource_history.php")
      .then((response) => response.json())
      .then((data) => {
        const tbody = document.getElementById("adminHistoryTableBody");
        if (!tbody) return;
        if (!data.success || !data.resources.length) {
          tbody.innerHTML =
            '<tr><td colspan="7" style="text-align:center; padding:30px;">No resource history found.</td></tr>';
          return;
        }

        tbody.innerHTML = data.resources
          .map(
            (resource) => `
          <tr>
            <td>${escapeHtml(resource.file_name)}</td>
            <td>${escapeHtml(resource.uploader_display)}</td>
            <td>${escapeHtml(resource.course_code)} ${resource.course_name ? "- " + escapeHtml(resource.course_name) : ""}</td>
            <td>${escapeHtml(resource.resource_type)}</td>
            <td>${escapeHtml(resource.uploaded_at)}</td>
            <td>${resource.report_count}</td>
            <td><button class="admin-action-btn admin-btn-promote" onclick="showResourceDetails(${resource.resource_id})">Details</button></td>
          </tr>
        `,
          )
          .join("");
      })
      .catch((error) => {
        console.error("Admin history error:", error);
      });
  }

  function loadAdminReportedResources() {
    fetch("./backend/admin_get_reported_resources.php")
      .then((response) => response.json())
      .then((data) => {
        const tbody = document.getElementById("adminReportsTableBody");
        if (!tbody) return;
        if (!data.success || !data.reports.length) {
          tbody.innerHTML =
            '<tr><td colspan="7" style="text-align:center; padding:30px;">No reported resources pending review.</td></tr>';
          return;
        }

        tbody.innerHTML = data.reports
          .map(
            (report) => `
          <tr>
            <td>${escapeHtml(report.file_name)}</td>
            <td>${escapeHtml(report.uploader_display)}</td>
            <td>${escapeHtml(report.course_code)} ${report.course_name ? "- " + escapeHtml(report.course_name) : ""}</td>
            <td>${escapeHtml(report.reporter_display)}</td>
            <td>${escapeHtml(report.reason)}</td>
            <td>${escapeHtml(report.reported_at)}</td>
            <td>
              <button class="admin-action-btn admin-btn-promote" onclick="reviewReport(${report.report_id}, 'dismiss_report')">Dismiss</button>
              <button class="admin-action-btn admin-btn-demote" onclick="reviewReport(${report.report_id}, 'delete_resource')">Remove</button>
            </td>
          </tr>
        `,
          )
          .join("");
      })
      .catch((error) => {
        console.error("Admin reports error:", error);
      });
  }

  function reviewReport(reportId, action) {
    if (
      !confirm(
        action === "delete_resource"
          ? "Remove the reported resource and mark the report resolved?"
          : "Dismiss this report?",
      )
    ) {
      return;
    }

    fetch("./backend/admin_review_report.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action, report_id: reportId }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(data.message);
          loadAdminReportedResources();
          loadAdminResourceHistory();
        } else {
          alert("Error: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Review report error:", error);
        alert("Network error. Please try again.");
      });
  }

  window.openReportModal = openReportModal;
  window.closeModal = closeModal;
  window.submitResourceReport = submitResourceReport;
  window.showResourceDetails = showResourceDetails;
  window.setActiveAdminSection = setActiveAdminSection;
  window.reviewReport = reviewReport;

  // File upload handlers
  if (dropArea) {
    dropArea.addEventListener("click", function (e) {
      if (!e.target.closest(".file-selected")) fileInput.click();
    });
  }
  if (fileInput) {
    fileInput.addEventListener("change", function (e) {
      if (this.files && this.files.length > 0) {
        selectedFile = this.files[0];
        fileSelected.innerHTML = `📎 ${selectedFile.name} (${(selectedFile.size / 1024 / 1024).toFixed(1)} MB)`;
      }
    });
  }
  if (dropArea) {
    dropArea.addEventListener("dragover", function (e) {
      e.preventDefault();
      this.style.borderColor = "#2d6a4f";
      this.style.backgroundColor = "rgba(45, 106, 79, 0.1)";
    });
    dropArea.addEventListener("dragleave", function (e) {
      e.preventDefault();
      this.style.borderColor = "#2d6a4f";
      this.style.backgroundColor = "transparent";
    });
    dropArea.addEventListener("drop", function (e) {
      e.preventDefault();
      this.style.borderColor = "#2d6a4f";
      this.style.backgroundColor = "transparent";
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        selectedFile = files[0];
        fileSelected.innerHTML = `📎 ${selectedFile.name} (${(selectedFile.size / 1024 / 1024).toFixed(1)} MB)`;
      }
    });
  }
  if (uploadSubmitBtn) {
    uploadSubmitBtn.addEventListener("click", function () {
      if (!selectedFile) {
        showToast("Please select a file to upload");
        return;
      }
      const customFileName = document
        .getElementById("customFileName")
        .value.trim();
      const typeId = document.getElementById("type_id").value;
      const courseId = document.getElementById("course_id").value;
      const isAnonymous = document.getElementById("anonCheck").checked;
      if (!typeId || !courseId) {
        showToast("Please select both Course and Resource Type");
        return;
      }
      const formData = new FormData();
      formData.append("file", selectedFile);
      formData.append("custom_file_name", customFileName);
      formData.append("type_id", typeId);
      formData.append("course_id", courseId);
      formData.append("anonymous_upload", isAnonymous ? "1" : "0");
      this.disabled = true;
      const originalText = this.textContent;
      this.textContent = "UPLOADING...";
      fetch("./backend/resourceRegisterProcess.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showToast(data.message || "Resource uploaded successfully!");
            document.getElementById("customFileName").value = "";
            document.getElementById("type_id").value = "";
            document.getElementById("course_id").value = "";
            document.getElementById("anonCheck").checked = false;
            fileSelected.innerHTML = "No file selected";
            selectedFile = null;
            if (fileInput) fileInput.value = "";
          } else {
            showToast("Upload failed: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Upload error:", error);
          showToast("Error uploading: " + error.message);
        })
        .finally(() => {
          this.disabled = false;
          this.textContent = originalText;
        });
    });
  }

  showView("home");
});

// ===== CHAT SESSION STATE =====
let chatHistory = [];
let currentSessionId = null;

// ===== SESSION MANAGEMENT FUNCTIONS =====
async function loadSessions() {
  try {
    const res = await fetch("./backend/chat_get_sessions.php");
    const data = await res.json();
    const list = document.getElementById("chatSessionList");
    if (!list) return;

    if (!data.sessions || data.sessions.length === 0) {
      list.innerHTML = '<div style="padding:12px; font-size:12px; color:rgba(255,255,255,0.4); text-align:center;">No chats yet</div>';
      return;
    }

    list.innerHTML = data.sessions.map(s => `
      <div class="chat-item ${s.session_id == currentSessionId ? 'active' : ''}"
           data-id="${s.session_id}"
           style="display:flex; align-items:center; justify-content:space-between; gap:6px; cursor:pointer;"
           onclick="loadSession(${s.session_id})">
        <span style="flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:13px;">
          ${escapeHtml(s.title)}
        </span>
        <button onclick="event.stopPropagation(); deleteSession(${s.session_id})" title="Delete"
                style="background:none; border:none; color:rgba(255,255,255,0.4); cursor:pointer; font-size:12px; flex-shrink:0; padding:2px 6px;">
          <i class="fas fa-times"></i>
        </button>
      </div>
    `).join("");
  } catch(e) {
    console.error("loadSessions error:", e);
  }
}

async function createNewSession() {
  const res = await fetch("./backend/chat_new_session.php");
  const data = await res.json();
  if (data.success) {
    currentSessionId = data.session_id;
    chatHistory = [];
    const chatMessages = document.getElementById("chatMessages");
    if (chatMessages) {
      chatMessages.innerHTML = `
        <div class="message ai">
          <div class="avatar"><i class="fas fa-robot"></i></div>
          <div class="message-content">Hello! I'm your AI study assistant. Ask me anything about your courses, resources, or topics you're studying.</div>
        </div>`;
    }
    await loadSessions();
  }
}

window.loadSession = async function(sessionId) {
  try {
    const res = await fetch(`./backend/chat_get_messages.php?session_id=${sessionId}`);
    const data = await res.json();
    if (!data.success) { console.error("loadSession failed:", data.message); return; }

    currentSessionId = sessionId;
    chatHistory = [];

    const chatMessages = document.getElementById("chatMessages");
    chatMessages.innerHTML = `
      <div class="message ai">
        <div class="avatar"><i class="fas fa-robot"></i></div>
        <div class="message-content">Hello! I'm your AI study assistant. Ask me anything about your courses, resources, or topics you're studying.</div>
      </div>`;

    data.messages.forEach(msg => {
      let content = msg.content;
      try { content = JSON.parse(content); } catch(e) {}

      if (msg.role === 'user') {
        const msgEl = document.createElement('div');
        msgEl.className = 'message user';

        if (Array.isArray(content)) {
          const textPart = content.find(c => c.type === 'text')?.text || '';
          const imgPart  = content.find(c => c.type === 'image_url');
          msgEl.innerHTML = `
            <div class="message-content">
              ${textPart ? escapeHtml(textPart) + '<br>' : ''}
              ${imgPart ? `<img src="${imgPart.image_url.url}" style="max-width:220px;max-height:180px;border-radius:10px;margin-top:6px;border:1px solid #d4e8d5;object-fit:cover;display:block;">` : ''}
            </div>
            <div class="avatar"><i class="fas fa-user"></i></div>`;
        } else {
          msgEl.innerHTML = `
            <div class="message-content">${escapeHtml(content)}</div>
            <div class="avatar"><i class="fas fa-user"></i></div>`;
        }

        chatMessages.appendChild(msgEl);
        chatHistory.push({ role: 'user', content: content });

      } else if (msg.role === 'assistant') {
        const msgEl = document.createElement('div');
        msgEl.className = 'message ai';
        let finalText = String(content).replace(/&#96;/g, '`').replace(/&nbsp;/g, ' ');
        finalText = finalText.replace(/(```[a-zA-Z0-9_-]*)\s*/g, '$1\n');
        msgEl.innerHTML = `
          <div class="avatar"><i class="fas fa-robot"></i></div>
          <div class="message-content">${marked.parse(finalText)}</div>`;
        chatMessages.appendChild(msgEl);
        chatHistory.push({ role: 'assistant', content: content });
      }
    });

    addCopyButtonsToCodeBlocks();
    chatMessages.scrollTop = chatMessages.scrollHeight;
    await loadSessions(); // refresh active highlight

  } catch(e) {
    console.error("loadSession error:", e);
  }
};

window.deleteSession = async function(sessionId) {
  if (!confirm('Delete this chat?')) return;
  await fetch('./backend/chat_delete_session.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ session_id: sessionId })
  });
  if (currentSessionId == sessionId) await createNewSession();
  else await loadSessions();
};

// ===== AI CHATBOT LOGIC =====
document.addEventListener("DOMContentLoaded", function () {
  const sendBtn = document.getElementById("sendBtn");
  const chatInput = document.getElementById("chatInput");
  const chatMessages = document.getElementById("chatMessages");
  const deepThinkBtn = document.getElementById("deepThinkBtn");
  const webSearchBtn = document.getElementById("webSearchBtn");

  let deepThinkMode = false;
  let webSearchMode = false;

  if (deepThinkBtn) {
    deepThinkBtn.addEventListener("click", function () {
      deepThinkMode = !deepThinkMode;

      if (deepThinkMode) {
        this.classList.add("active");
        this.innerHTML =
          '<i class="fas fa-brain"></i><span class="btn-text">DeepThink ON</span>';
        showToast("🧠 DeepThink mode activated!");
      } else {
        this.classList.remove("active");
        this.innerHTML =
          '<i class="fas fa-brain"></i><span class="btn-text">DeepThink</span>';
        showToast("DeepThink mode deactivated.");
      }
    });
  }

  if (webSearchBtn) {
    webSearchBtn.addEventListener("click", function () {
      webSearchMode = !webSearchMode;
      this.classList.toggle("active");
      this.querySelector(".btn-text").textContent = webSearchMode
        ? "Web Search ON"
        : "Web Search";
      showToast(
        webSearchMode
          ? "🌐 Web search enabled for next message"
          : "Web search disabled",
      );
    });
  }

  // ============ SHOW ATTACH BTN ONLY FOR VISION MODEL ============ <-- ADD HERE
  const modelSelect = document.getElementById("modelSelect");
  const attachBtn = document.querySelector(".attach-btn");

  function toggleAttachBtn() {
    const isVision = modelSelect?.value === "meta-llama/llama-4-scout-17b-16e-instruct";
    if (attachBtn) attachBtn.style.display = isVision ? "flex" : "none";
    if (!isVision) clearImagePreview();
  }

  toggleAttachBtn(); // run on load
  if (modelSelect) modelSelect.addEventListener("change", toggleAttachBtn);
 // ================================================================

  // ============ NEW CHAT / DELETE ALL BUTTONS ============
  const newChatBtn = document.getElementById("newChatBtn");
  const deleteAllChatsBtn = document.getElementById("deleteAllChatsBtn");

  if (newChatBtn) newChatBtn.addEventListener("click", createNewSession);

  if (deleteAllChatsBtn) {
    deleteAllChatsBtn.addEventListener("click", async function () {
      if (!confirm('Delete ALL chats? This cannot be undone.')) return;
      await fetch('./backend/chat_delete_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ delete_all: true })
      });
      await createNewSession();
    });
  }

  if (!sendBtn) return;

// ============ SAVE MESSAGE HELPER ============
  async function saveMessage(role, content) {
    if (!currentSessionId) return;
    await fetch('./backend/chat_save_message.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ session_id: currentSessionId, role, content })
    });
    await loadSessions(); // refresh title in sidebar
  }
  // ============ APPEND MESSAGE ============
  function appendMessage(role, text) {
    const msg = document.createElement("div");
    msg.className = `message ${role}`;
    msg.innerHTML = role === "ai"
      ? `<div class="avatar"><i class="fas fa-robot"></i></div><div class="message-content">${text}</div>`
      : `<div class="message-content">${text}</div><div class="avatar"><i class="fas fa-user"></i></div>`;
    chatMessages.appendChild(msg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function appendTyping() {
    const msg = document.createElement("div");
    msg.className = "message ai";
    msg.id = "typingIndicator";
    msg.innerHTML = `<div class="avatar"><i class="fas fa-robot"></i></div>
      <div class="message-content" style="color:#7da589;">Thinking...</span></div>`;
    chatMessages.appendChild(msg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

   // ============ SEND MESSAGE ============
  async function sendMessage() {
    const text = chatInput.value.trim();
    if (!text && document.getElementById("chatFileInput").files.length === 0) return;

    // Auto-create session if none
    if (!currentSessionId) await createNewSession();

    chatInput.value = "";
    sendBtn.disabled = true;

    const selectedModel = modelSelect?.value || "qwen3.5:4b";
    const isVision = selectedModel === "meta-llama/llama-4-scout-17b-16e-instruct";
    const chatFile = document.getElementById("chatFileInput");

    // Read file ONCE
    let imageData = null;
    let fileName = null;
    if (chatFile && chatFile.files.length > 0) {
      const file = chatFile.files[0];
      fileName = file.name;
      imageData = await new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = (e) => resolve(e.target.result);
        reader.readAsDataURL(file);
      });
      chatFile.value = "";
      const thumb = document.getElementById("imagePreviewThumb");
      if (thumb) { thumb.src = ""; thumb.style.display = "none"; }
    }

    // Build userMessage
    let userMessage;
    if (imageData && isVision) {
      userMessage = { role: "user", content: [
        { type: "text", text: text || "What is in this image?" },
        { type: "image_url", image_url: { url: imageData } }
      ]};
    } else if (imageData) {
      userMessage = { role: "user", content: text + "\n\n[Attached Image: " + fileName + "]" };
    } else {
      userMessage = { role: "user", content: text };
    }

    // Show in chat
    if (imageData) {
      appendMessage("user",
        (text ? escapeHtml(text) + "<br>" : "") +
        `<img src="${imageData}" style="max-width:220px;max-height:180px;border-radius:10px;margin-top:6px;border:1px solid #d4e8d5;object-fit:cover;display:block;">`
      );
    } else {
      appendMessage("user", escapeHtml(text));
    }

    chatHistory.push(userMessage);
    await saveMessage('user', userMessage.content);

    // AI placeholder
    const msg = document.createElement("div");
    msg.className = "message ai";
    msg.innerHTML = deepThinkMode
      ? `<div class="avatar"><i class="fas fa-robot"></i></div>
         <div class="message-content"><span class="thinking-indicator">
           <i class="fas fa-brain" style="color:#667eea;margin-right:6px;"></i>
           <span class="thinking-dots">Thinking<span>.</span><span>.</span><span>.</span></span>
         </span></div>`
      : `<div class="avatar"><i class="fas fa-robot"></i></div>
         <div class="message-content"><span class="typing-indicator"><span></span><span></span><span></span></span></div>`;
    chatMessages.appendChild(msg);
    const contentDiv = msg.querySelector(".message-content");
    chatMessages.scrollTop = chatMessages.scrollHeight;

    const startTime = performance.now();

    try {
      let requestBody = {
        model: selectedModel,
        stream: false,
        options: { temperature: deepThinkMode ? 0.5 : 0.7, num_predict: deepThinkMode ? 800 : 300, think: false },
        web_search: webSearchMode,
      };

      if (deepThinkMode) {
        requestBody.messages = [
          { role: "system", content: "You are a thoughtful, deep-thinking study assistant..." },
          ...chatHistory,
        ];
      } else {
        const MAX_MESSAGES = 20;
        while (chatHistory.length > MAX_MESSAGES) chatHistory.shift();
        requestBody.messages = [...chatHistory];
      }

      const response = await fetch("./backend/chatProxy.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(requestBody),
      });

      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();
      const fullReply = data.message?.content?.trim() || "";
      if (!fullReply) throw new Error("Empty response from AI");

      const elapsedSeconds = ((performance.now() - startTime) / 1000);
      const words = fullReply.split(" ");
      let index = 0;
      contentDiv.innerHTML = "";

      function typeWord() {
        if (index < words.length) {
          contentDiv.textContent += (index === 0 ? "" : " ") + words[index];
          chatMessages.scrollTop = chatMessages.scrollHeight;
          index++;
          const lastWord = words[index - 1];
          let delay = lastWord.length > 5 ? 40 : 20;
          if ([".", "!", "?"].includes(lastWord.slice(-1))) delay += 80;
          setTimeout(typeWord, delay);
        } else {
          let finalText = fullReply.replace(/&#96;/g, "`").replace(/&nbsp;/g, " ");
          finalText = finalText.replace(/(```[a-zA-Z0-9_-]*)\s*/g, "$1\n");
          contentDiv.innerHTML = marked.parse(finalText);
          const timeSpan = document.createElement("span");
          timeSpan.className = "response-time";
          timeSpan.textContent = `⏱ ${elapsedSeconds.toFixed(2)}s`;
          contentDiv.appendChild(timeSpan);
          chatHistory.push({ role: "assistant", content: fullReply });
          saveMessage('assistant', fullReply); // save to DB
          addCopyButtonsToCodeBlocks();
        }
      }
      typeWord();

    } catch (err) {
      console.error("Error:", err);
      contentDiv.innerHTML = "⚠️ Error: " + err.message;
      const timeSpan = document.createElement("span");
      timeSpan.className = "response-time";
      timeSpan.textContent = `⏱ ${((performance.now() - startTime) / 1000).toFixed(2)}s`;
      contentDiv.appendChild(timeSpan);
    } finally {
      sendBtn.disabled = false;
      chatInput.focus();
    }
  }
  // ============ FILE INPUT LISTENER ============
  const chatFileInput = document.getElementById("chatFileInput");
  if (chatFileInput) {
    chatFileInput.addEventListener("change", function () {
      const thumb = document.getElementById("imagePreviewThumb");
      if (!thumb) return;
      if (this.files && this.files.length > 0) {
        const file = this.files[0];
        if (!file.type.startsWith("image/")) {
          showToast("Only image files are allowed.");
          this.value = ""; return;
        }
        const reader = new FileReader();
        reader.onload = (e) => { thumb.src = e.target.result; thumb.style.display = "inline-block"; };
        reader.readAsDataURL(file);
      } else {
        thumb.src = ""; thumb.style.display = "none";
      }
    });
  }
sendBtn.addEventListener("click", sendMessage);
  chatInput.addEventListener("keydown", function (e) {
    if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); sendMessage(); }
  });

  // Init — load sessions and create first chat if none
  loadSessions().then(async () => {
    const list = document.getElementById("chatSessionList");
    const hasChats = list && list.querySelector(".chat-item");
    if (!hasChats) await createNewSession();
    else {
      // Load most recent session
      const firstItem = list.querySelector(".chat-item");
      if (firstItem) {
        const id = firstItem.querySelector(".session-title")
          ?.getAttribute("onclick")?.match(/\d+/)?.[0];
        if (id) await loadSession(parseInt(id));
      }
    }
  });
});

function clearImagePreview() {
  const thumb = document.getElementById("imagePreviewThumb");
  const input = document.getElementById("chatFileInput");
  if (thumb) { thumb.src = ""; thumb.style.display = "none"; }
  if (input) input.value = "";
}