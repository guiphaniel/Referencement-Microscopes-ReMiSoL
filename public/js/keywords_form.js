document.addEventListener("click", onBtClick);

function onBtClick(e) {
    let bt = e.target;

    if(bt.className.includes("add-tag"))
        addTag(bt);
    if(bt.className.includes("add-cat"))
        addCat(bt);
    if(bt.className.includes("rm-bt"))
        bt.parentElement.remove();
}

function addTag(bt) {
    let tagsWrapper = bt.parentElement;
    nextTagId = tagsWrapper.dataset.nextTagId++;
    catId = tagsWrapper.dataset.catId;

    let tag = createTag(catId, nextTagId);
    bt.insertAdjacentElement("beforebegin", tag);
}

function createTag(catId, tagId) {
    let div = document.createElement("div");

    let input = document.createElement("input");
    input.className = "kw-tag";
    input.type = "text";
    input.name = `keywords[${catId}][${tagId}]`;

    let rmBt = document.createElement("div");
    rmBt.className = "rm-bt"

    div.appendChild(input);
    div.appendChild(rmBt);

    return div;
}

function addCat(bt) {
    let catsWrapper = document.getElementById("cats-wrapper");

    let cat = createCat(catsWrapper.dataset.nextCatId++);
    bt.insertAdjacentElement("beforebegin", cat);
}

function createCat(catId) {
    let catWrapper = document.createElement("div");

    let input = document.createElement("input");
    input.className = "kw-cat";
    input.type = "text";
    input.name = `cats[${catId}]`;

    let rmBt = document.createElement("div");
    rmBt.className = "rm-bt"

    let tagsWrapper = document.createElement("div");
    tagsWrapper.dataset.nextTagId = 1;
    tagsWrapper.dataset.catId = catId;

    let addTagBt = document.createElement("div");
    addTagBt.className = "add-bt add-tag";

    catWrapper.appendChild(input);
    catWrapper.appendChild(rmBt);
    tagsWrapper.appendChild(addTagBt);
    catWrapper.appendChild(tagsWrapper);

    return catWrapper;
}