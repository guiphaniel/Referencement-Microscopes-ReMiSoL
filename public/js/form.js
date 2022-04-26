// TODO: block submit button if fields are badly filled (disabled, bad email)
/* fill coherent microscope infomations dataLists on input */

//init input listeners for microscopes inputs
{
    let compagnyInput = document.getElementById("micro-compagny-0");
    compagnyInput.addEventListener("input", onCompagnyInput);

    let brandInput = document.getElementById("micro-brand-0");
    brandInput.addEventListener("input", onBrandInput);
}

async function onCompagnyInput() {
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

async function onBrandInput() {
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

/* add new contact fieldset on add button click */
let contactFields = document.getElementById("contacts")
let nextContactFieldId = 1;

let addContactButton = document.getElementById("add-contact");
addContactButton.onclick = addContactField;

// save original fieldset innerHTML (so keywords won't be duplicated when adding new micro)
originalContactFieldHTML = document.getElementById("contact-field-0").innerHTML;

function addContactField() {
    let id = nextContactFieldId++;

    // create the form fieldset
    let contactField = document.createElement("fieldset");
    contactField.id = "contact-field-" + id;
    contactField.innerHTML = originalContactFieldHTML.replaceAll("[0]", `[${id}]`).replaceAll("-0", `-${id}`);
    
    // add the form fieldset at the end of the form
    contactFields.insertBefore(contactField, addContactButton);

    // append the remove button to the fieldset
    let rmButton = document.createElement("div")
    rmButton.className = "rm-bt";
    rmButton.id = "rm-contact-" + id;
    rmButton.addEventListener('click', function(){
        this.parentElement.remove()
    });
    contactField.append(rmButton);
}

/* add new microscope fieldset on add button click */
let microscopeFields = document.getElementById("microscopes")
let nextMicroFieldId = 1;

let addMicroButton = document.getElementById("add-micro");
addMicroButton.onclick = addMicroscopeField;

// save original fieldset innerHTML (so keywords won't be duplicated when adding new micro)
originalMicroFieldHTML = document.getElementById("micro-field-0").innerHTML;

function addMicroscopeField() {
    let id = nextMicroFieldId++;

    // create the form fieldset
    let microField = document.createElement("fieldset");
    microField.id = "micro-field-" + id;
    microField.innerHTML = originalMicroFieldHTML.replaceAll("[0]", `[${id}]`).replaceAll("-0", `-${id}`);
    
    // add the form fieldset at the end of the form
    microscopeFields.insertBefore(microField, addMicroButton);

    // append the remove button to the fieldset
    let rmButton = document.createElement("div")
    rmButton.className = "rm-bt";
    rmButton.id = "rm-micro-" + id;
    rmButton.addEventListener('click', function(){
        this.parentElement.remove();
    });
    microField.append(rmButton);

    //add listeners
    // add input listeners on micro infos inputs to fill datalists
    document.getElementById("micro-compagny-" + id).addEventListener("input", onCompagnyInput);
    document.getElementById("micro-brand-" + id).addEventListener("input", onBrandInput);
    // add input listeners on keywords input
    initKeywordCatInput(microField)
}

/* add multiple keywords */
//init first default fieldset
{
    let microField = document.getElementById("micro-field-0");
    initKeywordCatInput(microField);
}

/** init input listeners for keywords inputs */
function initKeywordCatInput(microField) {
    let catInputs = microField.getElementsByClassName("cat-input");

    for (const catInput of catInputs) {
        catInput.addEventListener('input', function() {
            let id = this.id.split('-')[1]; // retrieve the id of the input

            if(!isInputDatalistValid(this, document.getElementById("cats-" + id)))
                return;

            addKeyword(this.value, this)
        });
    }
}

function addKeyword(keyword, catInput) {
    // retrieve hidden input
    let infos = catInput.id.split('-');
    let cat = infos[1];
    let id = infos[2];

    // if the keyword is already selected, do nothing
    tags = catInput.parentElement.getElementsByClassName("tag");
    for (const tag of tags) {
        if(tag.textContent == keyword)
            return;
    }

    // clear the input
    catInput.value = "";

    // add tag
    const tag = document.createElement('div');
    tag.className = "tag";

    const rmBt = document.createElement("div");
    rmBt.className = "rm-bt"
    rmBt.addEventListener('click', function() {        
        this.parentElement.remove();
    });
    tag.append(rmBt);

    tag.append(keyword);

    catInput.parentElement.append(tag);

    // add hidden input with keyword
    let hiddenInput = document.createElement("input")
    hiddenInput.id = (`micro-kw-${cat}-${id}`)
    hiddenInput.setAttribute("type",  "hidden")
    hiddenInput.setAttribute("name",  `microscopes[${id}][keywords][${catInput.parentElement.getElementsByTagName("label")[0].innerText}][]`)
    hiddenInput.value = keyword;
    tag.append(hiddenInput);
}