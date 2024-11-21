document.addEventListener("DOMContentLoaded", () => {
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
