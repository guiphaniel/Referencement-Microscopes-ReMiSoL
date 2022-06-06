// TODO: block submit button if fields are badly filled (disabled, bad email)
/* fill coherent microscope infomations dataLists on input */

//init input listeners for microscopes inputs
document.addEventListener("change", onChange);

function onChange(event) {
    let select = event.target;

    if(select.className == "micro-compagnies")
        onCompagnySelect(select);
    else if(select.className == "micro-brands")
        onBrandSelect(select);
}

//init listeners for bt clicks
document.addEventListener("click", onClick);

function onClick(event) {
    let bt = event.target;

    if(bt.classList.contains("rm-bt")) {
        const previousTag = bt.parentElement.previousElementSibling;
        if(previousTag != null) {
            const y = previousTag.getBoundingClientRect().top + window.pageYOffset - 200;
            window.scrollTo({top: y, behavior: 'smooth'});
        }

        bt.parentElement.remove();

        if(bt.dataset.type == "ol") {
            // update other fields' legend index
            let cpt = 1;
            for (const field of document.getElementsByClassName(bt.id.split("-")[1] + "-field")) {
                let legend = field.querySelector("legend h3");
                legend.textContent =legend.textContent.substring(0, legend.textContent.lastIndexOf('°') + 1) + cpt++;
            }
        }
    }
}

async function onCompagnySelect(select) {
    const fieldsetId = select.id.split('-')[2];
    
    const brandSelect = document.getElementById(`micro-brands-` + fieldsetId);
    const modelSelect = document.getElementById(`micro-models-` + fieldsetId);
    const controllerSelect = document.getElementById(`micro-controllers-` + fieldsetId);

    if(select.value == "Homemade") {
        brandSelect.value = "Homemade";
        document.getElementById(`micro-brands-` + fieldsetId).innerHTML = "<option value='Homemade'>Homemade</option>";
        brandSelect.disabled = false;
        modelSelect.value = "Homemade";
        document.getElementById(`micro-models-` + fieldsetId).innerHTML = "<option value='Homemade'>Homemade</option>";
        modelSelect.disabled = false;

        let url = "/api/v1/listControllers.php";
        fillSelectOptions(controllerSelect, url);
    } else {
        resetSelect(modelSelect);
        resetSelect(controllerSelect);

        const url = `/api/v1/listBrands.php?compagny=${select.value}`;
    
        fillSelectOptions(brandSelect, url);
    }
}

async function onBrandSelect(select) {
    const fieldsetId = select.id.split('-')[2];

	const modelsUrl = `/api/v1/listModels.php?brand=${select.value}`;
    let modelSelect = document.getElementById(`micro-models-` + fieldsetId);
    fillSelectOptions(modelSelect, modelsUrl);

    const controllersUrl = `/api/v1/listControllers.php?brand=${select.value}`
    let controllerSelect = document.getElementById(`micro-controllers-` + fieldsetId);
    fillSelectOptions(controllerSelect, controllersUrl);
}

async function fillSelectOptions(select, url) {
    // get data from the url
    const response = await fetch(url);
	const data = await response.json();

    let innerHTML = '<option value="" selected disabled hidden>Choisissez ici</option>';
	for (let item of data) {
		innerHTML += `<option value="${item.name}">${item.name}</option>`;
	}
    select.innerHTML = innerHTML;
    select.disabled = false;
}

function isInputDatalistValid(input, datalist) {
    return datalist.querySelector("option[value=\"" + input.value.replace(/"/g, '\\\"') + "\"]") != null;
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
    let legend = newField.querySelector("legend h3");
    legend.textContent = legend.textContent.substring(0, legend.textContent.lastIndexOf('°') + 1) + (document.getElementsByClassName(fieldType + "-field").length + 1);

    // add the form fieldset at the end of the form (before the add button)
    let addButton = document.getElementById("add-" + fieldType);
    fieldsWrapper.insertBefore(newField, addButton);
    const y = newField.getBoundingClientRect().top + window.pageYOffset - 200;

    window.scrollTo({top: y, behavior: 'smooth'});

    newField.classList.add("closed");
    window.setTimeout(() => newField.classList.remove("closed"), 1); // a timeout is needed, else css wont trigger the transition
    

    // append the remove button to the fieldset
    let rmButton = document.createElement("div")
    rmButton.className = "bt rm-bt";
    switch (fieldType) {
        case "contact":
            rmButton.innerHTML = "Supprimer la·le référent·e";
            break;
        case "micro":
            rmButton.innerHTML = "Supprimer le microscope";
            break;
        default:
            rmButton.innerHTML = "Supprimer"
            break;
    }
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

    imgLabel = document.getElementById("micro-img-" + id).labels[0];
    imgLabel.innerText = "Ajouter une image";
    imgLabel.className = "bt add-bt";
    
    resetSelect(document.getElementById(`micro-brands-` + id));
    resetSelect(document.getElementById(`micro-models-` + id));
    resetSelect(document.getElementById(`micro-controllers-` + id));
});

function resetSelect(select) {
    select.disabled = true;
    select.innerHTML = '<option value="" selected disabled hidden>Choisissez ici</option>'
}

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
        images[0].parentElement.remove();
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
    rmBt.className = "bt rm-bt";
    rmBt.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM31.1 128H416V448C416 483.3 387.3 512 352 512H95.1C60.65 512 31.1 483.3 31.1 448V128zM111.1 208V432C111.1 440.8 119.2 448 127.1 448C136.8 448 143.1 440.8 143.1 432V208C143.1 199.2 136.8 192 127.1 192C119.2 192 111.1 199.2 111.1 208zM207.1 208V432C207.1 440.8 215.2 448 223.1 448C232.8 448 240 440.8 240 432V208C240 199.2 232.8 192 223.1 192C215.2 192 207.1 199.2 207.1 208zM304 208V432C304 440.8 311.2 448 320 448C328.8 448 336 440.8 336 432V208C336 199.2 328.8 192 320 192C311.2 192 304 199.2 304 208z"/></svg>';
    rmBt.dataset.type = "ul";

    tag.append(rmBt);

    tag.append(keyword);

    catInput.parentElement.nextElementSibling.append(tag);

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
    let snapshotWrapper = imgInput.previousElementSibling;
    if(snapshotWrapper.className == "snapshot-wrapper") {
        // if the input is empty (no image selected by user), remove the last displayed snapshot
        if (imgInput.files.length == 0) {
            snapshotWrapper.remove();
            label = imgInput.labels[0];
            label.innerText = "Ajouter une image";
            label.className = "bt add-bt";
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
    if(snapshotWrapper.className != "snapshot-wrapper") {// check a snapshot wrapper already existed. If not, create one.
        snapshotWrapper =  document.createElement("div");
        snapshotWrapper.className = "snapshot-wrapper";

        let snapshot = document.createElement("img");
        snapshot.className = "micro-snapshot";

        label = imgInput.labels[0];
        label.innerText = "Modifier l'image";
        label.className = "bt edit-bt";

        const rmBt = document.createElement("div");
        rmBt.className = "bt rm-bt";
        rmBt.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM31.1 128H416V448C416 483.3 387.3 512 352 512H95.1C60.65 512 31.1 483.3 31.1 448V128zM111.1 208V432C111.1 440.8 119.2 448 127.1 448C136.8 448 143.1 440.8 143.1 432V208C143.1 199.2 136.8 192 127.1 192C119.2 192 111.1 199.2 111.1 208zM207.1 208V432C207.1 440.8 215.2 448 223.1 448C232.8 448 240 440.8 240 432V208C240 199.2 232.8 192 223.1 192C215.2 192 207.1 199.2 207.1 208zM304 208V432C304 440.8 311.2 448 320 448C328.8 448 336 440.8 336 432V208C336 199.2 328.8 192 320 192C311.2 192 304 199.2 304 208z"/></svg>';
        rmBt.addEventListener("click", function(e) {imgInput.value = null; label.innerText = "Ajouter une image"; label.className = "bt add-bt";}) // set input value to null
        
        snapshotWrapper.append(snapshot);
        snapshotWrapper.append(rmBt);

        imgInput.insertAdjacentElement("beforebegin", snapshotWrapper);
    }

    let snapshot = snapshotWrapper.firstElementChild;
    let reader = new FileReader();
    reader.onload = (function(snapshot) { return function(e) { snapshot.src = e.target.result; }; })(snapshot);
    reader.readAsDataURL(file);
})




