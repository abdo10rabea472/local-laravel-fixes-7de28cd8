const form = document.getElementById("resetForm");
const email = document.getElementById("email");
const error = document.getElementById("error");
const btn = document.getElementById("btn");
form.addEventListener("submit", function(e) {
  e.preventDefault();
  error.textContent = "";
  const emailValue = email.value.trim();
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (emailValue === "") {
    error.textContent = "Email is required";
    return;
  }
  if (!emailRegex.test(emailValue)) {
    error.textContent = "Enter a valid email address";
    return;
  }
  btn.textContent = "Sending...";
  btn.disabled = true;
  setTimeout(() => {
    window.location.href = `otp.html?email=${emailValue}`;
  }, 1000);
});