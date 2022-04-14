let microscopeFields = document.getElementById("microscopes")
let nextMicroFieldId = 1;

let addMicroButton = document.getElementById("add-micro");
addMicroButton.onclick = addMicroscopeField;

function addMicroscopeField() {
    id = nextMicroFieldId++;

    // create the form fieldset
    microscope = document.createElement("fieldset");
    microscope.id = "micro-field-" + id;
    microscope.innerHTML = `
        <legend>Votre microscope</legend>
        <label for="micro-brand-${id}">Marque</label>
        <input id="micro-brand-${id}" type="text" name="microscopes[${id}][brand]" required>
        <label for="micro-ref-${id}">Référence</label>
        <input id="micro-ref-${id}" type="text" name="microscopes[${id}][ref]" required>
        <label for="micro-rate-${id}">Tarification</label>
        <input id="micro-rate-${id}" type="number" name="microscopes[${id}][rate]" min="0" step="0.01" required>
        <label for="micro-desc-${id}">Description</label>
        <textarea id="micro-desc-${id}" name="microscopes[${id}][desc]" cols="30" rows="10" required></textarea>
    `;

    // add the remove button in the fieldset
    let rmButton = document.createElement("div")
    rmButton.className = "rm-micro";
    rmButton.id = "rm-micro-" + id;
    rmButton.addEventListener('click', function(){
        let microId = this.id.split('-')[2]; // retrieve the id of the rmButton, which is the one of the fieldset too
        document.getElementById("micro-field-" + microId).remove();
    });
    microscope.append(rmButton);
    
    // add the form fieldset at the end of the form
    microscopeFields.insertBefore(microscope, addMicroButton);
}