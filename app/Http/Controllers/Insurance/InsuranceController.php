<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Classes\LogicalModels\Insurance\Exceptions\BaseInsuranceException;
use App\Http\Classes\LogicalModels\Insurance\Insurance;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Facades\PdfFacade;
use App\Models\MySql\Biodeposit\Dic_insurance_type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsuranceController extends BaseController
{
    private const TEMPLATE_PATH = 'pdfView.insurance.insurance';
    private const LANG_PATH = 'pdfView/insurance/insurance';
    public function __construct(
        private Insurance $model
    )
    {
        parent::__construct();
    }
    public function getInsuranceTypes(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getInsuranceTypes()
        );
    }
    public function getInsuranceTrees(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getInsuranceTrees()
        );
    }
    public function download(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);
        try {
            $templateData = $this->model->getTemplateData($validated['id']);
            $templateData['trans_prefix'] = self::LANG_PATH;
            $templateData['images'] = $this->downloadImage();
            $pdf = PdfFacade::getPdf(
                pathTemplate: self::TEMPLATE_PATH,
                templateData: $templateData,
                format: '',
                orientation: '',
            );
            return response($pdf->output())
                ->header('Content-Type', 'application/pdf');
        }catch (BaseInsuranceException $e){
            return $this->makeBadResponse($e);
        }
    }
    public function downloadImage(): array
    {
        $result = [];
        $paths = [
            'signature1' => public_path('images/insurance/signature1.png'),
            'signature2' => public_path('images/insurance/signature2.png'),
            'stamp1' => public_path('images/insurance/stamp1.png'),
            'stamp2' => public_path('images/insurance/stamp2.png'),
        ];
        foreach ($paths as $key => $path) {
            $image = file_get_contents($path);
            $base64Image = base64_encode($image);
            $result[$key] = $base64Image;
        }
        return $result;
    }
    public function createInsurance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'int'],
            'type' => ['required', 'int','exists:' . Dic_insurance_type::class . ',id',],
        ]);
        try {
            $this->model->createInsurance($validated);
        }catch (BaseInsuranceException $e){
            return $this->makeBadResponse($e);
        }
        return $this->makeGoodResponse([]);
    }
}
