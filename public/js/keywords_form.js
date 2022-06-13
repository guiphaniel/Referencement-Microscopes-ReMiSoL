document.addEventListener("click", onBtClick);

function onBtClick(e) {
    let bt = e.target;

    if(bt.className.includes("add-tag"))
        addTag(bt);
    if(bt.className.includes("add-cat"))
        addCat(bt);
    if(bt.className.includes("rm-bt")) {
        let previous = bt.parentElement.previousElementSibling;
        
        if(previous == null) 
            previous = bt.parentElement;

        const y = previous.getBoundingClientRect().top + window.pageYOffset - 200;
        window.scrollTo({top: y, behavior: 'smooth'});
        
        bt.parentElement.remove();
    }
}

function addTag(bt) {
    let tagsWrapper = bt.parentElement;
    nextTagId = tagsWrapper.dataset.nextTagId++;
    catId = tagsWrapper.dataset.catId;

    let tag = createTag(catId, nextTagId);

    bt.insertAdjacentElement("beforebegin", tag);

    //animation
    tag.classList.add("closed");
    const y = tag.getBoundingClientRect().top + window.pageYOffset - 200;
    window.scrollTo({top: y, behavior: 'smooth'});
    window.setTimeout(() => tag.classList.remove("closed"), 1); // a timeout is needed, else css wont trigger the transition
}

function createTag(catId, tagId) {
    let inputRmWrapper = createInputRmWrapper("tag-" + tagId, `keywords[${catId}][${tagId}]`, "Etiquette");

    return inputRmWrapper;
}

function addCat(bt) {
    let catsWrapper = bt.parentElement;

    let cat = createCat(catsWrapper.dataset.nextCatId++);
    bt.insertAdjacentElement("beforebegin", cat);

    //animation
    cat.classList.add("closed");
    const y = cat.getBoundingClientRect().top + window.pageYOffset - 200;
    window.scrollTo({top: y, behavior: 'smooth'});
    window.setTimeout(() => cat.classList.remove("closed"), 1); // a timeout is needed, else css wont trigger the transition
}

function createCat(catId) {
    let catWrapper = document.createElement("fieldset");

    let catInputWrapper = createInputWrapper("cat-" + catId, `cats[${catId}]`, "Catégorie");

    catWrapper.append(catInputWrapper);

    let tagsWrapper = document.createElement("fieldset");
    tagsWrapper.dataset.nextTagId = 1;
    tagsWrapper.dataset.catId = catId;   

    let addTagBt = document.createElement("div");
    addTagBt.className = "bt add-bt add-tag";
    addTagBt.textContent = "Ajouter une étiquette"
    
    tagsWrapper.appendChild(addTagBt);

    catWrapper.append(tagsWrapper);

    let rmBt = document.createElement("div");
    rmBt.className = "bt rm-bt";
    rmBt.textContent = "Supprimer la catégorie";

    catWrapper.append(rmBt);

    return catWrapper;
}

function createInputWrapper(inputId, inputName, labelContent) {
    let inputWrapper = document.createElement("div");
    inputWrapper.className = "input-wrapper";    
    
    let input = document.createElement("input");
    input.id = inputId;
    input.className = "ucfirst";
    input.type = "text";
    input.name = inputName;
    input.placeholder = " ";
    input.required = true;

    let label = document.createElement("label");
    label.for = input.id;
    label.textContent = labelContent;
    
    inputWrapper.appendChild(input);
    inputWrapper.appendChild(label);

    return inputWrapper;
}

function createInputRmWrapper(inputId, inputName, labelContent) {
    let inputRmWrapper = document.createElement("div");
    inputRmWrapper.className = "input-rm-wrapper";

    let inputWrapper = createInputWrapper(inputId, inputName, labelContent);

    let rmBt = document.createElement("div");
    rmBt.className = "bt rm-bt";
    rmBt.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM31.1 128H416V448C416 483.3 387.3 512 352 512H95.1C60.65 512 31.1 483.3 31.1 448V128zM111.1 208V432C111.1 440.8 119.2 448 127.1 448C136.8 448 143.1 440.8 143.1 432V208C143.1 199.2 136.8 192 127.1 192C119.2 192 111.1 199.2 111.1 208zM207.1 208V432C207.1 440.8 215.2 448 223.1 448C232.8 448 240 440.8 240 432V208C240 199.2 232.8 192 223.1 192C215.2 192 207.1 199.2 207.1 208zM304 208V432C304 440.8 311.2 448 320 448C328.8 448 336 440.8 336 432V208C336 199.2 328.8 192 320 192C311.2 192 304 199.2 304 208z"/></svg>';

    inputRmWrapper.appendChild(inputWrapper);
    inputRmWrapper.appendChild(rmBt);

    return inputRmWrapper;
}