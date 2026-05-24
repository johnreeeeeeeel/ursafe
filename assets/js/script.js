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

// Alert message    
document.addEventListener("DOMContentLoaded", function () {
    const toastEl = document.getElementById('messageAlert');
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl, {
            delay: 4000,
        });
        toast.show();
    }
});

document.addEventListener('hide.bs.modal', function (e) {
    const modal = e.target;

    if (modal.contains(document.activeElement)) {
        document.activeElement.blur();
    }
});

// Stay in offcanvas when reloading or redirecting
document.addEventListener("DOMContentLoaded", function () {

    if (window.location.hash) {
        const el = document.querySelector(window.location.hash);

        if (el) {
            const bsOffcanvas = new bootstrap.Offcanvas(el);
            bsOffcanvas.show();

            // When offcanvas is closed
            el.addEventListener("hidden.bs.offcanvas", function () {
                // remove hash 
                history.replaceState(null, null, window.location.pathname);
            });
        }
    }
});

// View active user details
function viewActiveUserDetails(
    id,
    fullname,
    sex,
    dob,
    institute,
    program,
    status,
    username,
    email,
    updated_at,
    created_at
) {
    document.getElementById('a_id').innerText = id;
    document.getElementById('a_username').innerText = username;
    document.getElementById('a_fullname').innerText = fullname;
    document.getElementById('a_sex').innerText = sex;
    document.getElementById('a_dob').innerText = dob;
    document.getElementById('a_institute').innerText = institute;
    document.getElementById('a_program').innerText = program;
    document.getElementById('a_status').innerText = status;
    document.getElementById('a_email').innerText = email;
    document.getElementById('a_updated_at').innerText = updated_at;
    document.getElementById('a_created_at').innerText = created_at;
}

// View inactive user details
function viewInactiveUserDetails(
    id,
    fullname,
    sex,
    dob,
    institute,
    program,
    status,
    email,
    created_at
) {
    document.getElementById('i_id').innerText = id;
    document.getElementById('i_fullname').innerText = fullname;
    document.getElementById('i_sex').innerText = sex;
    document.getElementById('i_dob').innerText = dob;
    document.getElementById('i_institute').innerText = institute;
    document.getElementById('i_program').innerText = program;
    document.getElementById('i_status').innerText = status;
    document.getElementById('i_email').innerText = email;
    document.getElementById('i_created_at').innerText = created_at;
}