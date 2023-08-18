<?php 

namespace App\Http\Controllers;


use FPDF;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade;
use Illuminate\Http\Request;
use App\Models\CodigoDeBarras;
use App\Http\Controllers\Controller;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeController extends Controller
{

    public function showBarcodeForm()
{
    // Obtener el último código de barras desde la base de datos
    $latestBarcode = CodigoDeBarras::orderBy('id', 'desc')->first();

    // Calcular el siguiente código incrementado en 1
    $nextCode = $latestBarcode ? $latestBarcode->datos_del_codigo + 1 : 1;

    return view('barcode-form', ['startCode' => $nextCode]);
}

    // public function generateBarcode($code)
    // {
    //     $barcodeGenerator = new BarcodeGeneratorSVG();
    //     $barcodeSVG = $barcodeGenerator->getBarcode($code, $barcodeGenerator::TYPE_CODE_128);

    //     return view('barcode', ['barcodeSVG' => $barcodeSVG, 'code' => $code]);
    // }

    // public function generateBarcodes(Request $request)
    // {
    //     $quantity = $request->input('quantity', 1);
    //     $barcodes = [];

    //     $barcodeGenerator = new BarcodeGeneratorSVG();

    //     for ($i = 0; $i < $quantity; $i++) {
    //         $code = rand(100000, 999999); // Cambia esto según tus necesidades
    //         $barcodeSVG = $barcodeGenerator->getBarcode($code, $barcodeGenerator::TYPE_CODE_128);
    //         $barcodes[] = ['svg' => $barcodeSVG, 'code' => $code];
    //     }

    //     return view('barcode', ['barcodes' => $barcodes]);
    // }
    public function generatePDF(Request $request)
{
    $quantity = $request->input('quantity', 1); // Obtener la cantidad desde el formulario
    $barcodeGenerator = new BarcodeGeneratorPNG();

    // Obtener el último código de barras desde la base de datos
    $latestBarcode = CodigoDeBarras::orderBy('id', 'desc')->first();

    if ($latestBarcode) {
        $startCode = $latestBarcode->datos_del_codigo;

        // Incrementar el código en 1 para el siguiente
        $nextCode = $startCode + 1;

        $pdf = new FPDF();
       
        for ($i = 0; $i < $quantity; $i++) {

//  Obtener la fecha actual en formato 'y-m-d h:i:s'
 $formattedDate = Carbon::now()->format('y-m-d h:i:s');
 // Convertir la fecha formateada al formato deseado
 $convertedDate = Carbon::createFromFormat('y-m-d H:i:s', $formattedDate)->setSeconds(0)->format('Y-m-d H:i:s');

            // Actualizar el código en la base de datos
            CodigoDeBarras::create([
                'datos_del_codigo' => $nextCode,
            ]);
            
        
            // Generar un código de barras y un PDF
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->AddPage('P', 'legal');
            $barcodePNG = $barcodeGenerator->getBarcode($nextCode, $barcodeGenerator::TYPE_CODE_128);
        
            // Guardar la imagen PNG temporalmente
            $tmpImagePath = public_path('temp_barcode.png');
            file_put_contents($tmpImagePath, $barcodePNG);
        
            // Agregar la imagen al PDF
            $pdf->Image($tmpImagePath, 180, 10, 30, 10);
        
            // Borrar la imagen temporal
            unlink($tmpImagePath);
        
            // Obtener el ancho del contenido del código
            $codeWidth = $pdf->GetStringWidth($nextCode);
        
            // Calcular la posición x para alinear el contenido a la derecha
            $pdfWidth = $pdf->GetPageWidth();
            $xPosition = $pdfWidth - $codeWidth - 25; // aqui mover para el numero del cod
        
            // Establecer la posición x y y para la celda
            $pdf->SetXY(187, 18);
        
            $pdf->SetFont('Arial', '', 12);
            // Agregar el código de barras como texto
            $pdf->Cell(0, 10, $nextCode, 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            // Alinear el texto "Ref Expte Nº_____________" a la izquierda vertical y horizontalmente
            $pdf->SetXY(10, 20); // Ajustar las coordenadas X e Y para el texto
            $pdf->Cell(0, 10, utf8_decode('Ref Expte Nº_____________'), 0, 0, 'L');
        
            // Coordenadas X e Y para la celda de la tabla
            $xCell = 194;
            $yCell = 32;

            // Altura de la celda
            $cellHeight = 20;

            // Posicionar "Ujier" más arriba dentro de la celda
            $pdf->SetXY($xCell, $yCell); 
            $pdf->Cell(15, 10, 'Ujier', 'LRT', 1, 'C'); // Espacio en blanco

            $pdf->SetXY($xCell, $yCell + 10); // Ajustar las coordenadas para posicionar "Ujier"
            $pdf->Cell(15, $cellHeight - 13, '', 'LRB', 1, 'C'); // Ancho: 20, Alto: cellHeight - 10, Bordes: 1, Salto de línea: 1, Alineación: 'C'

            // Pie de página
            $footerText = 'Original - PODER JUDICIAL DE SANTIAGO DEL ESTERO';
            $footerX = 10;
            $footerY = $pdf->GetPageHeight() - 31; // Posicionamiento cerca del borde inferior
            $pdf->SetXY($footerX, $footerY);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(0, 10, $footerText, 0, 0, 'R'); // Cambio de '0' a '1' para salto de línea
            
            // Incrementar el código para el siguiente ciclo
            $nextCode++;
        }
        
        // Obtener el contenido del PDF
        ob_start();
        $pdf->Output();
        $pdfContent = ob_get_clean();
        
        // Retornar el contenido del PDF
        return response($pdfContent)->header('Content-Type', 'application/pdf');
        
    } else {
        return "No hay códigos de barras registrados en la base de datos.";
    }
}

    }
?>