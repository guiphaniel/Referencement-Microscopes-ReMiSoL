let microscopeFields = document.getElementById("microscopes")
let nextMicroFieldId = 1;

let addMicroButton = document.getElementById("add-micro");
addMicroButton.onclick = addMicroscopeField;

function addMicroscopeField() {
    id = nextMicroFieldId++;

    // create the form fieldset
    microscope = document.createElement("fieldset");
    microscope.id = "micro-field-" + id;
    prevId = id - 1;
    microscope.innerHTML = document.getElementById("micro-field-" + prevId).innerHTML.replaceAll(`[${prevId}]`, `[${id}]`).replaceAll(`-${prevId}`, `-${id}`);
    
    // add the form fieldset at the end of the form
    microscopeFields.insertBefore(microscope, addMicroButton);

    // append the remove button to the fieldset
    let rmButton = document.createElement("div")
    rmButton.className = "rm-micro";
    rmButton.id = "rm-micro-" + id;
    rmButton.addEventListener('click', function(){
        let microId = this.id.split('-')[2]; // retrieve the id of the rmButton, which is the one of the fieldset too
        document.getElementById("micro-field-" + microId).remove();
        this.remove()
    });
    microscopeFields.insertBefore(rmButton, addMicroButton);
}