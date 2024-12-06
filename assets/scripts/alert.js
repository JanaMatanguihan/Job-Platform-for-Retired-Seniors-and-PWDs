document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    
    function showError(fieldname, message) {
        const errorElement = document.getElementById(`${fieldname}-error`);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }
    }

    form.addEventListener("submit", async function (event) {
        event.preventDefault();
        
        const formData = new FormData(form);
        const response = await fetch("../Backend/Main.php", {
            method: "POST",
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            swal({
                title: "Success!",
                text: data.message,
                icon: "success",
                button: "Continue",
            }).then(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            });
        } else if (data.errors) {
            data.errors.forEach(error => {
                showError(error.field.toLowerCase(), error.message);
            });
        }
    });
}); 