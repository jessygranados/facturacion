$(function () {

    // ================= Buscar producto al escribir el código =================
    $('#codpro').on('keyup', function () {
        let codigo = $(this).val().trim();

        if (codigo === '') {
            $('#descripcionpro').val('');
            $('#preciopro').val('');
            $('#totalpro').val('');
            $('#codpro').data('stock', 0);
            return;
        }

        $.ajax({
            url: 'ajax_producto.php',
            type: 'GET',
            data: { codigo: codigo },
            dataType: 'json',
            success: function (respuesta) {
                if (respuesta.ok) {
                    $('#descripcionpro').val(respuesta.descripcion);
                    $('#preciopro').val(respuesta.precio.toFixed(2));
                    $('#codpro').data('stock', respuesta.stock);
                    calcularTotalProducto();
                } else {
                    $('#descripcionpro').val('');
                    $('#preciopro').val('');
                    $('#totalpro').val('');
                    $('#codpro').data('stock', 0);
                }
            }
        });
    });

    // ================= Calcular total de la línea (cantidad x precio) =================
    $('#cantidadpro').on('keyup change', calcularTotalProducto);

    function calcularTotalProducto() {
        let cantidad = parseFloat($('#cantidadpro').val()) || 0;
        let precio = parseFloat($('#preciopro').val()) || 0;
        let total = cantidad * precio;
        $('#totalpro').val(total > 0 ? total.toFixed(2) : '');
    }

    // ================= Agregar producto a la factura =================
    $('#btnAgregarProducto').on('click', function () {
        let codigo = $('#codpro').val().trim();
        let descripcion = $('#descripcionpro').val().trim();
        let cantidad = parseFloat($('#cantidadpro').val()) || 0;
        let precio = parseFloat($('#preciopro').val()) || 0;

        if (codigo === '' || descripcion === '') {
            alert('Escriba un código de producto válido.');
            return;
        }
        if (cantidad <= 0 || !Number.isInteger(cantidad)) {
            alert('La cantidad debe ser un número entero mayor a cero.');
            return;
        }

        // Validar contra el stock disponible (sumando lo que ya está en la factura)
        let stock = parseInt($('#codpro').data('stock')) || 0;
        let yaEnFactura = 0;
        $('#tablaFactura tbody tr').each(function () {
            if (String($(this).data('codigo')) === codigo) {
                yaEnFactura += parseFloat($(this).find('.col-cantidad').text()) || 0;
            }
        });
        if (yaEnFactura + cantidad > stock) {
            alert('Stock insuficiente. Disponible: ' + stock + ' (ya en la factura: ' + yaEnFactura + ').');
            return;
        }

        let total = cantidad * precio;

        let fila = `<tr data-codigo="${codigo}" data-precio="${precio}">
                        <td class="col-codigo">${codigo}</td>
                        <td class="col-cantidad">${cantidad}</td>
                        <td class="col-descripcion">${descripcion}</td>
                        <td class="col-precio">${precio.toFixed(2)}</td>
                        <td class="col-total">${total.toFixed(2)}</td>
                        <td class="col-quitar">
                            <button type="button" class="btn-quitar" title="Quitar">&times;</button>
                        </td>
                    </tr>`;

        $('#tablaFactura tbody').append(fila);

        // Limpiar la sección "Seleccionar producto"
        $('#codpro').val('').focus();
        $('#descripcionpro').val('');
        $('#preciopro').val('');
        $('#cantidadpro').val('');
        $('#totalpro').val('');
        $('#codpro').data('stock', 0);

        calcularTotalesFactura();
    });

    // ================= Quitar una línea de la factura =================
    $('#tablaFactura').on('click', '.btn-quitar', function () {
        $(this).closest('tr').remove();
        calcularTotalesFactura();
    });

    // ================= Calcular Subtotal, IVA y Total a pagar =================
    const PORCENTAJE_IVA = 0.15; // 15%

    function calcularTotalesFactura() {
        let subtotal = 0;

        $('#tablaFactura tbody tr').each(function () {
            subtotal += parseFloat($(this).find('.col-total').text()) || 0;
        });

        let iva = subtotal * PORCENTAJE_IVA;
        let total = subtotal + iva;

        $('#subtotal').val(subtotal.toFixed(2));
        $('#iva').val(iva.toFixed(2));
        $('#totalapagar').val(total.toFixed(2));
    }

    // ================= Buscar cliente al escribir el código =================
    $('#codcli').on('keyup', function () {
        let codigo = $(this).val().trim();

        if (codigo === '') {
            $('#nombrecliente').val('');
            $('#nombrecliente').data('idcliente', '');
            return;
        }

        $.ajax({
            url: 'ajax_cliente.php',
            type: 'GET',
            data: { codigo: codigo },
            dataType: 'json',
            success: function (respuesta) {
                if (respuesta.ok) {
                    $('#nombrecliente').val(respuesta.nombre);
                    $('#nombrecliente').data('idcliente', respuesta.codigo);
                } else {
                    $('#nombrecliente').val('');
                    $('#nombrecliente').data('idcliente', '');
                }
            }
        });
    });

    // ================= Botón "Nueva factura" =================
    $('#btnNuevaFactura').on('click', function () {
        limpiarFactura();
    });

    function limpiarFactura() {
        $('#tablaFactura tbody').empty();
        $('#codpro, #descripcionpro, #preciopro, #cantidadpro, #totalpro').val('');
        $('#codcli, #nombrecliente').val('');
        $('#nombrecliente').data('idcliente', '');
        $('#subtotal, #iva, #totalapagar').val('');
    }

    // ================= Botón "Guardar factura" =================
    $('#btnGuardarFactura').on('click', function () {
        let idCliente = $('#nombrecliente').data('idcliente');

        if (!idCliente) {
            alert('Escriba el código de un cliente válido antes de guardar.');
            return;
        }

        let detalle = [];
        $('#tablaFactura tbody tr').each(function () {
            let fila = $(this);
            detalle.push({
                codigo: fila.data('codigo'),
                descripcion: fila.find('.col-descripcion').text(),
                cantidad: parseFloat(fila.find('.col-cantidad').text()) || 0,
                precio: parseFloat(fila.find('.col-precio').text()) || 0,
                total: parseFloat(fila.find('.col-total').text()) || 0
            });
        });

        if (detalle.length === 0) {
            alert('Agregue al menos un producto a la factura.');
            return;
        }

        $.ajax({
            url: 'guardar_factura.php',
            type: 'POST',
            dataType: 'json',
            data: {
                id_cliente: idCliente,
                subtotal: $('#subtotal').val() || 0,
                iva: $('#iva').val() || 0,
                total: $('#totalapagar').val() || 0,
                detalle: JSON.stringify(detalle)
            },
            success: function (respuesta) {
                if (respuesta.ok) {
                    alert('Factura No. ' + respuesta.numero_factura + ' guardada correctamente.');
                    limpiarFactura();
                    $('#numeroFactura').text(String(respuesta.siguiente).padStart(4, '0'));
                } else {
                    alert('Error: ' + respuesta.mensaje);
                }
            },
            error: function () {
                alert('Ocurrió un error al conectar con el servidor.');
            }
        });
    });

});
