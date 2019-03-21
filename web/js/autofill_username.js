/*
 * This script stores the username in localStorage, and autofills it in the
 * next time the user sees a login form.
 */

function storeUsername(username) {
    var field = document.querySelector("input[name='username']");

}

// We try to get the submit button and the username field first,
// and check if they exist.
// Also only run the code if localStorage is avaiable
var submitButton = document.querySelector("input[name='submit']");
var field = document.querySelector("input[name='username']");
if(submitButton && field && window.localStorage) {
    // Early out if there is no username field
    if(!field) return;

    // Auto fill the username if there is one in localStorage and the field is empty
    var username = window.localStorage.getItem("username");
    if(username && field.value === "") {
        field.value = username;
    }

    // Attach a listener to the submit button so we can store the username
    submitButton.addEventListener("click", function() {
        var username = field.value;
        window.localStorage.setItem("username", username);
    });
}

