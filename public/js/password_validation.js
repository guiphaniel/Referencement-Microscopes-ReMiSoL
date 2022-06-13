document.addEventListener("focusout", onInputChange);

function onInputChange(e) {
    let input = e.target;

    if(input.id.includes("password2")) {
        if(input.parentElement.previousElementSibling.firstElementChild.value !== input.value)
            input.setCustomValidity("Les mots de passe ne correspondent pas.");
        else
            input.setCustomValidity("");
    } else {
        if(input.id.includes("password1")) {
            let password2 = input.parentElement.nextElementSibling.firstElementChild;
            if(password2.value !== input.value)
                password2.setCustomValidity("Les mots de passe ne correspondent pas.");
            else
                password2.setCustomValidity("");
        }
    }
}

