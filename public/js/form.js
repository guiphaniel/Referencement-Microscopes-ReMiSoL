// TODO: block submit button if fields are badly filled (disabled, bad email)
/* fill coherent microscope infomations dataLists on input */

//init input listeners for microscopes inputs
document.addEventListener("input", onInput);

function onInput(event) {
    let input = event.target;

    if(input.className == "micro-compagy")
        onCompagnyInput(input);
    else if(input.className == "micro-brand")
        onBrandInput(input);
}

//init listeners for bt clicks
document.addEventListener("click", onClick);

function onClick(event) {
    let bt = event.target;

    if(bt.className == "rm-bt") {
        bt.parentElement.remove()
        if(bt.dataset.type == "ol") {
            // update other fields' legend index
            let cpt = 1;
            for (const field of document.getElementsByClassName(bt.id.split("-")[1] + "-field")) {
                let legend = field.querySelector("legend");
                legend.textContent =legend.textContent.substring(0, legend.textContent.lastIndexOf('°') + 1) + cpt++;
            }
        }
    }
}

async function onCompagnyInput(input) {
    const fieldsetId = input.id.split('-')[2];
    
    if(input.value == "Homemade") {
        let brandInput = document.getElementById(`micro-brand-` + fieldsetId);
        let modelInput = document.getElementById(`micro-model-` + fieldsetId);

        brandInput.value = "Homemade";
        document.getElementById(`micro-brands-` + fieldsetId).innerHTML = "<option value='Homemade'>";
        brandInput.disabled = false;
        modelInput.value = "Homemade";
        document.getElementById(`micro-models-` + fieldsetId).innerHTML = "<option value='Homemade'>";
        modelInput.disabled = false;

        let url = "/api/v1/listControllers.php";
        let controllerDatalist = document.getElementById(`micro-controllers-` + fieldsetId);
        fillDatalist(controllerDatalist, url).then(() => document.getElementById(`micro-controller-` + fieldsetId).disabled = false);
    } else {
        if(!isInputDatalistValid(input, document.getElementById("micro-compagnies"))) {
            document.getElementById(`micro-brand-` + fieldsetId).disabled = true;
            document.getElementById(`micro-model-` + fieldsetId).disabled = true;
            document.getElementById(`micro-controller-` + fieldsetId).disabled = true;
    
            return;
        }
    }

    const url = `/api/v1/listBrands.php?compagny=${input.value}`;
    
    let brandDatalist = document.getElementById(`micro-brands-` + fieldsetId);

    fillDatalist(brandDatalist, url).then(() => document.getElementById(`micro-brand-` + fieldsetId).disabled = false);
}

async function onBrandInput(input) {
    const fieldsetId = input.id.split('-')[2];

    if(!isInputDatalistValid(input, document.getElementById("micro-brands-" + fieldsetId))) {
        document.getElementById(`micro-model-` + fieldsetId).disabled = true;
        document.getElementById(`micro-controller-` + fieldsetId).disabled = true;

        return;
    }

    let compagnyInput = document.getElementById("micro-compagny-" + fieldsetId);

	const modelsUrl = `/api/v1/listModels.php?brand=${input.value}`;
    let modelDatalist = document.getElementById(`micro-models-` + fieldsetId);
    fillDatalist(modelDatalist, modelsUrl).then(() => document.getElementById(`micro-model-` + fieldsetId).disabled = false);

    const controllersUrl = `/api/v1/listControllers.php?brand=${input.value}`
    let controllerDatalist = document.getElementById(`micro-controllers-` + fieldsetId);
    fillDatalist(controllerDatalist, controllersUrl).then(() => document.getElementById(`micro-controller-` + fieldsetId).disabled = false);
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

function addField(fieldType, fieldId, firstField) {
    let firstFieldId = firstField.id.split('-')[2];
    let fieldsWrapper = document.getElementById(fieldType + "s");

    // create the form fieldset
    let newField = document.createElement("fieldset");
    newField.id = fieldType + "-field-" + fieldId;
    newField.className = fieldType + "-field";
    newField.innerHTML = firstField.innerHTML.replaceAll(`[${firstFieldId}]`, `[${fieldId}]`).replaceAll(`-${firstFieldId}`, `-${fieldId}`);
    // update legend's index
    let legend = newField.querySelector("legend");
    legend.textContent = legend.textContent.substring(0, legend.textContent.lastIndexOf('°') + 1) + (document.getElementsByClassName(fieldType + "-field").length + 1);

    // add the form fieldset at the end of the form (before the add button)
    let addButton = document.getElementById("add-" + fieldType);
    fieldsWrapper.insertBefore(newField, addButton);

    // append the remove button to the fieldset
    let rmButton = document.createElement("div")
    rmButton.className = "rm-bt";
    rmButton.dataset.type = "ol"; // ordered list, so other elements get their index updated
    rmButton.id = "rm-" + fieldType + "-" + fieldId;

    newField.append(rmButton);

    return newField;
}


/* add new contact fieldset on add button click */
let contactFields = document.getElementsByClassName("contact-field");
let firstContactField = contactFields[0];

let nextContactFieldId = parseInt(contactFields[contactFields.length - 1].id.split('-')[2]) + 1;

document.getElementById("add-contact").addEventListener('click', function(){
    let newField = addField("contact", nextContactFieldId++, firstContactField);
    // reset the inputs
    let inputs = newField.getElementsByTagName("input");
    for(input of inputs) {
        input.value = "";
    };
    let phoneCodeSelect = newField.getElementsByTagName("select")[0];
    phoneCodeSelect.value = "+33";
});


/* add new microscope fieldset on add button click */
let microcopesFields = document.getElementsByClassName("micro-field");
let firstMicroscopeField = microcopesFields[0];

let nextMicroscopeFieldId = parseInt(microcopesFields[microcopesFields.length - 1].id.split('-')[2]) + 1;

document.getElementById("add-micro").addEventListener('click', function(){
    let id = nextMicroscopeFieldId;

    let newField = addField("micro", nextMicroscopeFieldId++, firstMicroscopeField);

    resetField(newField);

    let tags = newField.getElementsByClassName("tag");
    while (tags.length > 0)
        tags[0].remove()

    document.getElementById(`micro-brand-` + id).disabled = true;
    document.getElementById(`micro-model-` + id).disabled = true;
    document.getElementById(`micro-controller-` + id).disabled = true;
});

function resetField(field) {
    let inputs = field.getElementsByTagName("input");
    for (input of inputs) {
        input.value = null;
    };

    let selects = field.getElementsByTagName("select");
    for (select of selects) {
        select.selectedIndex = 0;
    };

    let textareas = field.getElementsByTagName("textarea");
    for (textarea of textareas) {
        textarea.innerText = null;
    };

    let images = field.getElementsByClassName("micro-snapshot");
    while(images.length > 0)
        images[0].remove();
}

/* add multiple keywords */
document.addEventListener('input', function(event) {
    let input = event.target;
    if(input.className != "cat-input")
        return;

    let cat = input.id.split('-')[1]; // retrieve the cat of the input

    if(!isInputDatalistValid(input, document.getElementById("cats-" + cat)))
        return;

    addKeyword(input.value, input)
});

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
    rmBt.className = "rm-bt";
    rmBt.dataset.type = "ul";

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

/* IMG FILE INPUT */

document.addEventListener("change", function(event) {
    let imgInput = event.target;

    // check if the input is a file input
    if (imgInput.type != "file")
        return;

    // set keepImg to false
    let snapshotWrapper = imgInput.nextElementSibling;
    if(snapshotWrapper != undefined) {
        // if the input is empty, remove the last displayed snapshot
        if (imgInput.files.length == 0) {
            snapshotWrapper.remove();
            return;
        }

        let keepImg = snapshotWrapper.lastElementChild;
        keepImg.value = false
    }
        

    // check if the file is an img
    let imageType = /^image\//;

    let file = imgInput.files[0];
    if (!imageType.test(file.type))
        return;

    // display the snapshot
    // if a previous snapshot already existed, replace its url, else, create a new snapshot
    if(snapshotWrapper == undefined) {// check a snapshot wrapper already existed. If not, create one.
        snapshotWrapper =  document.createElement("div");

        let snapshot = document.createElement("img");
        snapshot.className = "micro-snapshot";

        const rmBt = document.createElement("div");
        rmBt.className = "rm-bt";
        rmBt.addEventListener("click", function(e) {e.target.parentElement.previousSibling.value = null}) // set input value to null
        
        snapshotWrapper.append(snapshot);
        snapshotWrapper.append(rmBt);

        imgInput.insertAdjacentElement("afterend", snapshotWrapper);
    }

    let snapshot = snapshotWrapper.firstElementChild;
    let reader = new FileReader();
    reader.onload = (function(snapshot) { return function(e) { snapshot.src = e.target.result; }; })(snapshot);
    reader.readAsDataURL(file);
})




