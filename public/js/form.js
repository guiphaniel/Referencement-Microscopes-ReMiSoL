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

    if(bt.className == "rm-bt") {
        const y = bt.parentElement.previousElementSibling.getBoundingClientRect().top + window.pageYOffset - 200;
        window.scrollTo({top: y, behavior: 'smooth'});

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
    rmButton.className = "rm-bt";
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
    rmBt.innerHTML = "X"
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
        rmBt.innerHTML = "Supprimer l'image"
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




