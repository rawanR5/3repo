document.addEventListener("DOMContentLoaded", () => {
    const cancelButtons = document.querySelectorAll(".cancel-button");

    cancelButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const sessionId = button.dataset.sessionId;

            if (confirm("Are you sure you want to cancel this session?")) {
                fetch("incomingSession-student.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ session_id: sessionId }),
                })
                    .then((response) => response.json())
                    .then((result) => {
                        if (result.status === "success") {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert("An error occurred. Please try again.");
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("An unexpected error occurred.");
                    });
            }
        });
    });
});


