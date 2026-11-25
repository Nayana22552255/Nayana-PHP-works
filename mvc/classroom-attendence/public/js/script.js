
//login
document.addEventListener("DOMContentLoaded", function () {

    const password = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    if (password && togglePassword) {

        togglePassword.addEventListener("click", function () {

            if (password.type === "password") {
                password.type = "text";
                togglePassword.textContent = "Hide";
            } else {
                password.type = "password";
                togglePassword.textContent = "Show";
            }

        });

    }

});


