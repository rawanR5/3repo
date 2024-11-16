document.addEventListener("DOMContentLoaded", () => {

    const continueButton = document.querySelector("button");
    const form = document.querySelector('.signup-form-T');
    const firstNameInput = document.querySelector('#first-name');
    const lastNameInput = document.querySelector('#last-name');
    const emailInput = document.querySelector('#email');
    const passwordInput = document.querySelector('#password');


    continueButton.addEventListener("click", (event) => {
        event.preventDefault(); 

        if (!firstNameInput.value || !lastNameInput.value || !emailInput.value || !passwordInput.value) {
            alert('All fields except photo are required!');
            return;
        }

  
        alert('Form submitted successfully!');
        window.location.href = "homePageStudent.html"; 
    });


    form.addEventListener('submit', (event) => {
     
        event.preventDefault();


        if (!firstNameInput.value || !lastNameInput.value || !emailInput.value || !passwordInput.value) {
            alert('All fields except photo are required!');
            return;
        }


        alert('Form submitted successfully!');


        form.reset();
    });
});
