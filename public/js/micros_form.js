document.addEventListener("click", onBtClick);

function onBtClick(e) {
    let bt = e.target;

    if(bt.classList.contains("add-bt")) {
        let parentElement = bt.parentElement;
        let elem;
        if(bt.id == "add-ctr")
            elem = createCtrInputWrapper(parentElement.parentElement.parentElement.dataset.parentId, parentElement.dataset.parentId, parentElement.dataset.nextCtrId++);
        else if(bt.id == "add-model")
            elem = createModelInputWrapper(parentElement.parentElement.parentElement.dataset.parentId, parentElement.dataset.parentId, parentElement.dataset.nextModelId++);
        else if(bt.id == "add-brand")
            elem = createBrandWrapper(parentElement.dataset.parentId, parentElement.dataset.nextBrandId++);
        else if(bt.id == "add-cmp")
            elem = createCompagnyWrapper(parentElement.dataset.nextCmpId++);

        bt.insertAdjacentElement("beforebegin", elem);

        //animation
        elem.classList.add("closed");
        const y = elem.getBoundingClientRect().top + window.pageYOffset - 200;
        window.scrollTo({top: y, behavior: 'smooth'});
        window.setTimeout(() => elem.classList.remove("closed"), 1); // a timeout is needed, else css wont trigger the transition
    } else if(bt.classList.contains("rm-bt")) {
        let previous = bt.parentElement.previousElementSibling;
            
        if(previous == null) 
            previous = bt.parentElement;

        const y = previous.getBoundingClientRect().top + window.pageYOffset - 200;
        window.scrollTo({top: y, behavior: 'smooth'});
        
        bt.parentElement.remove();
    }  
}

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function createH(level, textContent) {
	return createContentElement("h" + level, textContent);
}

function createContentElement(type, textContent) {
	let elem = document.createElement(type);
	elem.textContent = textContent;
	
	return elem;
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

function createModelInputWrapper(cmpId, brandId, modelId) {
    return createInputRmWrapper(`model-${cmpId}-${brandId}-${modelId}`, `models[${cmpId}][${brandId}][${modelId}]`, "Modèle");
}

function createCtrInputWrapper(cmpId, brandId, ctrId) {
    return createInputRmWrapper(`ctr-${cmpId}-${brandId}-${ctrId}`, `ctrs[${cmpId}][${brandId}][${ctrId}]`, "Contrôleur");
}

function createBrandWrapper(cmpId, brandId) {   
   let wrapper = document.createElement("fieldset");
   wrapper.className = "wrapper";

    let inputWrapper = createInputWrapper(`brand-${cmpId}-${brandId}`, `brands[${cmpId}][${brandId}]`, "Marque");
    
    let modelsWrapper = createEntitiesWrapper("model", createH(4, "Modèles"), "add-model", "Ajouter un modèle", brandId);
    let ctrsWrapper = createEntitiesWrapper("ctr", createH(4, "Contrôleurs"), "add-ctr", "Ajouter un contrôleurs", brandId);
    
    let rmBt = createRmBt("Supprimer la marque");

    wrapper.append(inputWrapper);
    wrapper.append(modelsWrapper);
    wrapper.append(ctrsWrapper);
    wrapper.append(rmBt);

    return wrapper;
}

function createCompagnyWrapper(cmpId) {
    let wrapper = document.createElement("fieldset");
    wrapper.className = "wrapper";

    let inputWrapper = createInputWrapper(`cmp-${cmpId}`, `cmps[${cmpId}]`, "Société");

    let heading = createH(4, "Marques");

    let brandsWrapper = createEntitiesWrapper("brand", heading, "add-brand", "Ajouter une marque", cmpId)
    
    let rmBt = createRmBt("Supprimer la société");

    wrapper.append(inputWrapper);
    wrapper.append(brandsWrapper);
    wrapper.append(rmBt);

    return wrapper;
}

function createEntitiesWrapper(type, heading, addId, addMsg, parentId = null) {
    let wrapper = document.createElement("fieldset");
    wrapper.id = type + "s" + "-wrapper";
    wrapper.dataset[`next${ucfirst(type)}Id`] = 1;
    if(parentId != null)
        wrapper.dataset.parentId = parentId;

    let addBt = createAddBt(addId, addMsg);

    wrapper.appendChild(heading)
    wrapper.appendChild(addBt);

    return wrapper;
}

function createInputWrapper(inputId, inputName, labelContent) {
    let inputWrapper = document.createElement("div");
    inputWrapper.className = "input-wrapper";    
    
    let input = createInput(inputId, inputName);

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

    let rmBt = createRmBt('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM31.1 128H416V448C416 483.3 387.3 512 352 512H95.1C60.65 512 31.1 483.3 31.1 448V128zM111.1 208V432C111.1 440.8 119.2 448 127.1 448C136.8 448 143.1 440.8 143.1 432V208C143.1 199.2 136.8 192 127.1 192C119.2 192 111.1 199.2 111.1 208zM207.1 208V432C207.1 440.8 215.2 448 223.1 448C232.8 448 240 440.8 240 432V208C240 199.2 232.8 192 223.1 192C215.2 192 207.1 199.2 207.1 208zM304 208V432C304 440.8 311.2 448 320 448C328.8 448 336 440.8 336 432V208C336 199.2 328.8 192 320 192C311.2 192 304 199.2 304 208z"/></svg>');

    inputRmWrapper.appendChild(inputWrapper);
    inputRmWrapper.appendChild(rmBt);

    return inputRmWrapper;
}

function createInput(id, name) {
    let input = document.createElement("input");
    input.id = id;
    input.className = "ucfirst";
    input.type = "text";
    input.name = name;
    input.placeholder = " ";
    input.required = true;

    return input;
}

function createRmBt(innerHTML) {
    let rmBt = document.createElement("div");
    rmBt.className = "bt rm-bt";
    rmBt.innerHTML = innerHTML;

    return rmBt;
}

function createAddBt(id, innerHTML) {
    let addBt = document.createElement("div");
    addBt.id = id;
    addBt.className = "bt add-bt";
    addBt.innerHTML = innerHTML;

    return addBt;
}