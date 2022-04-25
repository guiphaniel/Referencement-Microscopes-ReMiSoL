// TODO: block submit button if fields are badly filled (disabled, bad email)
/* fill coherent microscope infomations dataLists on focusout */

//init focusout listeners for microscopes inputs
{
    let compagnyInput = document.getElementById("micro-compagny-0");
    compagnyInput.addEventListener("focusout", compagnyFocusOut);

    let brandInput = document.getElementById("micro-brand-0");
    brandInput.addEventListener("focusout", brandFocusOut);
}

async function compagnyFocusOut() {
    const fieldsetId = this.id.split('-')[2];
    
    if(!isInputDatalistValid(this, document.getElementById("micro-compagnies"))) {
        document.getElementById(`micro-brand-` + fieldsetId).disabled = true;
        document.getElementById(`micro-model-` + fieldsetId).disabled = true;
        document.getElementById(`micro-controller-` + fieldsetId).disabled = true;

        return;
    }
    
    const url = `/api/v1/listBrands.php?compagny=${this.value}`;
    
    let brandDatalist = document.getElementById(`micro-brands-` + fieldsetId);

    await fillDatalist(brandDatalist, url);
    
    document.getElementById(`micro-brand-` + fieldsetId).disabled = false;
}

async function brandFocusOut() {
    const fieldsetId = this.id.split('-')[2];

    if(!isInputDatalistValid(this, document.getElementById("micro-brands-" + fieldsetId))) {
        document.getElementById(`micro-model-` + fieldsetId).disabled = true;
        document.getElementById(`micro-controller-` + fieldsetId).disabled = true;

        return;
    }

    let compagnyInput = document.getElementById("micro-compagny-" + fieldsetId);

	const modelsUrl = `/api/v1/listModels.php?compagny=${compagnyInput.value}&brand=${this.value}`;
    let modelDatalist = document.getElementById(`micro-models-` + fieldsetId);
    await fillDatalist(modelDatalist, modelsUrl);

    const controllersUrl = `/api/v1/listControllers.php?compagny=${compagnyInput.value}&brand=${this.value}`
    let controllerDatalist = document.getElementById(`micro-controllers-` + fieldsetId);
    await fillDatalist(controllerDatalist, controllersUrl);

    document.getElementById(`micro-model-` + fieldsetId).disabled = false;
    document.getElementById(`micro-controller-` + fieldsetId).disabled = false;
}

async function fillDatalist(datalist, url) {
    // get data from the url
    const response = await fetch(url);
	const data = await response.json();

    let innerHTML = "";
	for (let item of data) {
		innerHTML += `<option value="${item.name}">`;
	}
    datalist.innerHTML = innerHTML;
}

function isInputDatalistValid(input, datalist) {
    return datalist.querySelector("option[value='" + input.value + "']") != null;
}

/* add new fieldsets on add button click */
let microscopeFields = document.getElementById("microscopes")
let nextMicroFieldId = 1;

let addMicroButton = document.getElementById("add-micro");
addMicroButton.onclick = addMicroscopeField;

// save original fieldset innerHTML (so keywords won't be duplicated when adding new micro)
originalMicroFieldHTML = document.getElementById("micro-field-0").innerHTML;

function addMicroscopeField() {
    id = nextMicroFieldId++;

    // create the form fieldset
    microField = document.createElement("fieldset");
    microField.id = "micro-field-" + id;
    microField.innerHTML = originalMicroFieldHTML.replaceAll("[0]", `[${id}]`).replaceAll("-0", `-${id}`);
    
    // add the form fieldset at the end of the form
    microscopeFields.insertBefore(microField, addMicroButton);

    // append the remove button to the fieldset
    let rmButton = document.createElement("div")
    rmButton.className = "rm-bt";
    rmButton.id = "rm-micro-" + id;
    rmButton.addEventListener('click', function(){
        let microId = this.id.split('-')[2]; // retrieve the id of the rmButton, which is the one of the fieldset too
        document.getElementById("micro-field-" + microId).remove();
        this.remove()
    });
    microscopeFields.insertBefore(rmButton, addMicroButton);

    //add listeners
    // add focusout listeners on micro infos inputs to fill datalists
    document.getElementById("micro-compagny-" + id).addEventListener("focusout", compagnyFocusOut);
    document.getElementById("micro-brand-" + id).addEventListener("focusout", brandFocusOut);
    // add focusout listeners on keywords input
    initKeywordFocusout(microField)
}

/* add multiple keywords */
//init first default fieldset
{
    let microField = document.getElementById("micro-field-0");
    initKeywordFocusout(microField);
}

/** init focusout listeners for keywords inputs */
function initKeywordFocusout(microField) {
    let catInputs = microField.getElementsByClassName("cat-input");

    for (const catInput of catInputs) {
        catInput.addEventListener('focusout', function() {
            let id = this.id.split('-')[1]; // retrieve the id of the input

            if(!isInputDatalistValid(this, document.getElementById("cats-" + id)))
                return;

            addKeyword(this.value, this)
        });
    }
}
//TODO: add keyword to hidden input
function addKeyword(keyword, catInput) {
    // if the keyword is already selected, do nothing
    tags = catInput.parentElement.getElementsByClassName("tag");
    for (const tag of tags) {
        if(tag.textContent == keyword)
            return;
    }

    catInput.value = "";

    const tag = document.createElement('div');
    tag.className = "tag";

    const rmBt = document.createElement("div");
    rmBt.className = "rm-bt"
    rmBt.dataset.tagId = tag.id;
    rmBt.addEventListener('click', function() {
        this.parentElement.remove();
    });
    tag.append(rmBt);

    tag.append(keyword);

    catInput.parentElement.append(tag);
}