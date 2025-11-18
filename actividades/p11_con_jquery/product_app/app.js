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

    // Listener para el botón de cancelar edición
    $('#product-form').on('click', '#cancel-edit', resetForm);

    // Búsqueda en tiempo real al teclear
    $('#search').on('input', function() {
        buscarProducto();
    });

    // NUEVO: Validación en tiempo real al escribir (focus)
    $(document).on('focus', '#name, #precio, #unidades, #modelo, #marca, #detalles, #imagen', function () {
        const campo = $(this).attr('id');
        mostrarBarraEstado(campo, 'info', 'Editando campo...');
    });

    // Validación al perder el foco (MODIFICADO)
    $(document).on('blur', '#name, #precio, #unidades, #modelo, #marca, #detalles, #imagen', function () {
        const campo = $(this).attr('id');
        const valor = $(this).val().trim();
        let mensaje = "";

        switch (campo) {
          case 'name':
            if (!valor) mensaje = "El nombre es requerido.";
            else if (valor.length > 100) mensaje = "El nombre debe tener 100 caracteres o menos.";
            break;
          case 'precio':
            if (!valor) mensaje = "El precio es requerido.";
            else if (isNaN(valor) || Number(valor) <= 99.99) mensaje = "El precio debe ser mayor a 99.99.";
            break;
          case 'unidades':
            if (!valor) mensaje = "Las unidades son requeridas.";
            else if (isNaN(valor) || Number(valor) < 0) mensaje = "Las unidades deben ser un número mayor o igual a 0.";
            break;
          case 'modelo':
            if (!valor) mensaje = "El modelo es requerido.";
            else if (!/^[a-zA-Z0-9\-]+$/.test(valor)) mensaje = "El modelo debe ser alfanumérico.";
            else if (valor.length > 25) mensaje = "El modelo debe tener 25 caracteres o menos.";
            break;
          case 'marca':
            const marcasValidas = ["Sony", "Samsung", "LG", "Panasonic", "NA"];
            if (!valor || !marcasValidas.includes(valor)) mensaje = "Selecciona una marca válida.";
            break;
          case 'detalles':
            if (valor.length > 250) mensaje = "Los detalles deben tener 250 caracteres o menos.";
            break;
          case 'imagen':
            if (!valor) mensaje = "La ruta de imagen es requerida (usa img/default.png si no tienes una).";
            break;
        }

        if (mensaje) {
          $(this).addClass('is-invalid');
          mostrarErrorCampo($(this), mensaje);
          mostrarBarraEstado(campo, 'error', + mensaje);
        } else {
          $(this).removeClass('is-invalid');
          limpiarErrorCampo($(this));
          let mensajeExito = "bien " + campo.charAt(0).toUpperCase() + campo.slice(1) + " válido";
          mostrarBarraEstado(campo, 'success', mensajeExito);
        }
    });

    // NUEVO: Validación en tiempo real mientras escribe
    $(document).on('input', '#name, #precio, #unidades, #modelo, #marca, #detalles, #imagen', function () {
        const campo = $(this).attr('id');
        const valor = $(this).val().trim();
        validarCampoEnTiempoReal(campo, valor);
    });

    // VALIDACIÓN ASÍNCRONA: verificar si el nombre ya existe en la BD
$(document).on('input', '#name', function () {
    let nombre = $(this).val().trim();

    // Si está vacío, solo mensaje informativo
    if (nombre.length === 0) {
        mostrarBarraEstado('name', 'info', 'Escribiendo nombre...');
        $('#name').removeClass('is-invalid');
        return;
    }

    $.ajax({
        url: './backend/product-search.php',
        type: 'GET',
        data: { search: nombre },
        dataType: 'json',
        success: function (productos) {

            // Solo marcar como repetido si NO estás editando o si el nombre es de otro producto
            let idActual = $('#productId').val();
            let nombreExiste = false;

            if (productos && productos.length > 0) {
                productos.forEach(p => {
                    if (p.nombre.toLowerCase() === nombre.toLowerCase() && p.id != idActual) {
                        nombreExiste = true;
                    }
                });
            }

            if (nombreExiste) {
                mostrarBarraEstado('name', 'error', '⚠️ El nombre ya existe en la base de datos.');
                $('#name').addClass('is-invalid');
            } else {
                mostrarBarraEstado('name', 'success', '✔️ Nombre disponible.');
                $('#name').removeClass('is-invalid');
            }
        },
        error: function (err) {
            console.error("Error comprobando nombre:", err);
        }
    });
});

 
});

// NUEVA FUNCIÓN: Validación en tiempo real (mientras escribe)
function validarCampoEnTiempoReal(campo, valor) {
    let mensaje = "";
    let tipo = 'info';

    switch (campo) {
        case 'name':
            if (!valor) {
                mensaje = "Escribiendo nombre...";
            } else if (valor.length > 100) {
                mensaje = "Nombre demasiado largo (" + valor.length + "/100)";
                tipo = 'warning';
            } else {
                mensaje = "Nombre: " + valor.length + "/100 caracteres";
            }
            break;
        case 'precio':
            if (!valor) {
                mensaje = "Escribiendo precio...";
            } else if (isNaN(valor)) {
                mensaje = "Debe ser un número";
                tipo = 'warning';
            } else if (Number(valor) <= 99.99) {
                mensaje = "Debe ser mayor a 99.99 (actual: " + valor + ")";
                tipo = 'warning';
            } else {
                mensaje = "Precio: $" + valor;
            }
            break;
        case 'unidades':
            if (!valor) {
                mensaje = "Escribiendo unidades...";
            } else if (isNaN(valor)) {
                mensaje = "Debe ser un número";
                tipo = 'warning';
            } else {
                mensaje = "Unidades: " + valor;
            }
            break;
        case 'modelo':
            if (!valor) {
                mensaje = "Escribiendo modelo...";
            } else if (!/^[a-zA-Z0-9\-]+$/.test(valor)) {
                mensaje = "Solo letras, números y guiones";
                tipo = 'warning';
            } else if (valor.length > 25) {
                mensaje = "Modelo demasiado largo (" + valor.length + "/25)";
                tipo = 'warning';
            } else {
                mensaje = "Modelo: " + valor + " (" + valor.length + "/25)";
            }
            break;
        case 'marca':
            if (!valor || valor === "NA") {
                mensaje = "Selecciona una marca...";
            } else {
                mensaje = "Marca: " + valor;
            }
            break;
        case 'detalles':
            if (valor.length > 250) {
                mensaje = "Detalles demasiado largos (" + valor.length + "/250)";
                tipo = 'warning';
            } else {
                mensaje = "Detalles: " + valor.length + "/250 caracteres";
            }
            break;
        case 'imagen':
            if (!valor) {
                mensaje = "Escribiendo ruta de imagen...";
            } else {
                mensaje = "Imagen: " + valor;
            }
            break;
    }

    function mostrarBarraEstado(campo, tipo, mensaje) {
    let statusBar = $('#status-global');
    statusBar.removeClass('status-info status-success status-error status-warning');
    statusBar.addClass('status-' + tipo);
    statusBar.html(`Campo: ${campo} → ${mensaje}`).fadeIn(200);
}

}

// NUEVA FUNCIÓN: Mostrar barra de estado para un campo
function mostrarBarraEstado(campo, tipo, mensaje) {
    let statusBar = $('#status-' + campo);
    
    if (statusBar.length === 0) {
        $('#' + campo).after('<div id="status-' + campo + '" class="validation-status"></div>');
        statusBar = $('#status-' + campo);
    }

    statusBar.removeClass('status-info status-success status-error status-warning');
    statusBar.addClass('status-' + tipo);
    statusBar.html(mensaje).fadeIn(200);
}

// FUNCIÓN CALLBACK AL CARGAR LA PÁGINA
function init() {
    resetForm();
    listarProductos();
}

// FUNCIÓN CALLBACK AL CARGAR LA PÁGINA O AL AGREGAR UN PRODUCTO
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
                            <td class="text-right">
                                <button class="product-edit btn btn-info btn-sm mr-2">
                                    Editar
                                </button>
                                <button class="product-delete btn btn-danger btn-sm">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $("#products").html(template);
            } else {
                globalProductos = [];
                $("#products").html('<tr><td colspan="4">No hay productos para mostrar.</td></tr>');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error al listar productos: " + textStatus, errorThrown);
        }
    });
}

// FUNCIÓN CALLBACK DE FORMULARIO "Buscar"
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
        data: {
            search: search
        }, 
        dataType: 'json',
        success: function(productos) {
            if (productos && Object.keys(productos).length > 0) {
                let template = '';
                let template_bar = '';

                $.each(productos, function(index, producto) {
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
                            <td class="text-right">
                                <button class="product-edit btn btn-info btn-sm mr-2">
                                    Editar
                                </button>
                                <button class="product-delete btn btn-danger btn-sm">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    `;

                    template_bar += `
                        <li>${producto.nombre}</li>
                    `;
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

// FUNCIÓN CALLBACK DE BOTÓN "Agregar Producto" O "MODIFICAR PRODUCTO"
function agregarProducto(e) {
    e.preventDefault();

    let id = $('#productId').val();
    let isEdit = id ? true : false;
    let url = isEdit ? './backend/product-edit.php' : './backend/product-add.php';
    
    let errores = [];
    let campos = ['#name', '#precio', '#unidades', '#modelo', '#marca', '#imagen'];

    campos.forEach(selector => {
        let valor = $(selector).val().trim();
        if (!valor) {
            errores.push(`El campo ${selector.replace('#', '')} es requerido.`);
            $(selector).addClass('is-invalid');
            mostrarErrorCampo($(selector), `El campo ${selector.replace('#', '')} es requerido.`);
            mostrarBarraEstado(selector.replace('#', ''), 'error', `El campo ${selector.replace('#', '')} es requerido.`);
        } else {
            $(selector).removeClass('is-invalid');
            limpiarErrorCampo($(selector));
        }
    });

   if (errores.length > 0) {
        // Al haber errores, la validación por campo ya los mostró (rojo debajo del input).
        // Forzamos la ocultación del contenedor de resultados superior.
        $("#product-result").attr("class", "card my-4 d-none"); 
        return;
    }

    let finalJSON = {
        nombre: $('#name').val(),
        precio: parseFloat($('#precio').val()),
        unidades: parseInt($('#unidades').val()),
        modelo: $('#modelo').val(),
        marca: $('#marca').val(),
        detalles: $('#detalles').val(),
        imagen: $('#imagen').val() || "img/default.png"
    };

    if (isEdit) {
        finalJSON['id'] = id;
    }

    $.ajax({
        url: url,
        type: 'POST',
        contentType: "application/json;charset=UTF-8", 
        data: JSON.stringify(finalJSON),
        dataType: 'json',
        success: function(respuesta) {
            let template_bar = `
                <li style="list-style: none;">status: ${respuesta.status}</li>
                <li style="list-style: none;">message: ${respuesta.message}</li>
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

// FUNCIÓN CALLBACK DE BOTÓN "Eliminar"
function eliminarProducto(e) {
    e.preventDefault(); 

    if (confirm("¿De verdad deseas eliminar el Producto?")) {

        var id = $(this).closest('tr').attr("productId");

        $.ajax({
            url: './backend/product-delete.php',
            type: 'GET',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(respuesta) {
                let template_bar = `
                    <li style="list-style: none;">status: ${respuesta.status}</li>
                    <li style="list-style: none;">message: ${respuesta.message}</li>
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

// FUNCIÓN: Se activa al presionar el botón "Editar"
function editarProducto(e) {
    e.preventDefault();
    let id = $(this).closest('tr').attr("productId");
    let producto = globalProductos.find(p => p.id == id);

    if (producto) {
        $('#productId').val(producto.id);
        $('#name').val(producto.nombre);
        $('#precio').val(producto.precio);
        $('#unidades').val(producto.unidades);
        $('#modelo').val(producto.modelo);
        $('#marca').val(producto.marca);
        $('#detalles').val(producto.detalles);
        $('#imagen').val(producto.imagen);

        $('#product-form button[type="submit"]').text('Modificar Producto');
        
        if ($('#cancel-edit').length === 0) {
            $('#product-form button[type="submit"]').after(
                '<button type="button" id="cancel-edit" class="btn btn-secondary btn-block mt-2">Cancelar Edición</button>'
            );
        }
        
        window.scrollTo(0, 0);
    }
}

// FUNCIÓN: Restablece el formulario al estado inicial
function resetForm() {
    $('#product-form').trigger('reset');
    $('#productId').val('');
    $('#product-form button[type="submit"]').text('Agregar Producto');
    $('#cancel-edit').remove();
    $('.invalid-feedback').remove();
    $('.is-invalid').removeClass('is-invalid');
    $('.validation-status').remove();
}

function mostrarErrorCampo(campo, mensaje) {
    limpiarErrorCampo(campo);
    campo.after(`<div class="invalid-feedback d-block">${mensaje}</div>`);
}

function limpiarErrorCampo(campo) {
    campo.next('.invalid-feedback').remove();
}