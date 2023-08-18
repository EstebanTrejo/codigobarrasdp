<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Codigo de barras Imprenta</title>
</head>
<body>
    <div class="d-flex flex-column justify-content-center align-items-center vh-100">
        <h1 class="mb-3">Sistema Imprenta</h1>
        <h4 class="mb-4">Generador de Códigos de Barras</h4>
        
        <form method="post" action="{{ route('generate-pdf') }}" target="_blank" class="my-4" id="form">
            @csrf
            <div class="form-group text-center">
                <label for="codigo" class="h3">Código Actual:</label>
                <input type="text" id="codigo" name="codigo" value="{{ $startCode }}" readonly class="form-control form-control-lg text-center">
            </div>
            
            <div class="form-group text-center">
                <label for="quantity" class="h3">Cantidad a Generar:</label>
                <input type="number" id="quantity" name="quantity" min="1" max="100" value="1" class="form-control form-control-lg text-center">
            </div>
            
            <div class="form-group text-center">
                <label for="resultado" class="h3">Generará hasta el código:</label>
                <input type="text" id="resultado" name="resultado" value="{{ $startCode }}" readonly class="form-control form-control-lg text-center">
            </div>
        
            <div class="text-center mt-4">
                <button type="button" id="generate-pdf-button" class="btn btn-primary btn-lg">Generar PDF</button>
            </div>
        </form>
    </div>
    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script>

        // Obtener referencias a los elementos de input
        const quantityInput = document.getElementById('quantity');
        const resultadoInput = document.getElementById('resultado');
        const startCode = parseInt({{ $startCode }}, 10);
        const calculatedResult2 = startCode + quantityInput;

    
        // Escuchar el evento de cambio en el input de cantidad
        quantityInput.addEventListener('input', function() {
            const quantity = parseInt(quantityInput.value, 10);
            const calculatedResult = startCode + quantity - 1;
            // Actualizar el valor del input de resultado
            resultadoInput.value = calculatedResult;
        });

        //validar input
        document.getElementById('quantity').addEventListener('change', function() {
        var quantityValue = parseInt(this.value);
        if (this.value.trim() === "" || quantityValue > 100 || quantityValue <= 0) {
            Swal.fire({
                icon: 'error',
                title: '¡Atención!',
                text: 'El campo no puede estar vacio y La cantidad debe esta entre 1 y 100!.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
            this.value = 1;
            resultadoInput.value = startCode;

            // setTimeout(function() {
            //     location.reload();
            // }, 2000); // Establecer el valor máximo permitido
        }
    });


// estas seguro en el pdf
document.getElementById('generate-pdf-button').addEventListener('click', function() {
    const quantity = parseInt(quantityInput.value, 10);
    const calculatedResult = startCode + quantity - 1;
    const confirmationText = 'Esta acción generará el PDF con codigos desde: ' + startCode + ' hasta ' + calculatedResult + '. \n¿Estás seguro de continuar?';

    Swal.fire({
        title: '¿Estás seguro?',
        text: confirmationText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, generar PDF',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('form');
            form.submit();

            // Esperar 2 segundos y recargar la página
            setTimeout(function() {
                location.reload();
            }, 1000);
        }
    });
});


        // const form = document.getElementById('barcode-form');

// document.addEventListener('DOMContentLoaded', function() {
//     document.getElementById('generate-pdf-button').addEventListener('click', function() {
//         setTimeout(function() {
//             location.reload(); // Recargar la página después de 3 segundos
//         }, 2000); // 3000 milisegundos = 3 segundos
//     });
// });
    </script>
</body>
</html>