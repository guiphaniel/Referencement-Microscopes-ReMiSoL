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
loadAndShowGroups("/api/v1/listMicroscopesGroups.php");

async function loadAndShowGroups(url) {
	markersClusters.clearLayers();

	const response = await fetch(url);
	const groups = await response.json();

	for (let group of groups) {
		// set custom icon color
		let color;
		let first = true;
		for (let micro of Object.values(group.microscopes)) {
			if(first) {
				color = micro.type == "LABO" ? "blue" : "red";
				first = false;
			} else {
				if(micro.type == "LABO" ? "blue" : "red" != color) {
					color = "orange";
					break;
				}
			}
		}

		let customIcon = new L.Icon({
			iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
			shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
			iconSize: [25, 41],
			iconAnchor: [12, 41],
			popupAnchor: [1, -34],
			shadowSize: [41, 41]
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

		// zoom on the marker if wer're on it's group-details page
		if(page == "group-details.php" && group.id == window.location.search.split("=").pop()) {
			map.setView(group.coor, 13);
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
	infos.append(createH(2, group.lab.name + " (" + group.lab.type + group.lab.code + ")"));

	// website
	{
		let lab = group.lab;
		let label = createP("Site internet : ");
		label.append(createA(lab.website, lab.website, "_blank"));
		infos.append(label);
	}

	// contacts
	infos.append(createContentElement("h3", "Référents"))
	let contactsAddress = document.createElement("address");
	for (const contact of group.contacts) {
		// generate contact infos
		let contactAddress = document.createElement("address");

		// role
		contactAddress.append(createP(contact.role));

		// name
		contactAddress.append(createP([contact.firstname, contact.lastname].join(" ")));
		
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
  div.innerHTML += '<div class="legend-item"><img class="legend-icon" src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png" alt="Marqueur mixte"></img><span>Laboratoire</span><br></div>';
  div.innerHTML += '<div class="legend-item"><img class="legend-icon" src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png" alt="Marqueur mixte"></img><span>Plateforme</span><br></div>';
  div.innerHTML += '<div class="legend-item"><img class="legend-icon" src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png" alt="Marqueur mixte"></img><span>Mixte</span><br></div>';
  
  

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

	document.getElementById("filters-reinit").addEventListener("click", () => filters = []);
	document.addEventListener("change", updateFilters);
}

function updateFilters(e) {
	let checkbox = e.target;
	if(checkbox.type != "checkbox")
		return;

	if(checkbox.checked)
		filters.push(checkbox.value);
	else
		filters.splice(filters.indexOf(checkbox.value), 1);

	let url = "/api/v1/search.php";

	if(filters.length > 0)
		url += "?filters[]=" + filters[0];

	filters.forEach((filter, index) => {
		if (index === 0) return;
		url += "&filters[]=" + filter;
	});

	loadAndShowGroups(url);
}
