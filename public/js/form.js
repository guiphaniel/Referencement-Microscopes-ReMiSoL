// fill coherent dataLists on focusOut
// TODO: add listener for new fieldsets too

//init focus out listeners for inputs
{
    let compagnyInput = document.getElementById("micro-compagny-0");
    compagnyInput.addEventListener("focusout", fillBrandsDatalist);
    let brandInput = document.getElementById("micro-brand-0");
    brandInput.addEventListener("focusout", fillModelsDatalist);
    brandInput.addEventListener("focusout", fillControllersDatalist);
}

async function fillBrandsDatalist() {
	const fieldsetId = this.id.split('-')[2];
    
    const url = `/api/v1/listBrands.php?compagny=${this.value}`;

    let brandsDatalist = document.getElementById("micro-brands-" + fieldsetId);
    await fillDatalist(brandsDatalist, url);

    //TODO: activate / deactivate the inputs, dependending if the datalist is empty or not.
}

async function fillModelsDatalist() {
    const fieldsetId = this.id.split('-')[2];

    let compagnyInput = document.getElementById("micro-compagny-" + fieldsetId);
	const url = `/api/v1/listModels.php?compagny=${compagnyInput.value}&brand=${this.value}`;

    let modelsDatalist = document.getElementById("micro-models-" + fieldsetId);
    await fillDatalist(modelsDatalist, url);

    //TODO: activate / deactivate the inputs, dependending if the datalist is empty or not.
}

async function fillControllersDatalist() {
    const fieldsetId = this.id.split('-')[2];

    let compagnyInput = document.getElementById("micro-compagny-" + fieldsetId);
    const url = `/api/v1/listControllers.php?compagny=${compagnyInput.value}&brand=${this.value}`

    let controllersDatalist = document.getElementById("micro-controllers-" + fieldsetId);
	await fillDatalist(controllersDatalist, url);

    //TODO: activate / deactivate the inputs, dependending if the datalist is empty or not.
}

/** returns false if the datalist is empty */
async function fillDatalist(datalist, url) {
	const response = await fetch(url);
	const data = await response.json();

    let innerHTML = "";
	for (let item of data) {
		innerHTML += `<option value="${item.name}">`;
	}
    datalist.innerHTML = innerHTML;

    return innerHTML == ""
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
    document.getElementById("micro-compagny-" + id).addEventListener("focusout", fillBrandsDatalist);
    let brandInput = document.getElementById("micro-brand-" + id);
    brandInput.addEventListener("focusout", fillModelsDatalist);
    brandInput.addEventListener("focusout", fillControllersDatalist);
}