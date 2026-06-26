const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');
signUpButton.addEventListener('click', () => {
container.classList.add("right-panel-active");
});
signInButton.addEventListener('click', () => {
container.classList.remove("right-panel-active");
});
const signUpForm = document.querySelector(".sign-up-container form");
const signInForm = document.querySelector(".sign-in-container form");
const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
const namePattern = /^[A-Z][a-zA-Z]*$/;
function showError(input, message) {
    removeError(input);
    const small = document.createElement("small");
    small.style.color = "red";
    small.innerText = message;
    input.style.border = "2px solid red";
    input.parentElement.appendChild(small);
}
function removeError(input) {
    input.style.border = "1px solid #ccc";
    const small = input.parentElement.querySelector("small");
    if (small) small.remove();
}
signUpForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const name = signUpForm.querySelector('input[type="text"]');
    const email = signUpForm.querySelector('input[type="email"]');
    const password = signUpForm.querySelector('input[type="password"]');
    let valid = true;
    [name, email, password].forEach(input => {
        if (input.value.trim() === "") {
            showError(input, "This field is required");
            valid = false;
        } else {
            removeError(input);
        }
    });
    if (!namePattern.test(name.value)) {
        showError(name, "Name must start with a capital letter");
        valid = false;
    }
    if (!emailPattern.test(email.value)) {
        showError(email, "Enter a valid email");
        valid = false;
    }
    if (password.value.length < 6) {
        showError(password, "Password must be at least 6 characters");
        valid = false;
    }
    if (valid) {
        signUpForm.reset();
    }
});
signInForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const email = signInForm.querySelector('input[type="email"]');
    const password = signInForm.querySelector('input[type="password"]');
    let valid = true;
    [email, password].forEach(input => {
        if (input.value.trim() === "") {
            showError(input, "This field is required");
            valid = false;
        } else {
            removeError(input);
        }
    });
    if (!emailPattern.test(email.value)) {
        showError(email, "Enter a valid email");
        valid = false;
    }
    if (valid) {
        signInForm.reset();
    }
});


