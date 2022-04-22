// TODO: block submit button if fields are badly filled (disabled, bad email)
// fill coherent microscope infomations dataLists on focusout

//init focusout listeners for inputs
{
    let compagnyInput = document.getElementById("micro-compagny-0");
    compagnyInput.addEventListener("focusout", compagnyFocusOut);
    let brandInput = document.getElementById("micro-brand-0");
    brandInput.addEventListener("focusout", brandFocusOut);
}

async function compagnyFocusOut() {
	const fieldsetId = this.id.split('-')[2];
    
    const url = `/api/v1/listBrands.php?compagny=${this.value}`;
    
    let brandDatalist = document.getElementById(`micro-brands-` + fieldsetId);

    // enable/disable the inputs wether the datalist is filled/empty (i.e. the compagny doesn't exist)
    let empty = !await fillDatalist(brandDatalist, url);
    if(empty) {
        document.getElementById(`micro-brand-` + fieldsetId).disabled = true
        document.getElementById(`micro-model-` + fieldsetId).disabled = true
        document.getElementById(`micro-controller-` + fieldsetId).disabled = true
    } else
        document.getElementById(`micro-brand-` + fieldsetId).disabled = false
}

async function brandFocusOut() {
    const fieldsetId = this.id.split('-')[2];

    let compagnyInput = document.getElementById("micro-compagny-" + fieldsetId);

	const modelsUrl = `/api/v1/listModels.php?compagny=${compagnyInput.value}&brand=${this.value}`;
    let modelDatalist = document.getElementById(`micro-models-` + fieldsetId);
    // enable/disable the input wether the datalist is filled/empty (i.e. the brand doesn't exist)
    document.getElementById(`micro-model-` + fieldsetId).disabled = !await fillDatalist(modelDatalist, modelsUrl);

    const controllersUrl = `/api/v1/listControllers.php?compagny=${compagnyInput.value}&brand=${this.value}`
    let controllerDatalist = document.getElementById(`micro-controllers-` + fieldsetId);
    // enable/disable the input wether the datalist is filled/empty (i.e. the compagny doesn't exist)
    document.getElementById(`micro-controller-` + fieldsetId).disabled = !await fillDatalist(controllerDatalist, controllersUrl);
}

/** Returns false if the datalist is empty */
async function fillDatalist(datalist, url) {
    // get data from the url
    const response = await fetch(url);
	const data = await response.json();

    let innerHTML = "";
	for (let item of data) {
		innerHTML += `<option value="${item.name}">`;
	}
    datalist.innerHTML = innerHTML;

    return innerHTML != ""
}

// add new fieldsets on add button click
let microscopeFields = document.getElementById("microscopes")
let nextMicroFieldId = 1;

let addMicroButton = document.getElementById("add-micro");
addMicroButton.onclick = addMicroscopeField;

function addMicroscopeField() {
    id = nextMicroFieldId++;

    // create the form fieldset
    microscope = document.createElement("fieldset");
    microscope.id = "micro-field-" + id;
    microscope.innerHTML = document.getElementById("micro-field-0").innerHTML.replaceAll("[0]", `[${id}]`).replaceAll("-0", `-${id}`);
    
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

    // add focusout listeners on inputs to fill datalists
    document.getElementById("micro-compagny-" + id).addEventListener("focusout", compagnyFocusOut);
    document.getElementById("micro-brand-" + id).addEventListener("focusout", brandFocusOut);
}