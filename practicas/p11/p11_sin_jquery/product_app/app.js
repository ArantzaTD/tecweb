// JSON BASE A MOSTRAR EN FORMULARIO
var baseJSON = {
  "precio": 0.0,
  "unidades": 1,
  "modelo": "XX-000",
  "marca": "NA",
  "detalles": "NA",
  "imagen": "img/default.png"
};

function init() {
  var JsonString = JSON.stringify(baseJSON, null, 2);
  document.getElementById("description").value = JsonString;
  listarProductos();
}

function listarProductos() {
  var client = getXMLHttpRequest();
  client.open('GET', './backend/product-list.php', true);
  client.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  client.onreadystatechange = function () {
    if (client.readyState == 4 && client.status == 200) {
      let productos = JSON.parse(client.responseText);
      if (Object.keys(productos).length > 0) {
        let template = '';
        productos.forEach(producto => {
          let descripcion = '';
          descripcion += '<li>precio: ' + producto.precio + '</li>';
          descripcion += '<li>unidades: ' + producto.unidades + '</li>';
          descripcion += '<li>modelo: ' + producto.modelo + '</li>';
          descripcion += '<li>marca: ' + producto.marca + '</li>';
          descripcion += '<li>detalles: ' + producto.detalles + '</li>';

          template += `
            <tr productId="${producto.id}">
              <td>${producto.id}</td>
              <td>${producto.nombre}</td>
              <td><ul>${descripcion}</ul></td>
              <td>
                <button class="product-delete btn btn-danger" onclick="eliminarProducto()">
                  Eliminar
                </button>
              </td>
            </tr>
          `;
        });
        document.getElementById("products").innerHTML = template;
      }
    }
  };
  client.send();
}

function buscarProducto(e) {
  e.preventDefault();
  var search = document.getElementById('search').value;
  var client = getXMLHttpRequest();
  client.open('GET', './backend/product-search.php?search=' + search, true);
  client.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  client.onreadystatechange = function () {
    if (client.readyState == 4 && client.status == 200) {
      let productos = JSON.parse(client.responseText);
      if (Object.keys(productos).length > 0) {
        let template = '';
        let template_bar = '';

        productos.forEach(producto => {
          let descripcion = '';
          descripcion += '<li>precio: ' + producto.precio + '</li>';
          descripcion += '<li>unidades: ' + producto.unidades + '</li>';
          descripcion += '<li>modelo: ' + producto.modelo + '</li>';
          descripcion += '<li>marca: ' + producto.marca + '</li>';
          descripcion += '<li>detalles: ' + producto.detalles + '</li>';

          template += `
            <tr productId="${producto.id}">
              <td>${producto.id}</td>
              <td>${producto.nombre}</td>
              <td><ul>${descripcion}</ul></td>
              <td>
                <button class="product-delete btn btn-danger" onclick="eliminarProducto()">
                  Eliminar
                </button>
              </td>
            </tr>
          `;

          template_bar += `<li>${producto.nombre}</li>`;
        });

        document.getElementById("product-result").className = "card my-4 d-block";
        document.getElementById("container").innerHTML = template_bar;
        document.getElementById("products").innerHTML = template;
      }
    }
  };
  client.send();
}

function agregarProducto(e) {
  e.preventDefault();

  var productoJsonString = document.getElementById('description').value;
  var finalJSON = JSON.parse(productoJsonString);
  finalJSON['nombre'] = document.getElementById('name').value;
  productoJsonString = JSON.stringify(finalJSON, null, 2);

  // 🔍 VALIDACIONES IMPLEMENTADAS
  let errores = [];

  if (!finalJSON['nombre'] || finalJSON['nombre'].trim() === "") {
    errores.push("El nombre es requerido.");
  } else if (finalJSON['nombre'].length > 100) {
    errores.push("El nombre debe tener 100 caracteres o menos.");
  }

  const marcasValidas = ["Sony", "Samsung", "LG", "Panasonic", "NA"];
  if (!finalJSON['marca'] || !marcasValidas.includes(finalJSON['marca'])) {
    errores.push("La marca es requerida y debe ser una opción válida.");
  }

  if (!finalJSON['modelo'] || finalJSON['modelo'].trim() === "") {
    errores.push("El modelo es requerido.");
  } else if (!/^[a-zA-Z0-9\\-]+$/.test(finalJSON['modelo'])) {
    errores.push("El modelo debe ser alfanumérico.");
  } else if (finalJSON['modelo'].length > 25) {
    errores.push("El modelo debe tener 25 caracteres o menos.");
  }

  if (finalJSON['precio'] === undefined || finalJSON['precio'] === null || finalJSON['precio'] === "") {
    errores.push("El precio es requerido.");
  } else if (isNaN(finalJSON['precio']) || Number(finalJSON['precio']) <= 99.99) {
    errores.push("El precio debe ser mayor a 99.99.");
  }

  if (finalJSON['detalles'] && finalJSON['detalles'].length > 250) {
    errores.push("Los detalles deben tener 250 caracteres o menos.");
  }

  if (finalJSON['unidades'] === undefined || finalJSON['unidades'] === null || finalJSON['unidades'] === "") {
    errores.push("Las unidades son requeridas.");
  } else if (isNaN(finalJSON['unidades']) || Number(finalJSON['unidades']) < 0) {
    errores.push("Las unidades deben ser un número mayor o igual a 0.");
  }

  if (!finalJSON['imagen'] || finalJSON['imagen'].trim() === "") {
    finalJSON['imagen'] = "img/default.png";
  }

  if (errores.length > 0) {
    let template_bar = errores.map(e => `<li style="list-style: none;">${e}</li>`).join('');
    document.getElementById("product-result").className = "card my-4 d-block";
    document.getElementById("container").innerHTML = template_bar;
    return; // Detener envío si hay errores
  }

  // Si no hay errores, se envía el producto
  var client = getXMLHttpRequest();
  client.open('POST', './backend/product-add.php', true);
  client.setRequestHeader('Content-Type', "application/json;charset=UTF-8");
  client.onreadystatechange = function () {
    if (client.readyState == 4 && client.status == 200) {
      let respuesta = JSON.parse(client.responseText);
      let template_bar = `
        <li style="list-style: none;">status: ${respuesta.status}</li>
        <li style="list-style: none;">message: ${respuesta.message}</li>
      `;
      document.getElementById("product-result").className = "card my-4 d-block";
      document.getElementById("container").innerHTML = template_bar;
      listarProductos();
    }
  };
  client.send(JSON.stringify(finalJSON));
}

function eliminarProducto() {
  if (confirm("De verdad deseas eliminar el Producto")) {
    var id = event.target.parentElement.parentElement.getAttribute("productId");
    var client = getXMLHttpRequest();
    client.open('GET', './backend/product-delete.php?id=' + id, true);
    client.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    client.onreadystatechange = function () {
      if (client.readyState == 4 && client.status == 200) {
        let respuesta = JSON.parse(client.responseText);
        let template_bar = `
          <li style="list-style: none;">status: ${respuesta.status}</li>
          <li style="list-style: none;">message: ${respuesta.message}</li>
        `;
        document.getElementById("product-result").className = "card my-4 d-block";
        document.getElementById("container").innerHTML = template_bar;
        listarProductos();
      }
    };
    client.send();
  }
}

function getXMLHttpRequest() {
  var objetoAjax;
  try {
    objetoAjax = new XMLHttpRequest();
  } catch (err1) {
    try {
      objetoAjax = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (err2) {
      try {
        objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (err3) {
        objetoAjax = false;
      }
    }
  }
  return objetoAjax;
}
