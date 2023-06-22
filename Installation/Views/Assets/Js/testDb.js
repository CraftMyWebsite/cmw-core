const testDb = async () => {
  let address = document.getElementById("bdd_address").value
  let name = document.getElementById("bdd_name").value
  let login = document.getElementById("bdd_login").value
  let port = document.getElementById("bdd_port").value
  let pass = document.getElementById("bdd_pass").value

  let formData = {
    'bdd_address': address,
    'bdd_name': name,
    'bdd_login': login,
    'bdd_port': port,
    'bdd_pass': pass
  }

  let request = await fetch('installer/test/db', {
    method: 'POST',
    headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"},
    body: Object.entries(formData).map(([k, v]) => {
      return k + '=' + v
    }).join('&')
  })

  const response = await request.json();

  sendToaster(response['status'], response['content'])
}

function sendToaster(status, content) {
  iziToast.show(
      {
        message: content,
        color: status === 1 ? "green" : "red"
      });
}