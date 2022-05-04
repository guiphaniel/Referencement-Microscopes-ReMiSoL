const userForms = document.getElementById("user-forms");

const rmBts = userForms.getElementsByClassName("rm-bt");

for (const rmBt of rmBts) {
    rmBt.addEventListener("click", () => {
        form = rmBt.parentElement;
        timeoutID = window.setTimeout(removeUser, 5000, form);

        rmBt.hidden = true;

        wrapper = document.createElement("div");
        
        wrapper.append(document.createTextNode("Suppression en cours..."));
        
        undo = document.createElement("span");
        undo.append(document.createTextNode("Annuler"));
        undo.className = "undo-bt";
        undo.dataset.timeoutId = timeoutID
        undo.addEventListener('click', function() { 
            window.clearTimeout(this.dataset.timeoutId); 
            this.parentElement.previousElementSibling.hidden = false // show the remove-bt again
            this.parentElement.remove(); // remove wrapper
        });
        wrapper.append(undo);
        
        form.append(wrapper);
    })
}

function removeUser(form) {
    form.children[1].value = "delete"; // set action to delete
    form.submit();
}