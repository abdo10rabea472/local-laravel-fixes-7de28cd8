const inputs = document.querySelectorAll(".otp-inputs input");
const verifyBtn = document.getElementById("verifyBtn");
const otpError = document.getElementById("otpError");

const otpStep = document.getElementById("otpStep");
const resetStep = document.getElementById("resetStep");

const newPass = document.getElementById("newPass");
const confirmPass = document.getElementById("confirmPass");
const passError = document.getElementById("passError");
const resetBtn = document.getElementById("resetBtn");
const successMsg = document.getElementById("successMsg");

const resend = document.getElementById("resend");

// auto move between OTP inputs
inputs.forEach((input, index) => {
  input.addEventListener("input", () => {
    if (input.value.length === 1 && index < inputs.length - 1) {
      inputs[index + 1].focus();
    }
  });
});

// verify OTP
verifyBtn.addEventListener("click", () => {
  let code = "";
  inputs.forEach(input => code += input.value);

  if (code.length < 6) {
    otpError.textContent = "Enter full code";
    return;
  }

  otpError.textContent = "";

  // simulate success
  otpStep.classList.add("hidden");
  resetStep.classList.remove("hidden");
});

// resend code
resend.addEventListener("click", () => {
  alert("Code resent successfully");
});

// reset password
resetBtn.addEventListener("click", () => {
  const pass = newPass.value.trim();
  const confirm = confirmPass.value.trim();

  passError.textContent = "";
  successMsg.textContent = "";

  if (pass.length < 6) {
    passError.textContent = "Password must be at least 6 characters";
    return;
  }

  if (pass !== confirm) {
    passError.textContent = "Passwords do not match";
    return;
  }

  // simulate success
  successMsg.textContent = "Password updated successfully!";
  newPass.value = "";
  confirmPass.value = "";
});