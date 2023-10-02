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
    $latestBarcode = CodigoDeBarras::orderBy('datos_del_codigo', 'desc')->first();

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
        $quantity = $request->input('quantity', 1);
        $barcodeGenerator = new BarcodeGeneratorPNG();
        $startCode = $request->input('codigo');

        if ($startCode) {
            $pdf = new FPDF();

            for ($i = 0; $i < $quantity; $i++) {
                $formattedDate = Carbon::now()->format('y-m-d h:i:s');
                $convertedDate = Carbon::createFromFormat('y-m-d H:i:s', $formattedDate)->setSeconds(0)->format('Y-m-d H:i:s');

                CodigoDeBarras::create([
                    'datos_del_codigo' => $startCode,
                ]);

                $pdf->SetFont('Arial', 'B', 9);
                $pdf->AddPage('P', 'legal');
                $barcodePNG = $barcodeGenerator->getBarcode($startCode, $barcodeGenerator::TYPE_CODE_128);

                $tmpImagePath = public_path('temp_barcode_' . $startCode . '.png');
                file_put_contents($tmpImagePath, $barcodePNG);

                $pdf->Image($tmpImagePath, 175, 10, 30, 15);

                unlink($tmpImagePath);

                $pdf->SetXY(182, 24);
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(0, 10, $startCode, 0, 0, 'L');

                $pdf->SetFont('Arial', '', 9);
                $pdf->SetXY(10, 20);
                $pdf->Cell(0, 10, utf8_decode('Ref Expte Nº_____________'), 0, 0, 'L');

                $xCell = 190;
                $yCell = 33;
                $cellHeight = 20;

                $pdf->SetXY($xCell, $yCell);
                $pdf->Cell(15, 10, 'Ujier', 'LRT', 1, 'C');

                $pdf->SetXY($xCell, $yCell + 10);
                $pdf->Cell(15, $cellHeight - 13, '', 'LRB', 1, 'C');

                $footerText = 'Original - PODER JUDICIAL DE SANTIAGO DEL ESTERO';
                $footerX = 10;
                $footerY = $pdf->GetPageHeight() - 31;
                $pdf->SetXY($footerX, $footerY);
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(0, 10, $footerText, 0, 0, 'R');

                $startCode++;
            }

            ob_start();
            $pdf->Output();
            $pdfContent = ob_get_clean();

            return response($pdfContent)->header('Content-Type', 'application/pdf');
        } else {
            return "No hay códigos de barras registrados en la base de datos.";
        }
    }
    

    }
?>