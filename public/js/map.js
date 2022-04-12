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
	const response = await fetch("/api/v1/microscopes.php");
	const microscopes = await response.json();

	for (let microscope of microscopes) {
		let coor = microscope.coor;
		L.marker([coor.lat, coor.lon]).bindPopup(microscope.ref).addTo(map);
	}
}