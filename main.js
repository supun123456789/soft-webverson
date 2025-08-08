// Add extra model input fields dynamically
function addOutdoorModel() {
  const div = document.createElement('div');
  div.className = 'model-group';
  div.innerHTML = `
    <input type="text" name="outdoor_model[]" placeholder="Outdoor Model" required>
    <input type="number" name="outdoor_qty[]" placeholder="Qty" required>
    <input type="text" name="outdoor_serial[]" placeholder="Serial(s) (comma separated)" required>
  `;
  document.getElementById('outdoorModels').appendChild(div);
}

function addIndoorModel() {
  const div = document.createElement('div');
  div.className = 'model-group';
  div.innerHTML = `
    <input type="text" name="indoor_model[]" placeholder="Indoor Model" required>
    <input type="number" name="indoor_qty[]" placeholder="Qty" required>
    <input type="text" name="indoor_serial[]" placeholder="Serial(s) (comma separated)" required>
  `;
  document.getElementById('indoorModels').appendChild(div);
}

// Handle form submission
const form = document.getElementById('customerForm');
form.addEventListener('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(form);

  fetch('../api/save_customer.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      const box = document.getElementById('response');
      box.innerHTML = data.message;
      box.style.color = data.success ? 'green' : 'red';
      if (data.success) form.reset();
    })
    .catch(err => {
      document.getElementById('response').innerHTML = 'Failed to submit data.';
    });
});
