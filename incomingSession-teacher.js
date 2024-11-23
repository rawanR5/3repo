document.addEventListener("DOMContentLoaded", () => {
    // Handle cancel session
    const cancelButtons = document.querySelectorAll(".cancel-button");
    cancelButtons.forEach(button => {
        button.addEventListener("click", () => {
            const sessionId = button.dataset.sessionId;
            if (confirm("Are you sure you want to cancel this session?")) {
                fetch("incomingSession-teacher.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ action: "cancel_session", session_id: sessionId }),
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === "success") {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert(result.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An unexpected error occurred.");
                    });
            }
        });
    });

    // Handle upload material
    const uploadMaterialButtons = document.querySelectorAll(".upload-material-button");
    uploadMaterialButtons.forEach(button => {
        button.addEventListener("click", () => {
            const sessionId = button.dataset.sessionId;
            const materialInput = button.previousElementSibling.value.trim();
            if (materialInput === "") {
                alert("Please enter a valid material link.");
                return;
            }
            fetch("incomingSession-teacher.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ action: "upload_material", session_id: sessionId, material: materialInput }),
            })
                .then(response => response.json())
                .then(result => {
                    if (result.status === "success") {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert("Failed to upload material. Please try again.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An unexpected error occurred.");
                });
        });
    });
});
