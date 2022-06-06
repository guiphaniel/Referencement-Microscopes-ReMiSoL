document.addEventListener("click", onRemoveGroupBt)

function onRemoveGroupBt(e) {
    if(e.target.classList.contains("rm-bt")) {
        let rmBt = e.target;
        form = rmBt.parentElement;
        timeoutID = window.setTimeout(removeGroup, 5000, form);

        rmBt.style.display = "none";

        wrapper = document.createElement("div");
        wrapper.className = "undo-wrapper";
        
        wrapper.append(document.createTextNode("Suppression en cours..."));
        
        undo = document.createElement("span");
        undo.append(document.createTextNode("Annuler"));
        undo.className = "bt undo-bt";
        undo.dataset.timeoutId = timeoutID
        undo.addEventListener('click', function() { 
            window.clearTimeout(this.dataset.timeoutId); 
            this.parentElement.previousElementSibling.style.display = "flex"; // show the remove-bt again
            this.parentElement.remove(); // remove wrapper
        });
        wrapper.append(undo);
        
        form.append(wrapper);
    }
}

function removeGroup(form) {
    form.submit();
}