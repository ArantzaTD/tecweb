// JSON base de referencia
var baseJSON = {
  "precio": 0,
  "unidades": 1,
  "modelo": "XX-000",
  "marca": "NA",
  "detalles": "NA",
  "imagen": "img/default.png"
};

// ===== FUNCIONES DE RENDER =====
function renderSinResultados() {
  document.getElementById("productos").innerHTML =
    `<tr><td colspan="3" style="text-align:center;">Sin resultados</td></tr>`;
}

function renderFila(obj) {
  if (!obj || Object.keys(obj).length === 0) return renderSinResultados();
  const desc = `
    <li>precio: ${obj.precio ?? ""}</li>
    <li>unidades: ${obj.unidades ?? ""}</li>
    <li>modelo: ${obj.modelo ?? ""}</li>
    <li>marca: ${obj.marca ?? ""}</li>
    <li>detalles: ${obj.detalles ?? ""}</li>`;
  document.getElementById("productos").innerHTML = `
    <tr>
      <td>${obj.id ?? ""}</td>
      <td>${obj.nombre ?? ""}</td>
      <td><ul>${desc}</ul></td>
    </tr>`;
}

function renderLista(arr) {
  if (!Array.isArray(arr) || arr.length === 0) return renderSinResultados();
  let html = "";
  arr.forEach(p => {
    html += `
      <tr>
        <td>${p.id}</td>
        <td>${p.nombre}</td>
        <td>
          <ul>
            <li>precio: ${p.precio}</li>
            <li>unidades: ${p.unidades}</li>
            <li>modelo: ${p.modelo}</li>
            <li>marca: ${p.marca}</li>
            <li>detalles: ${p.detalles}</li>
          </ul>
        </td>
      </tr>`;
  });
  document.getElementById("productos").innerHTML = html;
}

// ===== BUSCAR POR ID =====
function buscarID(e) {
  if (e) e.preventDefault();
  const id = document.getElementById("search").value.trim();
  if (!id) return renderSinResultados();

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "./backend/read.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = () => {
    if (xhr.readyState !== 4) return;
    if (xhr.status !== 200) return renderSinResultados();
    let data; try { data = JSON.parse(xhr.responseText); } catch { return renderSinResultados(); }
    renderFila(data);
  };
  xhr.send("id=" + encodeURIComponent(id));
}

// ===== BUSCAR POR TEXTO =====
function buscarProducto(e) {
  if (e) e.preventDefault();
  const q = document.getElementById("search").value.trim();
  if (!q) return renderSinResultados();

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "./backend/read.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = () => {
    if (xhr.readyState !== 4) return;
    if (xhr.status !== 200) return renderSinResultados();
    let data; try { data = JSON.parse(xhr.responseText); } catch { return renderSinResultados(); }
    Array.isArray(data) ? renderLista(data) : renderFila(data);
  };
  xhr.send("q=" + encodeURIComponent(q));
}

// ===== AGREGAR PRODUCTO =====
function agregarProducto(e) {
  if (e) e.preventDefault();

  const producto = {
    nombre: document.getElementById("name").value.trim(),
    precio: parseFloat(document.getElementById("precio").value),
    unidades: parseInt(document.getElementById("unidades").value),
    modelo: document.getElementById("modelo").value.trim(),
    marca: document.getElementById("marca").value.trim(),
    detalles: document.getElementById("detalles").value.trim(),
    imagen: document.getElementById("imagen").value.trim()
  };

  // Validaciones básicas
  if (producto.nombre === "" || producto.marca === "") {
    window.alert("⚠️ Debes llenar los campos 'nombre' y 'marca'.");
    return;
  }
  if (isNaN(producto.precio) || producto.precio < 0) {
    window.alert("⚠️ El precio debe ser un número mayor o igual a 0.");
    return;
  }
  if (isNaN(producto.unidades) || producto.unidades < 0) {
    window.alert("⚠️ Las unidades deben ser un número mayor o igual a 0.");
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "./backend/create.php", true);
  xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
  xhr.onreadystatechange = () => {
    if (xhr.readyState !== 4) return;
    if (xhr.status !== 200) {
      window.alert("❌ Error al comunicarse con el servidor.");
      return;
    }

    try {
      const resp = JSON.parse(xhr.responseText);
      window.alert(resp.msg || "Respuesta desconocida.");
    } catch {
      window.alert("⚠️ Respuesta no válida del servidor.");
    }
  };
  xhr.send(JSON.stringify(producto));
}

// ===== INICIALIZAR =====
function init() {
  document.getElementById("description").value = JSON.stringify(baseJSON, null, 2);
  renderSinResultados();
}
