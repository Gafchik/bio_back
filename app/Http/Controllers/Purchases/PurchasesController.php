<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Classes\LogicalModels\Purchases\Purchases;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Facades\ArchiveFacade;
use App\Http\Facades\PdfFacade;
use App\Models\MySql\Biodeposit\Orders;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchasesController extends BaseController
{
    private const DOCUMENTS_PATH = [
        'act' => 'pdfView.document_templates.act',
        'agency_contract' => 'pdfView.document_templates.agency_contract',
        'offer' => 'pdfView.document_templates.offer',
//      'terms_of_use' => 'pdfView.document_templates.terms_of_use', не готово
    ];
    public function __construct(
        private Purchases $model
    )
    {
        parent::__construct();
    }
    public function getPurchases(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getPurchases()
        );
    }
    public function getTreeByOrderId(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'int', 'exists:' . Orders::class . ',id',],
        ]);
        return $this->makeGoodResponse(
            $this->model->getTreeByOrderId($validated)
        );
    }
    public function download(int $id)
    {
        $docData = $this->model->getDocumentData($id);
        $zipData = [];
        foreach (self::DOCUMENTS_PATH as $name => $path){
            $pdf = PdfFacade::getPdf(
                pathTemplate: $path,
                templateData: $docData,
                format: '',
                orientation: '',
            );
            $zipData[$name] = [
                'outputFunction' => 'output',
                'mime' => '.pdf',
                'class' => $pdf,
            ];
        }

        $zipContent = ArchiveFacade::createZipArchive($zipData);

        return response()->stream(
            function () use ($zipContent) {
                echo $zipContent;
            },
            200,
            [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="Documents_' . $id . '.zip"',
            ]
        );
    }
}
