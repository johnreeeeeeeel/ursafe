// Loading screen
function showLoading() {
    document.getElementById("loadingScreen").classList.remove("d-none");
}

function hideLoading() {
    document.getElementById("loadingScreen").classList.add("d-none");
}

document.querySelectorAll("form").forEach(form => {
    form.addEventListener("submit", function () {
        showLoading();
    });
});

window.addEventListener("load", hideLoading);

window.addEventListener("pageshow", function (event) {
    hideLoading();
});

// Toogle Show Password
document.querySelectorAll(".toggle-password").forEach(icon => {
    icon.addEventListener("click", function () {
        const input = this.parentElement.querySelector("input");

        if (input.type === "password") {
            input.type = "text";
            this.classList.remove("fa-eye-slash");
            this.classList.add("fa-eye");
        } else {
            input.type = "password";
            this.classList.remove("fa-eye");
            this.classList.add("fa-eye-slash");
        }
    });
});

// Alert message timout
setTimeout(() => {
    const alertBox = document.getElementById("messageAlert");

    if (alertBox) {
        alertBox.remove();
    }
}, 4500);

// Remove focus when modal closes
document.addEventListener('hide.bs.modal', function (e) {
    const modal = e.target;

    if (modal.contains(document.activeElement)) {
        document.activeElement.blur();
    }
});

// Dynamic header title
function setTitle(el) {
    document.querySelector(".page-title").textContent = el.dataset.title;
}

// View user details
function viewUserDetails(
    id,
    fullname,
    sex,
    dob,
    institute,
    program,
    username,
    email
) {
    document.getElementById('vu_id').innerText = id;
    document.getElementById('vu_username').innerText = username;
    document.getElementById('vu_fullname').innerText = fullname;
    document.getElementById('vu_sex').innerText = sex;
    document.getElementById('vu_dob').innerText = dob;
    document.getElementById('vu_institute').innerText = institute;
    document.getElementById('vu_program').innerText = program;
    document.getElementById('vu_email').innerText = email;
}