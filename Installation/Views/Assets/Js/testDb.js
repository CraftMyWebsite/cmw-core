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
    'bdd_pass': btoa(pass)
  }

  let request = await fetch('installer/test/db', {
    method: 'POST',
    headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"},
    body: Object.entries(formData).map(([k, v]) => {
      return k + '=' + v
    }).join('&')
  })

  const response = await request.json();

  sendToaster(response['status'], response['content'], response['title'])
}

function sendToaster(status, content, title) {
  iziToast.show(
      {
          message: content,
          theme: 'dark',
          backgroundColor: status === 0 ? 'rgba(255, 92, 92, 0.12)' : 'rgba(76, 175, 80, 0.12)',
          color: '#ffffff',
          icon: 'fa-solid fa-check',
          iconColor: status === 0 ? '#ff5c5c' : '#4caf50',
          titleSize: '17',
          messageSize: '14',
          titleColor: '#ffffff',
          messageColor: '#d1d1d1',
          progressBarColor: status === 0 ? '#ff5c5c' : '#4caf50',
          close: true,
          pauseOnHover: true,
          position: 'topRight',
          timeout: 6000,
          animateInside: true,
          transitionIn: 'fadeInDown',
          transitionOut: 'fadeOutUp',
          class: 'iziToast-dark-success',
          layout: 2,
      });
}