let microscopeFields = document.getElementById("microscopes")
let nextMicroFieldId = 0;

let addMicroButton = document.getElementById("add-micro");
addMicroButton.onclick = addMicroscopeField;

function addMicroscopeField() {
    id = nextMicroFieldId++;
    microscope = document.createElement("fieldset");
    microscope.id = "micro-field-" + id;
    microscope.innerHTML = `
        <legend>Votre microscope</legend>
        <label for="micro-brand-${id}">Marque</label>
        <input id="micro-brand-${id}" type="text" name="microscopes[${id}][microBrand]" required>
        <label for="micro-ref-${id}">Référence</label>
        <input id="micro-ref-${id}" type="text" name="microscopes[${id}][microRef]" required>
        <label for="micro-rate-${id}">Tarification</label>
        <input id="micro-rate-${id}" type="text" name="microscopes[${id}][microRate]}" required>
        <label for="micro-desc-${id}">Description</label>
        <textarea id="micro-desc-${id}" name="microscopes[${id}][microDesc]" cols="30" rows="10" required></textarea>
    `;

    let rmButton = document.createElement("div")
    rmButton.className = "rm-micro"
    //TODO: corriger cette partie qui ne fonctionne pas (remove)
    rmButton.addEventListener('click', function(){
        document.getElementById("micro-field-" + id).remove();
    });
    microscope.append(rmButton);
    
    microscopeFields.insertBefore(microscope, addMicroButton);
}