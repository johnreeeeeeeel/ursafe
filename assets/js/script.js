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
    created_at
) {
    document.getElementById('vu_id').innerText = id;
    document.getElementById('vu_username').innerText = username;
    document.getElementById('vu_fullname').innerText = fullname;
    document.getElementById('vu_sex').innerText = sex;
    document.getElementById('vu_dob').innerText = dob;
    document.getElementById('vu_institute').innerText = institute;
    document.getElementById('vu_program').innerText = program;
    document.getElementById('vu_email').innerText = email;
    document.getElementById('vu_created_at').innerText = created_at;
}