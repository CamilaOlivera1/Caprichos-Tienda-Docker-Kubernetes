// =====================================================
//                CARRUSEL
// =====================================================
let currentIndex = 0;
const images = document.querySelectorAll('.carousel-images img');
const totalImages = images.length;

function showNextImage() {
    currentIndex = (currentIndex + 1) % totalImages;
    const offset = -currentIndex * 100;
    document.querySelector('.carousel-images').style.transform = `translateX(${offset}%)`;
}

function startCarousel() {
    if (totalImages > 0) setInterval(showNextImage, 5000);
}


// =====================================================
//               VARIABLES GLOBALES DE PAGO
// =====================================================
let totalBase = 0;
let costoEnvio = 0;
let interes = 0;


// ---------------------------
// ELEMENTOS DEL DOM
// ---------------------------
const envioSelect = document.getElementById("envio_id");
const medioPagoSelect = document.getElementById("medioPago_id");
const cuotasSelect = document.getElementById("cuota_id");

const formTarjeta = document.getElementById("form-tarjeta");
const cuotasContainer = document.getElementById("cuotas-container");
const mensajeEfectivo = document.getElementById("mensaje-efectivo");

const totalBaseElement = document.getElementById("total-base");
const totalPagarElement = document.getElementById("total-pagar");

const hiddenSubtotal = document.getElementById("subtotal");
const hiddenEnvio = document.getElementById("costo_envio");
const hiddenInteres = document.getElementById("interes_aplicado");
const hiddenTotalFinal = document.getElementById("total_final");

const btnFinalizar = document.getElementById("btn-finalizar");


// ---------------------------
// ACTUALIZAR COSTO ENVÍO + MENSAJE
// ---------------------------
function actualizarEnvio() {
    const opcion = envioSelect.options[envioSelect.selectedIndex];
    const costo = parseFloat(opcion.dataset.costo || 0);

    document.getElementById("costo-envio").innerText = "$" + costo;

    hiddenEnvio.value = costo;

    actualizarMensajeEfectivo();
    recalcularTotal();
}


// ---------------------------
// MENSAJE SEGÚN ENVÍO (solo si es efectivo)
// ---------------------------
function actualizarMensajeEfectivo() {
    if (medioPagoSelect.value !== "1") {
        mensajeEfectivo.innerText = "";
        return;
    }

    let envio = envioSelect.value;
    if (!envio) {
        mensajeEfectivo.innerText = "";
        return;
    }

    let texto = "";

    if (envio == "1") { // Retiro por local
        texto = "Podrás abonar el total de tu compra cuando la retires por el local.";
    } else {
        texto = "Podrás abonar el total de tu compra cuando se te sea entregada.";
    }

    mensajeEfectivo.innerText = texto;
}


// ---------------------------
// CAMBIO DE MEDIO DE PAGO
// ---------------------------
function actualizarMedioPago() {
    let medio = medioPagoSelect.value;

    if (medio === "") {
        formTarjeta.style.display = "none";
        cuotasContainer.style.display = "none";
        mensajeEfectivo.innerText = "";
        actualizarRequiredTarjeta();
        return;
    }

    if (medio === "1") {
        // EFECTIVO
        formTarjeta.style.display = "none";
        cuotasContainer.style.display = "none";
        actualizarMensajeEfectivo();
        //agregue esta linea de abajo 
        mensajeEfectivo.innerText = "";
    } else {
        // T.DEBITO = 2  /  T.CREDITO = 3
        formTarjeta.style.display = "block";

        if (medio === "3") {
            cuotasContainer.style.display = "block";
        } else {
            cuotasContainer.style.display = "none";
        }

        mensajeEfectivo.innerText = "";
    }

    actualizarRequiredTarjeta();
    recalcularTotal();
}

// ---------------------------
// CUOTAS E INTERÉS
// ---------------------------
function actualizarCuota() {
    const opcion = cuotasSelect.options[cuotasSelect.selectedIndex];
    const interes = parseFloat(opcion ? opcion.dataset.interes : 0);

    hiddenInteres.value = interes;
    document.getElementById("interes-info").innerText = "Interés: " + interes + "%";

    recalcularTotal();
}


// ---------------------------
// RE-CALCULAR TOTAL FINAL
// ---------------------------
function recalcularTotal() {
    let subtotal = parseFloat(hiddenSubtotal.value);
    let envioCosto = parseFloat(hiddenEnvio.value);
    let interes = parseFloat(hiddenInteres.value);

    let total = subtotal + envioCosto;

    if (interes > 0) {
        total += (subtotal * interes) / 100;
    }

    hiddenTotalFinal.value = total;
    totalPagarElement.innerText = "$" + total.toFixed(2);
}


// ---------------------------
// VALIDACIÓN DE CAMPOS TARJETA
// ---------------------------
function validarCampo(input, regex, mensajeError) {
    let valor = input.value.trim();

    if (!regex.test(valor)) {
        input.style.border = "2px solid red";
        input.setCustomValidity(mensajeError);
        return false;
    }

    input.style.border = "2px solid green";
    input.setCustomValidity("");
    return true;
}

function validarCamposTarjeta() {
    if (medioPagoSelect.value === "1") return true; // Efectivo → no valida tarjeta
    if (medioPagoSelect.value === "") return false;

    const valido =
    validarCampo(document.getElementById("nombre_titular"), /.+/, "Ingrese un nombre válido") &&
    validarCampo(document.getElementById("dni_titular"), /^[0-9]{7,8}$/, "DNI inválido") &&
    validarCampo(document.getElementById("email_titular"), /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/, "Email inválido") &&
    validarCampo(document.getElementById("tel_titular"), /^[0-9]{6,15}$/, "Teléfono inválido") &&
    validarCampo(document.getElementById("numero_tarjeta"), /^[0-9]{16}$/, "Debe tener 16 números") &&
    validarCampo(document.getElementById("cvv"), /^[0-9]{3,4}$/, "CVV inválido");

    return !!valido;
}


document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("formCompra");
    const envioSelect = document.getElementById("envio_id");
    const medioPago = document.getElementById("medioPago_id");
    const formTarjeta = document.getElementById("form-tarjeta");
    const cuotasContainer = document.getElementById("cuotas-container");
    const cuotasSelect = document.getElementById("cuota_id");
});

// ---------------------------
// EVENTOS
// ---------------------------
envioSelect.addEventListener("change", actualizarEnvio);
medioPagoSelect.addEventListener("change", actualizarMedioPago);
cuotasSelect.addEventListener("change", actualizarCuota);

// VALIDACIÓN AL ENVIAR FORMULARIO
document.getElementById("formCompra").addEventListener("submit", function (e) {

    // 1) Validar envío
    if (envioSelect.value === "") {
        alert("⚠ Debes seleccionar un tipo de envío.");
        e.preventDefault();
        return;
    }

    // 2) Validar medio de pago
    if (medioPagoSelect.value === "") {
        alert("⚠ Debes seleccionar un medio de pago.");
        e.preventDefault();
        return;
    }

    // 3) Validaciones para tarjeta
    const textoMedio = medioPagoSelect.options[medioPagoSelect.selectedIndex].text;

    if (textoMedio.includes("Tarjeta")) {

        const camposTarjeta = [
            "nombre_titular",
            "dni_titular",
            "email_titular",
            "tel_titular",
            "numero_tarjeta",
            "fecha_expiracion",
            "cvv"
        ];

        for (let id of camposTarjeta) {
            const campo = document.getElementById(id);
            if (!campo || !campo.value.trim()) {
                alert("⚠ Debes completar TODOS los datos de la tarjeta.");
                campo.focus();
                e.preventDefault();
                return;
            }
        }

        // 4) Validar cuotas SOLO si es crédito
        if (medioPagoSelect.value === "3" && (!cuotasSelect.value || cuotasSelect.value === "")) {
            alert("⚠ Debes seleccionar una opción de cuotas.");
            e.preventDefault();
            return;
        }
    }

});

