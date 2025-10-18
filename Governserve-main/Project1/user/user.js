
var map = L.map('map').setView([14.6697314, 120.5414944], 13); 

  
  L.tileLayer('https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=Nhfgp9DG2mD6AaAhXqhW', {
    attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
  }).addTo(map);

  var marker;
  var bataanGeoJSON;

  fetch('../data/Province.geojson')
  .then(response => response.json())
  .then(data => {
    bataanGeoJSON = data;

    var bataanLayer = L.geoJSON(data, {
      style: {
        color: '#3d86fc',
        weight: 10,
        fillColor: 'transparent',
        fillOpacity: 0.3
      }
    }).addTo(map);

    
    map.fitBounds(bataanLayer.getBounds());
  })
  .catch(err => console.error('Could not load GeoJSON:', err));

  
  map.on('click', function(e) {
    if (!bataanGeoJSON) {
    alert("Bataan boundary not loaded yet. Please wait a moment.");
    return;
  }

  var point = turf.point([e.latlng.lng, e.latlng.lat]);
  var inside = turf.booleanPointInPolygon(point, bataanGeoJSON.features[0]);

  if (inside) {
  var lat = e.latlng.lat.toFixed(6);
  var lng = e.latlng.lng.toFixed(6);

  if (marker) {
    map.removeLayer(marker);
  }

  marker = L.marker([lat, lng]).addTo(map);


  document.getElementById('location').value = lat + ", " + lng;


  fetch(`https://corsproxy.io/?https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
    .then(response => response.json())
    .then(data => {
      if (data && data.display_name) {
        const addressField = document.getElementById('address');
        if (addressField) {
          addressField.value = data.display_name;
        } 
      } else {
        alert('Address not found for this location.');
      }
    })
    .catch(error => {
      console.error('Error fetching address:', error);
    });
} else {
  alert("Please select a location inside Bataan province only.");
}

  });
       
  
// Sidebar toggle
function showSection(sectionId, event) {
  document.getElementById('account').style.display = 'none';
  document.getElementById('security').style.display = 'none';
  document.getElementById('notifications').style.display = 'none';

  if (sectionId === 'account') {
    document.getElementById('account').style.display = 'block';
    document.getElementById('security').style.display = 'block';
  } else {
    document.getElementById('notifications').style.display = 'block';
  }

  document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
  event.target.classList.add('active');
}

document.getElementById('uploadInput').addEventListener('change', function (e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (event) {
      document.getElementById('profileImage').src = event.target.result;
    };
    reader.readAsDataURL(file);
  }
});

function removeImage() {
  document.getElementById('profileImage').src = "https://cdn-icons-png.flaticon.com/128/9131/9131646.png";
  document.getElementById('uploadInput').value = "";
}


//modal
document.getElementById("btn").addEventListener("click", function(){
    document.getElementsByClassName("popup")
    [0].classList.add("active");
});

document.getElementById("verify-popup-btn").addEventListener("click", function(){
    document.getElementsByClassName("popup")
    [0].classList.remove("active")
});


// Preview new image when selected
document.getElementById('uploadInput').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(ev) {
      document.getElementById('profileImage').src = ev.target.result;
    };
    reader.readAsDataURL(file);
  }
});

// Handle save profile
document.getElementById('profileForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('save_profile.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      alert('Profile saved successfully!');
      if (data.image) {
        document.getElementById('profileImage').src = data.image;
      }
    } else {
      alert('Error: ' + data.message);
    }
  });
});

// Remove image
function removeImage() {
  document.getElementById('profileImage').src = "https://cdn-icons-png.flaticon.com/128/9131/9131646.png";
  document.getElementById('uploadInput').value = "";
}
