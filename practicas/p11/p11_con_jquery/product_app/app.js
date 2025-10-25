// JSON BASE A MOSTRAR EN FORMULARIO
var baseJSON = {
    "precio": 0.0,
    "unidades": 1,
    "modelo": "XX-000",
    "marca": "NA",
    "detalles": "NA",
    "imagen": "img/default.png"
};

// Variable global para almacenar los productos listados
var globalProductos = [];

/**
 * Bloque de inicialización de jQuery.
 * Se ejecuta una vez que el DOM está completamente cargado.
 */
$(document).ready(function() {
    init();
    $('#search-form').on('submit', buscarProducto); 
    $('#product-form').on('submit', agregarProducto);

    // Listeners delegados para botones en la tabla
    $("#products").on('click', '.product-delete', eliminarProducto);
    $("#products").on('click', '.product-edit', editarProducto);

    // Listener para cancelar edición
    $('#product-form').on('click', '#cancel-edit', resetForm);

    // Búsqueda en tiempo real
    $('#search').on('input', function() {
        buscarProducto();
    });
});

// FUNCIÓN CALLBACK AL CARGAR LA PÁGINA
function init() {
    resetForm();
    listarProductos();
}

// FUNCIÓN PARA LISTAR PRODUCTOS
function listarProductos() {
    $.ajax({
        url: './backend/product-list.php',
        type: 'GET',
        dataType: 'json',
        success: function(productos) {
            if (productos && Object.keys(productos).length > 0) {
                globalProductos = productos;
                let template = '';

                $.each(productos, function(index, producto) {
                    let descripcion = `
                        <li>precio: ${producto.precio}</li>
                        <li>unidades: ${producto.unidades}</li>
                        <li>modelo: ${producto.modelo}</li>
                        <li>marca: ${producto.marca}</li>
                        <li>detalles: ${producto.detalles}</li>
                    `;

                    // 🔁 ORDEN CAMBIADO: primero Eliminar (rojo), luego Editar (ámbar)
                    template += `
                        <tr productId="${producto.id}">
                            <td>${producto.id}</td>
                            <td>${producto.nombre}</td>
                            <td><ul>${descripcion}</ul></td>
                            <td class="text-right">
                                <button class="product-delete btn btn-danger btn-sm mr-2">Eliminar</button>
                                <button class="product-edit btn btn-warning btn-sm">Editar</button>
                            </td>
                        </tr>`;
                });
                $("#products").html(template);
            } else {
                $("#products").html('<tr><td colspan="4">No hay productos para mostrar.</td></tr>');
                globalProductos = [];
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error al listar productos: " + textStatus, errorThrown);
        }
    });
}

// FUNCIÓN PARA BUSCAR PRODUCTOS
function buscarProducto(e) {
    if (e) e.preventDefault();

    var search = $('#search').val();
    if (!search.trim()) {
        $("#product-result").attr("class", "card my-4 d-none");
        listarProductos();
        return;
    }

    $.ajax({
        url: './backend/product-search.php',
        type: 'GET',
        data: { search: search },
        dataType: 'json',
        success: function(productos) {
            if (productos && Object.keys(productos).length > 0) {
                let template = '';
                let template_bar = '';

                $.each(productos, function(index, producto) {
                    let descripcion = `
                        <li>precio: ${producto.precio}</li>
                        <li>unidades: ${producto.unidades}</li>
                        <li>modelo: ${producto.modelo}</li>
                        <li>marca: ${producto.marca}</li>
                        <li>detalles: ${producto.detalles}</li>
                    `;

                    // 🔁 ORDEN CAMBIADO aquí también
                    template += `
                        <tr productId="${producto.id}">
                            <td>${producto.id}</td>
                            <td>${producto.nombre}</td>
                            <td><ul>${descripcion}</ul></td>
                            <td class="text-right">
                                <button class="product-delete btn btn-danger btn-sm mr-2">Eliminar</button>
                                <button class="product-edit btn btn-warning btn-sm">Editar</button>
                            </td>
                        </tr>`;
                    template_bar += `<li>${producto.nombre}</li>`;
                });

                $("#product-result").attr("class", "card my-4 d-block");
                $("#container").html(template_bar);
                $("#products").html(template);
            } else {
                $("#products").html('<tr><td colspan="4">No se encontraron productos.</td></tr>');
                $("#product-result").attr("class", "card my-4 d-block");
                $("#container").html('<li>No se encontraron resultados para la búsqueda.</li>');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error al buscar productos: " + textStatus, errorThrown);
        }
    });
}

// FUNCIÓN PARA AGREGAR O EDITAR PRODUCTOS
function agregarProducto(e) {
    e.preventDefault();

    let id = $('#productId').val();
    let isEdit = id ? true : false;
    let url = isEdit ? './backend/product-edit.php' : './backend/product-add.php';

    let productoJsonString = $('#description').val();
    let finalJSON;

    try {
        finalJSON = JSON.parse(productoJsonString);
    } catch (error) {
        $("#product-result").attr("class", "card my-4 d-block");
        $("#container").html('<li style="list-style:none;">Error: JSON inválido.</li>');
        return;
    }

    finalJSON['nombre'] = $('#name').val();
    if (isEdit) finalJSON['id'] = id;

    // Validaciones
    let errores = [];
    if (!finalJSON['nombre'] || finalJSON['nombre'].trim() === "") {
        errores.push("El nombre es requerido.");
    } else if (finalJSON['nombre'].length > 100) {
        errores.push("El nombre debe tener 100 caracteres o menos.");
    }

  


    if (!finalJSON['modelo'] || finalJSON['modelo'].trim() === "") {
        errores.push("El modelo es requerido.");
    } else if (!/^[a-zA-Z0-9\-]+$/.test(finalJSON['modelo'])) {
        errores.push("El modelo debe ser alfanumérico.");
    } else if (finalJSON['modelo'].length > 25) {
        errores.push("El modelo debe tener 25 caracteres o menos.");
    }

    if (finalJSON['precio'] === undefined || finalJSON['precio'] === "" || isNaN(finalJSON['precio']) || Number(finalJSON['precio']) <= 99.99) {
        errores.push("El precio debe ser mayor a 99.99.");
    }

    if (finalJSON['unidades'] === undefined || isNaN(finalJSON['unidades']) || Number(finalJSON['unidades']) < 0) {
        errores.push("Las unidades deben ser un número válido.");
    }

    if (finalJSON['detalles'] && finalJSON['detalles'].length > 250) {
        errores.push("Los detalles deben tener 250 caracteres o menos.");
    }

    if (!finalJSON['imagen'] || finalJSON['imagen'].trim() === "") {
        finalJSON['imagen'] = "img/default.png";
    }

    if (errores.length > 0) {
        let template_bar = errores.map(e => `<li style="list-style:none;">${e}</li>`).join('');
        $("#product-result").attr("class", "card my-4 d-block");
        $("#container").html(template_bar);
        return;
    }

  $.ajax({
      url: url,
      type: 'POST',
      data: { json: JSON.stringify(finalJSON) }, // ✅ Enviamos como campo POST
      dataType: 'json',
      success: function(respuesta) {
          let template_bar = `
              <li style="list-style:none;">status: ${respuesta.status}</li>
              <li style="list-style:none;">message: ${respuesta.message}</li>
          `;
          $("#product-result").attr("class", "card my-4 d-block");
          $("#container").html(template_bar);
          resetForm();
          listarProductos();
      },
      error: function(jqXHR, textStatus, errorThrown) {
          console.error("Error al agregar/editar producto: " + textStatus, errorThrown);
      }
  });

}

// FUNCIÓN PARA ELIMINAR PRODUCTOS
function eliminarProducto(e) {
    e.preventDefault();
    if (confirm("¿Deseas eliminar este producto?")) {
        var id = $(this).closest('tr').attr("productId");
        $.ajax({
            url: './backend/product-delete.php',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(respuesta) {
                let template_bar = `
                    <li style="list-style:none;">status: ${respuesta.status}</li>
                    <li style="list-style:none;">message: ${respuesta.message}</li>
                `;
                $("#product-result").attr("class", "card my-4 d-block");
                $("#container").html(template_bar);
                listarProductos();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error al eliminar producto: " + textStatus, errorThrown);
            }
        });
    }
}

// FUNCIÓN PARA EDITAR PRODUCTOS
function editarProducto(e) {
    e.preventDefault();
    let id = $(this).closest('tr').attr("productId");
    let producto = globalProductos.find(p => p.id == id);

    if (producto) {
        $('#productId').val(producto.id);
        $('#name').val(producto.nombre);
        $('#description').val(JSON.stringify({
            "precio": parseFloat(producto.precio),
            "unidades": parseInt(producto.unidades),
            "modelo": producto.modelo,
            "marca": producto.marca,
            "detalles": producto.detalles,
            "imagen": producto.imagen
        }, null, 2));

        $('#product-form button[type="submit"]').text('Modificar Producto');
        if ($('#cancel-edit').length === 0) {
            $('#product-form button[type="submit"]').after('<button type="button" id="cancel-edit" class="btn btn-secondary btn-block mt-2">Cancelar Edición</button>');
        }
        window.scrollTo(0, 0);
    }
}

// FUNCIÓN PARA RESETEAR FORMULARIO
function resetForm() {
    $('#product-form').trigger('reset');
    $('#productId').val('');
    $("#description").val(JSON.stringify(baseJSON, null, 2));
    $('#product-form button[type="submit"]').text('Agregar Producto');
    $('#cancel-edit').remove();
}
