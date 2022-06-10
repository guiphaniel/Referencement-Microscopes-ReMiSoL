// get page name
let path = window.location.pathname;
let page = path.split("/").pop();

// initialize Leaflet
let map = L.map('map').setView([46.606111, 1.875278], 5);

// add the OpenStreetMap tile and display the license attribution
L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
	maxZoom: 20,
	attribution: '&copy; OpenStreetMap France | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// init groups' markers on the map
let markersClusters = L.markerClusterGroup();
loadAndShowGroups("/api/v1/search.php");

async function loadAndShowGroups(url) {
	markersClusters.clearLayers();

	const response = await fetch(url);
	const groups = await response.json();

	for (let group of groups) {
		// set custom icon color
		let type;
		let first = true;
		for (let micro of Object.values(group.microscopes)) {
			if(first) {
				type = micro.type == "LABO" ? "lab" : "plat";
				first = false;
			} else {
				if((micro.type == "LABO" ? "lab" : "plat") != type) {
					type = "mix";
					break;
				}
			}
		}

		let customIcon = new L.divIcon({
			html: `<div class="legend-icon"><svg><use class="marker ${type}-marker" href="#marker"/></svg></div>`,
			className: "",
			iconSize: [25, 41],
			iconAnchor: [12, 41],
			popupAnchor: [1, -41],
		  });


		let marker = L.marker(group.coor, {alt: group.lab.name, icon: customIcon});
		
		marker.bindPopup(getCustomPopupHTML(group), {maxHeight : 200});

		marker.on('mouseover',function(event) {
			event.target.openPopup();
		});

		marker.on('click',function(event) {
			window.location.href = "/group-details.php?id=" + group.id;
		});
		
		markersClusters.addLayer(marker);

		// zoom on the marker if wer're on it's group-details/edit page
		if((page == "group-details.php" || page == "edit_micros_group.php") && group.id == window.location.search.split("=").pop()) {
			map.setView(group.coor, 16);
		}
	}

	map.addLayer(markersClusters);
}


function normalizeLatLng(LatLng, nbDecimals) {
	return new L.LatLng(normalizeLat(LatLng.lat, nbDecimals), normalizeLng(LatLng.lng, nbDecimals));
}

function normalizeCoor(coor, min, max, nbDecimals) {
	// keep in range
	if(coor < min)
		coor = max - ((min - coor)%(max - min));
	else if (coor > max)
		coor = min + (coor - max)%(max - min);
	
	// retrieve good number of decimals
	pow = 10 ** nbDecimals
	return Math.round(coor * pow) / pow;
}

function normalizeLat(lat, nbDecimals) {
	return normalizeCoor(lat, -90, 90, nbDecimals);
}

function normalizeLng(lng, nbDecimals) {
	return normalizeCoor(lng, -180, 180, nbDecimals);
}


// show coordinates on map click
let popup = L.popup();

function showCoordinates(e) {
	normalizedLatLng = normalizeLatLng(e.latlng, 5);
    popup
        .setLatLng(e.latlng)
        .setContent("lat : " +  normalizedLatLng.lat + ", lon : " + normalizedLatLng.lng)
        .openOn(map);
}

map.on('click', showCoordinates);


function createContentElement(type, textContent) {
	let elem = document.createElement(type);
	elem.textContent = textContent;
	
	return elem;
}

function createH(level, textContent) {
	return createContentElement("h" + level, textContent);
}

function createP(textContent) {
	return createContentElement("p", textContent);
}

function createA(href, text, target = "_self") {
	let a = document.createElement("a");
	a.append(document.createTextNode(text));
	a.href = href;
	a.target = target;
	
	return a;
}

function getCustomPopupHTML(group) {
	let infos = document.createElement("section");

	// lab
	let labName = group.lab.name;
	if(group.lab.type != "Autre")
		labName += " (" + group.lab.type + group.lab.code + ")";
	infos.append(createH(2, labName));

	// website
	{
		let lab = group.lab;
		let label = createP("Site internet : ");
		label.append(createA(lab.website, lab.website, "_blank"));
		infos.append(label);
	}

	// contacts
	infos.append(createH(3, "Référent·e·s"))
	let contactsAddress = document.createElement("address");
	let nb = 1;
	for (const contact of group.contacts) {
		// generate contact infos
		let contactAddress = document.createElement("address");
		contactAddress.append(createH(4, "Référent·e n°" + nb++))

		// name (role)
		contactAddress.append(createP([contact.firstname, contact.lastname].join(" ") + " (" + contact.role + ")"));
		
		// email
		{
			let label = createP("Courriel : ");
			label.append(createA("mailto:" + contact.email, contact.email));
			contactAddress.append(label);
		}

		// phone
		let phone = contact.phoneCode + contact.phoneNum 
		
		let label = createP("Téléphone : ");
		label.append(createA("tel:" + phone, phone));
		contactAddress.append(label);


		// add infos to all contacts infos
		contactsAddress.append(contactAddress);
	}
	infos.append(contactsAddress);

	// Microscopes
	infos.append(createContentElement("h3", "Microscopes"));
	let microsList = document.createElement("ul");
	for (const micro of Object.values(group.microscopes)) {
		ctr = micro.controller;
		model = micro.model;
		brand = model.brand;
		compagny = brand.compagny;

		if(compagny.name == "Homemade")
			microName = "Homemade - " + ctr.name;
		else
			microName = [compagny.name, brand.name, model.name, ctr.name].join(" - ");

		if (micro.type == "LABO")
			microType = "laboratoire"
		else if (micro.type == "PLAT")
			microType = "plateforme"

		microsList.appendChild(createContentElement("li", microName + " (" + microType + ")"));
	}
	infos.append(microsList);

	// Keywords
	// merge all tags of all microscopes, by categories
	let allKeywords = {};
	for (const micro of Object.values(group.microscopes)) {
		for (const kw of micro.keywords) {
			let catName = kw.cat.name;
			if(!allKeywords[catName])
				allKeywords[catName] = [kw.tag];
			else
				allKeywords[catName].push(kw.tag);
		}
	}

	for (const cat in allKeywords) {
		allKeywords[cat] = [...new Set(allKeywords[cat])]
	}

	//display the keywords
	infos.append(createContentElement("h3", "Mots-clés"));
	let kwList = document.createElement("ul");

	let maxTags = 4;
	for (const cat in allKeywords) {
		if (Object.hasOwnProperty.call(allKeywords, cat)) {
			const tags = allKeywords[cat].slice(0, maxTags);
			catLi = createContentElement("li", cat + " : " + tags.join(", "));
			kwList.appendChild(catLi);
		}
	}
	infos.append(kwList);

	return infos.innerHTML;
}

/*Legend specific*/
let legend = L.control({ position: "bottomleft" });

legend.onAdd = function(map) {
  var div = L.DomUtil.create("div", "legend");
  div.innerHTML += "<h2>Légende</h2>";
  div.innerHTML += '<div class="legend-item"><div class="legend-icon"><svg><use class="marker lab-marker" href="#marker"/></svg></div><span>Laboratoire</span></div>';
  div.innerHTML += '<div class="legend-item"><div class="legend-icon"><svg><use class="marker plat-marker" href="#marker"/></svg></div><span>Plateforme</span></div>';
  div.innerHTML += '<div class="legend-item"><div class="legend-icon"><svg><use class="marker mix-marker" href="#marker"/></svg></div><span>Mixte</span></div>';
  
  

  return div;
};

legend.addTo(map);

// show the scale bar on the lower left corner
L.control.scale({imperial: true, metric: true}).addTo(map);

// MAP FILTERS

let filters = [];
initMapFilters();

function initMapFilters() {
	let mapFilters = document.getElementById("map-filters");

	if(mapFilters == null)
		return;

	document.getElementById("filters-reset").addEventListener("click", () => { filters = []; updateFilters(); });
	mapFilters.addEventListener("change", onFilterChange);
	mapFilters.addEventListener("click", onHeaderClick)
}

function onFilterChange(e) {
	let checkbox = e.target;
	if(checkbox.type != "checkbox")
		return;

	if(checkbox.checked) 
		filters.push(checkbox.value); // add filter
	else
		filters.splice(filters.indexOf(checkbox.value), 1); // remove filter

	updateFilters();
}

function onHeaderClick(e){
	let header = e.target;
	if(header.tagName != "H3")
		return;

	let checkboxes = header.parentElement.getElementsByTagName("input");

	let allChecked = true;
	for (const checkbox of checkboxes) {
		if(checkbox.checked == false) {
			allChecked = false;
			break;
		}
	}

	for (const checkbox of checkboxes)
		checkbox.checked = !allChecked;

	updateFilters();
}

function updateFilters() {
	let url = "/api/v1/search.php";

	if(filters.length > 0)
		url += "?filters[]=" + filters[0];

	filters.forEach((filter, index) => {
		if (index === 0) return;
		url += "&filters[]=" + filter;
	});

	loadAndShowGroups(url);
}

/* form auto fill */

if(page == "form.php" || page == "edit_micros_group.php") {
	map.on('click', fillCoordinates);
}

function fillCoordinates(e) {
	normalizedLatLng = normalizeLatLng(e.latlng, 5);
	let lat = normalizedLatLng.lat;
	let lon = normalizedLatLng.lng;

	if(lat < 41) lat = 41;
	if(lat > 52) lat = 52;
	if(lon < -6) lon = -6;
	if(lon > 11) lon = 11;

    document.getElementById("lat").value = lat;
    document.getElementById("lon").value = lon;
}