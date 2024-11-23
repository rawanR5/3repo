// Initialize dates dynamically based on the current date
function initializeDates() {
    const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const today = new Date();

    // Find the closest Sunday (start of the week)
    const daysUntilSunday = today.getDay(); // 0 for Sunday
    const sundayDate = new Date(today);
    sundayDate.setDate(today.getDate() - daysUntilSunday); // Move back to the most recent Sunday

    daysOfWeek.forEach((day, index) => {
        const currentDate = new Date(sundayDate);
        currentDate.setDate(sundayDate.getDate() + index); // Add days to get subsequent days
        const formattedDate = currentDate.toISOString().split("T")[0]; // Format as YYYY-MM-DD

        const dateInput = document.getElementById(`date-${day}`);
        if (dateInput) {
            dateInput.value = formattedDate; // Set the value in the date picker
        }
    });
}

// Preselect existing time slots based on availability data
function preselectTimeSlots() {
    if (!existingAvailability || !Array.isArray(existingAvailability)) {
        console.warn("No existing availability data found.");
        return;
    }

    existingAvailability.forEach(slot => {
        const { available_date, start_time, end_time, is_available } = slot;

        // Find the corresponding time slot element
        const matchingSlot = document.querySelector(
            `.time-slot[data-start-time="${start_time}"][data-end-time="${end_time}"][data-day="${getDayName(available_date)}"]`
        );

        if (matchingSlot) {
            matchingSlot.classList.add("selected");

            // Apply a gray border for unavailable slots
            if (is_available === 0) {
                matchingSlot.style.borderColor = "gray";
                matchingSlot.style.backgroundColor = "#e0e0e0"; // Gray background
                matchingSlot.style.cursor = "not-allowed"; // Disable interaction
            } else {
                matchingSlot.style.borderColor = "pink"; // Pink for available slots
            }
        }
    });
}

// Helper function to convert date (YYYY-MM-DD) to day name
function getDayName(dateString) {
    const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const date = new Date(dateString);
    return daysOfWeek[date.getDay()];
}

// Add event listeners to time slots for toggling selection
function initializeTimeSlotListeners() {
    document.querySelectorAll(".time-slot").forEach(slot => {
        slot.addEventListener("click", function () {
            // Prevent toggling for unavailable (gray) slots
            if (slot.style.borderColor === "gray") {
                return;
            }

            slot.classList.toggle("selected"); // Toggle 'selected' class
            console.log(`Time slot toggled: ${slot.innerText}`); // Log the clicked time slot
        });
    });
}

// Submit data to the server using Fetch API
async function submitDataToServer(data) {
    console.log("Submitting data:", data); // Log the collected data

    try {
        // Make a POST request to send data to the server
        const response = await fetch("teacherSchedule.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ data: JSON.stringify(data) }), // Send data as JSON
        });

        // Parse the response
        const result = await response.json();
        console.log("Server response:", result);

        // Show success or error alert only after the server response
        if (result.status === "success") {
            alert(result.message); // Show success message
        } else {
            alert(result.message); // Show error message
        }
    } catch (error) {
        console.error("Error submitting data:", error);
        alert("An unexpected error occurred. Please try again.");
    }
}

// Handle Update button click
function handleUpdateButtonClick() {
    const updateButton = document.querySelector(".update-button");
    updateButton.addEventListener("click", function (e) {
        e.preventDefault(); // Prevent default form submission
        console.log("Update button clicked");

        const selectedSlots = document.querySelectorAll(".time-slot.selected");
        if (selectedSlots.length === 0) {
            alert("No time slots selected. Please select at least one slot.");
            return; // Stop execution if no time slots are selected
        }

        const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        const dataToSubmit = [];

        // Loop through all days and collect selected time slots
        days.forEach(day => {
            const dateInput = document.getElementById(`date-${day}`);
            const dateValue = dateInput?.value;

            // Collect all selected time slots for the current day
            document.querySelectorAll(`.time-slot.selected[data-day="${day}"]`).forEach(slot => {
                const startTime = slot.getAttribute("data-start-time");
                const endTime = slot.getAttribute("data-end-time");
                if (dateValue && startTime && endTime) {
                    dataToSubmit.push({
                        date: dateValue,
                        start_time: startTime,
                        end_time: endTime,
                    });
                }
            });
        });

        console.log("Data to submit:", dataToSubmit);

        // Submit data to the server using the helper function
        submitDataToServer(dataToSubmit);
    });
}

// Initialize everything
document.addEventListener("DOMContentLoaded", () => {
    console.log("JavaScript loaded!");
    initializeDates();
    preselectTimeSlots(); // Preselect time slots on page load
    initializeTimeSlotListeners();
    handleUpdateButtonClick();
});
