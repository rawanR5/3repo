document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#create-course-form");

    form.addEventListener("submit", (e) => {
        e.preventDefault(); // Prevent default form submission

        const formData = new FormData(form);

        fetch("create_new_course.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "success") {
                    alert(data.message);
                    window.location.href = "view_course_as_teacher.php"; // Redirect on success
                } else {
                    alert(data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred. Please try again.");
            });
    });

    // Image preview functionality
    const imageInput = document.querySelector("#course-image");
    const previewImg = document.querySelector("#preview-img");

    imageInput.addEventListener("change", () => {
        const file = imageInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewImg.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    });
});
