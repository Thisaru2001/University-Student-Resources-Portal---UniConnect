// Toast
function showToast(msg) {
  alert(msg); // simple version for presentation
}

// SIGNUP FUNCTION
async function handleSignUp(e) {
  e.preventDefault();

  const id = document.getElementById('uid').value.trim();
  const fname = document.getElementById('fname').value.trim();
  const email = document.getElementById('email').value.trim();
  const pwd = document.getElementById('pwd').value.trim();

  if (!id || !fname || !email || !pwd) {
    showToast("Please fill all fields");
    return;
  }

  const formData = new FormData();
  formData.append("id", id);
  formData.append("fname", fname);
  formData.append("email", email);
  formData.append("pwd", pwd);

  try {

    const response = await fetch("signupProcess.php", {
      method: "POST",
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showToast(data.message);
      setTimeout(() => {
        window.location.href = "index.php";
      }, 1000);
    } else {
      showToast(data.message);
    }

  } catch (err) {
    console.error(err);
    showToast("Network error");
  }
}