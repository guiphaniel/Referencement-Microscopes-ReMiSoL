// get page name
let path = window.location.pathname;
let page = path.split("/").pop();

// initialize Leaflet
var map = L.map('map').setView([46.606111, 1.875278], 5);

// add the OpenStreetMap tile and display the license attribution
L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
	maxZoom: 20,
	attribution: '&copy; OpenStreetMap France | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// show the scale bar on the lower left corner
L.control.scale({imperial: true, metric: true}).addTo(map);

// show microscopes' markers on the map
loadAndShowMicroscopes();

async function loadAndShowMicroscopes() {
	const response = await fetch("/api/v1/listMicroscopesGroups.php");
	const groups = await response.json();

	for (let group of groups) {
		let marker = L.marker(group.coor, { "alt": group.lab.name });
		marker.bindPopup(group.lab.name);

		marker.on('mouseover',function(event) {
			event.target.openPopup();
		});

		marker.on('click',function(event) {
			window.location.replace("/group-details.php?id=" + group.id);
		});
		
		marker.addTo(map);

		// zoom on the marker if wer're on it's group-details page
		if(page == "group-details.php" && group.id == window.location.search.split("=").pop()) {
			map.setView(group.coor, 13);
		}
	}
}
