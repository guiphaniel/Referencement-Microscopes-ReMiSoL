// TODO: block submit button if fields are badly filled (disabled, bad email)
/* fill coherent microscope infomations dataLists on input */

//init input listeners for microscopes inputs
{
    // add input listeners on micro infos inputs to fill datalists
    let compagnyInput = document.getElementById("micro-compagny-0");
    compagnyInput.addEventListener("input", onCompagnyInput);

    let brandInput = document.getElementById("micro-brand-0");
    brandInput.addEventListener("input", onBrandInput);

    // add change listener for the micro's type
    let typeInput = document.getElementById("micro-type-0");
    typeInput.addEventListener("change", onTypeChange);
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

    fillDatalist(brandDatalist, url).then(document.getElementById(`micro-brand-` + fieldsetId).disabled = false);
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
    fillDatalist(modelDatalist, modelsUrl).then(document.getElementById(`micro-model-` + fieldsetId).disabled = false);

    const controllersUrl = `/api/v1/listControllers.php?compagny=${compagnyInput.value}&brand=${this.value}`
    let controllerDatalist = document.getElementById(`micro-controllers-` + fieldsetId);
    fillDatalist(controllerDatalist, controllersUrl).then(document.getElementById(`micro-controller-` + fieldsetId).disabled = false);
}

async function onTypeChange() {
    const fieldsetId = this.id.split('-')[2];

    document.getElementById(`micro-rate-` + fieldsetId).disabled = this.value == "LAB";
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

/* ADD FIELDSETS */

function addField(fieldType, fieldId, innerHTML) {
    let fieldsWrapper = document.getElementById(fieldType + "s");

    // create the form fieldset
    let newField = document.createElement("fieldset");
    newField.id = fieldType + "-field-" + fieldId;
    newField.className = fieldType + "-field";
    newField.innerHTML = innerHTML.replaceAll("[0]", `[${fieldId}]`).replaceAll("-0", `-${fieldId}`);
    // update legend's index
    let legend = newField.querySelector("legend");
    legend.textContent = legend.textContent.substring(0, legend.textContent.lastIndexOf('°') + 1) + (document.getElementsByClassName(fieldType + "-field").length + 1);

    // add the form fieldset at the end of the form (before the add button)
    let addButton = document.getElementById("add-" + fieldType);
    fieldsWrapper.insertBefore(newField, addButton);

    // append the remove button to the fieldset
    let rmButton = document.createElement("div")
    rmButton.className = "rm-bt";
    rmButton.id = "rm-" + fieldType + "-" + fieldId;
    rmButton.addEventListener('click', function(){
        this.parentElement.remove()

        // update other fields' legend index
        let cpt = 1;
        for (const field of document.getElementsByClassName(fieldType + "-field")) {
            let legend = field.querySelector("legend");
            legend.textContent =legend.textContent.substring(0, legend.textContent.lastIndexOf('°') + 1) + cpt++;
        }
    });
    newField.append(rmButton);

    return newField;
}


/* add new contact fieldset on add button click */

let nextContactFieldId = 1;

// save original fieldset innerHTML
let originalContactFieldHTML = document.getElementById("contact-field-0").innerHTML;

document.getElementById("add-contact").addEventListener('click', function(){
    addField("contact", nextContactFieldId++, originalContactFieldHTML);
});


/* add new microscope fieldset on add button click */

let nextMicroscopeFieldId = 1;

// save original fieldset innerHTML
let originalMicroscopeFieldHTML = document.getElementById("micro-field-0").innerHTML;

document.getElementById("add-micro").addEventListener('click', function(){
    let id = nextMicroscopeFieldId;

    microField = addField("micro", nextMicroscopeFieldId++, originalMicroscopeFieldHTML);

    // add input listeners on micro infos inputs to fill datalists
    document.getElementById("micro-compagny-" + id).addEventListener("input", onCompagnyInput);
    document.getElementById("micro-brand-" + id).addEventListener("input", onBrandInput);

    // add change listener for the micro's type
    document.getElementById("micro-type-" + id).addEventListener("change", onTypeChange);

    // add input listeners on keywords input
    initKeywordCatInput(microField)
});


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
    hiddenInput.setAttribute("name",  `micros[${id}][keywords][${catInput.parentElement.getElementsByTagName("label")[0].innerText}][]`)
    hiddenInput.value = keyword;
    tag.append(hiddenInput);
}