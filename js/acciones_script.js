// Muestra el nombre del producto al escribir su código (AJAX con XMLHttpRequest)
function mostrarProducto(produc) {
    if (produc.length == 0) { // Si no se ha introducido ningún id del producto
        // Mostrar en nompro "Producto no seleccionado"
        document.getElementById("nompro").value = "Producto no seleccionado";
        return; // Salir de la función
    }

    // Declarar el objeto fnproducto de tipo XMLHttpRequest
    var fnproducto = new XMLHttpRequest();

    // Ejecutar esta función anónima dependiendo del valor de onreadystatechange
    fnproducto.onreadystatechange = function () {
        // Si el valor readyState es 4 y el de status es 200 entonces
        if (this.readyState == 4 && this.status == 200) {
            // Mostrar el valor devuelto en el input como Producto
            document.getElementById("nompro").value = this.responseText;
        }
    };

    // Enviar el id del producto a través de la variable dato y método get
    // al archivo conProductoId.php
    fnproducto.open("GET", "conProductoId.php?dato=" + encodeURIComponent(produc), true);
    fnproducto.send();
}
