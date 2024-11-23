document.addEventListener("DOMContentLoaded", () => {
    const viewMaterialButtons = document.querySelectorAll(".view-materials-btn");

    viewMaterialButtons.forEach(button => {
        button.addEventListener("click", () => {
            // Retrieve the material link from the button's dataset
            const materialLink = button.dataset.material ? button.dataset.material.trim() : "";

            // Check if the material link exists and is valid
            if (materialLink && materialLink.startsWith("http")) {
                // Open the material link in a new tab
                window.open(materialLink, "_blank");
            } else {
                // Show an alert if the material is missing or invalid
                alert("No material has been uploaded for this session.");
            }
        });
    });
});

