// initialize Leaflet
var map = L.map('map').setView([45.78209592175619, 4.872300243795213], 13);

// add the OpenStreetMap tile and display the license attribution
L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
	maxZoom: 20,
	attribution: '&copy; OpenStreetMap France | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// show a marker on the map
L.marker([45.78209592175619, 4.872300243795213]).bindPopup('INL').addTo(map);