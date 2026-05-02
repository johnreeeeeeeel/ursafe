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
    email,
    status
) {
    document.getElementById('vu_id').innerText = id;
    document.getElementById('vu_username').innerText = username;
    document.getElementById('vu_fullname').innerText = fullname;
    document.getElementById('vu_sex').innerText = sex;
    document.getElementById('vu_dob').innerText = dob;
    document.getElementById('vu_institute').innerText = institute;
    document.getElementById('vu_program').innerText = program;
    document.getElementById('vu_email').innerText = email;
    document.getElementById('vu_status').innerText = status;
}

// Update user details
function updateUserDetails(
    id,
    firstname,
    middlename,
    lastname,
    sex,
    dob,
    institute,
    program,
    email
) {
    document.getElementById('uu_id').value = id;
    document.getElementById('uu_firstname').value = firstname;
    document.getElementById('uu_middlename').value = middlename;
    document.getElementById('uu_lastname').value = lastname;
    document.getElementById('uu_sex').value = sex;
    document.getElementById('uu_dob').value = dob;
    document.getElementById('uu_institute').value = institute;
    document.getElementById('uu_program').value = program;
    document.getElementById('uu_email').value = email;
}

// Delete user 
function setDeleteUser(
    id
) {
    document.getElementById('du_id').value = id;
}