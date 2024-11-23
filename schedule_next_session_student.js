// Wait for DOM to load
document.addEventListener("DOMContentLoaded", () => {
    console.log("JavaScript file loaded!");

    const courseSections = document.querySelectorAll(".course-section");

    courseSections.forEach(section => {
        const teacherId = section.querySelector(".date-select").dataset.teacherId;
        const dateSelect = section.querySelector(".date-select");
        const timeSelect = section.querySelector(".time-select");
        const sendRequestButton = section.querySelector(".send-request-button");

        // Fetch available dates for the selected teacher
        fetch(`schedule_next_session_student.php?teacher_id=${teacherId}`)
            .then(response => response.json())
            .then(dates => {
                console.log(`Available dates for teacher ${teacherId}:`, dates);
                dates.forEach(date => {
                    const option = document.createElement("option");
                    option.value = date.date;
                    option.textContent = date.date;
                    dateSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error fetching available dates:", error));

        // Fetch available time slots when a date is selected
        dateSelect.addEventListener("change", () => {
            const selectedDate = dateSelect.value;

            // Reset time slots dropdown
            timeSelect.innerHTML = "<option value='' disabled selected>Select Time</option>";
            timeSelect.disabled = true;

            // Fetch time slots for the selected date
            fetch(`schedule_next_session_student.php?teacher_id=${teacherId}&date=${selectedDate}`)
                .then(response => response.json())
                .then(times => {
                    console.log(`Time slots for ${selectedDate}:`, times);
                    times.forEach(slot => {
                        const option = document.createElement("option");
                        option.value = JSON.stringify(slot); // Store slot data as JSON string
                        option.textContent = `${slot.start_time} - ${slot.end_time}`;
                        timeSelect.appendChild(option);
                    });
                    timeSelect.disabled = false;
                })
                .catch(error => console.error("Error fetching time slots:", error));
        });

        // Handle "Send Request" button click
        sendRequestButton.addEventListener("click", () => {
            if (!dateSelect.value) {
                alert("Please select a date before submitting the request.");
                return;
            }

            if (!timeSelect.value) {
                alert("Please select a time slot before submitting the request.");
                return;
            }

            // Parse the selected time slot data
            const { start_time, end_time, availability_id } = JSON.parse(timeSelect.value);

            const courseId = sendRequestButton.dataset.courseId;

            // Log the data being submitted for debugging
            console.log("Submitting request with the following data:", {
                course_id: courseId,
                session_date: dateSelect.value,
                start_time,
                end_time,
                availability_id,
            });

            // Send the data to the server
            fetch("schedule_next_session_student.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    course_id: courseId,
                    session_date: dateSelect.value,
                    start_time,
                    end_time,
                    availability_id,
                }),
            })
                .then(response => response.json())
                .then(result => {
                    console.log("Server response:", result);

                    if (result.status === "success") {
                        alert(result.message);
                    } else {
                        alert(result.message || "An error occurred. Please try again.");
                    }
                })
                .catch(error => {
                    console.error("Error submitting request:", error);
                    alert("An unexpected error occurred. Please try again.");
                });
        });
    });
});

// Utility: Reset all time and date selects when switching courses
function resetAllSelectors() {
    const dateSelects = document.querySelectorAll(".date-select");
    const timeSelects = document.querySelectorAll(".time-select");

    dateSelects.forEach(select => {
        select.innerHTML = "<option value='' disabled selected>Select Date</option>";
    });

    timeSelects.forEach(select => {
        select.innerHTML = "<option value='' disabled selected>Select Time</option>";
        select.disabled = true;
    });
}

// Functionality: Highlight selected button in the top navigation
function highlightActiveButton() {
    const buttons = document.querySelectorAll(".button-s");

    buttons.forEach(button => {
        button.addEventListener("click", () => {
            buttons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");
        });
    });
}

highlightActiveButton();
