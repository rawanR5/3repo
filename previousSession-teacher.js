document.addEventListener("DOMContentLoaded", () => {
    // Handle upload or update material
    const uploadMaterialButtons = document.querySelectorAll(".upload-material-button");

    uploadMaterialButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const sessionId = button.dataset.sessionId;
            const materialInput = button.previousElementSibling.value.trim();

            if (materialInput === "") {
                alert("Please enter a valid material link.");
                return;
            }

            fetch("previousSession-teacher.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    action: "update_material",
                    session_id: sessionId,
                    material: materialInput,
                }),
            })
                .then((response) => response.json())
                .then((result) => {
                    if (result.status === "success") {
                        alert(result.message); // Display success alert
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert(result.message); // Display error message
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An unexpected error occurred. Please try again.");
                });
        });
    });
});
