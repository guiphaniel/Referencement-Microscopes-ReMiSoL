document.addEventListener("click", onBtClick);

function onBtClick(e) {
    let bt = e.target;

    if(bt.className == "add-bt") {
        let parentElement = bt.parentElement;
        let elem;
        if(bt.id == "add-model")
            elem = createModelInputWrapper(parentElement);
        else if(bt.id == "add-ctr")
            elem = createCtrInputWrapper(parentElement);
        else if(bt.id == "add-brand")
            elem = createBrandWrapper(parentElement);
        else if(bt.id == "add-cmp")
            elem = createCompagnyWrapper(parentElement);

        bt.insertAdjacentElement("beforebegin", elem);
    } else if(bt.className == "rm-bt")
        bt.parentElement.remove();
}

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function createContentElement(type, textContent) {
	let elem = document.createElement(type);
	elem.textContent = textContent;
	
	return elem;
}

function createH(level, textContent) {
	return createContentElement("h" + level, textContent);
}

function createModelInputWrapper(modelsWrapper) {
    let nextModelId = modelsWrapper.dataset.nextModelId++;
    let brandId = modelsWrapper.dataset.parentId;
    let cmpId = modelsWrapper.parentElement.parentElement.dataset.parentId;

    let inputName = `models[${cmpId}][${brandId}][${nextModelId}]`;

    return createInputWrapper("model", inputName);
}

function createCtrInputWrapper(ctrsWrapper) {
    nextCtrId = ctrsWrapper.dataset.nextCtrId++;
    brandId = ctrsWrapper.dataset.parentId;
    cmpId = ctrsWrapper.parentElement.parentElement.dataset.parentId;

    let inputName = `ctrs[${cmpId}][${brandId}][${nextCtrId++}]`;

    return createInputWrapper("ctr", inputName);
}

function createBrandWrapper(parentWrapper) {
    let wrapper = document.createElement("div");
    wrapper.className = "brand-wrapper";

    cmpId = parentWrapper.dataset.parentId;
    nextBrandId = parentWrapper.dataset.nextBrandId++;

    let inputName = `brands[${cmpId}][${nextBrandId}]`;
    let input = createInput(inputName);
    let rmBt = createRmBt();

    let modelsWrapper = createEntitiesWrapper(5, "Modèles", "model", nextBrandId);
    let ctrsWrapper = createEntitiesWrapper(5, "Électroniques / Contrôleurs", "ctr", nextBrandId);

    wrapper.append(input);
    wrapper.append(rmBt);
    wrapper.append(modelsWrapper);
    wrapper.append(ctrsWrapper);

    return wrapper;
}

function createCompagnyWrapper(parentWrapper) {
    let wrapper = document.createElement("div");
    wrapper.className = "cmp-wrapper";

    nextCmpId = parentWrapper.dataset.nextCmpId++;

    let inputName = `cmps[${nextCmpId}]`;
    let input = createInput(inputName);
    let rmBt = createRmBt();

    let brandsWrapper = createEntitiesWrapper(4, "Marques", "brand", nextCmpId)

    wrapper.append(input);
    wrapper.append(rmBt);
    wrapper.append(brandsWrapper);

    return wrapper;
}

/**Creates an input inside a div, for leaf inputs */
function createInputWrapper(type, inputName) {
    let div = document.createElement("div");
    div.className = type + "-input-wrapper";

    let input = createInput(inputName);

    let rmBt = createRmBt();
    
    div.appendChild(input);
    div.appendChild(rmBt);

    return div;
}

function createInput(name) {
    let input = document.createElement("input");
    input.type = "text";
    input.name = name;

    return input;
}

function createRmBt() {
    let rmBt = document.createElement("div");
    rmBt.className = "rm-bt";

    return rmBt;
}

function createEntitiesWrapper(titleLevel, titleContent, type, parentId = null) {
    let wrapper = document.createElement("div");
    wrapper.id = type + "s" + "-wrapper";
    wrapper.dataset[`next${ucfirst(type)}Id`] = 1;
    if(parentId != null)
        wrapper.dataset.parentId = parentId;

    let title = createH(titleLevel, titleContent)

    let addBt = document.createElement("div");
    addBt.id = "add-" + type;
    addBt.className = "add-bt";

    wrapper.appendChild(title);
    wrapper.appendChild(addBt);

    return wrapper;
}
