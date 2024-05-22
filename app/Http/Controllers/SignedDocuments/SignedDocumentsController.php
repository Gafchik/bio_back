<?php

namespace App\Http\Controllers\SignedDocuments;

use App\Http\Classes\LogicalModels\SignedDocuments\SignedDocuments;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Facades\ArchiveFacade;
use App\Http\Facades\PdfFacade;
use App\Models\MySql\Biodeposit\Trees;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignedDocumentsController extends BaseController
{
    private const DOCUMENTS_PATH = [
        'act' => 'pdfView.document_templates.act',
        'agency_contract' => 'pdfView.document_templates.agency_contract',
        'offer' => 'pdfView.document_templates.offer',
        'certificate' => 'pdfView.TreeCertificate.OldTreeCertificate',
//      'terms_of_use' => 'pdfView.document_templates.terms_of_use', не готово
    ];

    private const LANG_PATH = [
        'certificate' => 'pdfView/TreeCertificate/OldTreeCertificate',
    ];
    public function __construct(
        private SignedDocuments $model
    )
    {
        parent::__construct();
    }

    public function getOfferByUuid($treeId)
    {
        $docData['trees'] = $this->model->getOfferData(['uuid' => $treeId]);
        $pdf = PdfFacade::getPdf(
            pathTemplate: self::DOCUMENTS_PATH['offer'],
            templateData: $docData,
            format: '',
            orientation: '',
        );
        return $this->makePdfResponse($pdf);
    }
    public function getContractByUuid($treeId)
    {
        $userData = $this->model->getUserDataByDoc();
        $docData['order'] = $this->model->getOrderData(['uuid' => $treeId]);
        $docData['user'] = [
            'lastName' => $userData['last_name'],
            'firstName' => $userData['first_name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
        ];
        $pdf = PdfFacade::getPdf(
            pathTemplate: self::DOCUMENTS_PATH['agency_contract'],
            templateData: $docData,
            format: '',
            orientation: '',
        );
        return $this->makePdfResponse($pdf);
    }
    public function getActByUuid($treeId)
    {
        $userData = $this->model->getUserDataByDoc();
        $docData['order'] = $this->model->getOrderData(['uuid' => $treeId]);
        $docData['trees'] = $this->model->getOfferData(['uuid' => $treeId]);
        $docData['user'] = [
            'lastName' => $userData['last_name'],
            'firstName' => $userData['first_name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
        ];
        $pdf = PdfFacade::getPdf(
            pathTemplate: self::DOCUMENTS_PATH['act'],
            templateData: $docData,
            format: '',
            orientation: '',
        );
        return $this->makePdfResponse($pdf);
    }
    public function downloadCertificateByUuid($treeId)
    {
        $templateData = $this->model->getCertificateDara(['uuid' => $treeId]);
        $templateData['trans_prefix'] = self::LANG_PATH['certificate'];
        $pdf = PdfFacade::getPdf(
            pathTemplate: self::DOCUMENTS_PATH['certificate'],
            templateData: $templateData,
            format: '',
            orientation: '',
        );
        return $this->makePdfResponse($pdf);
    }
    public function signed(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uuid' => ['required', 'string','exists:' . Trees::class . ',uuid',],
        ]);
        $this->model->signed($validated);
        return $this->makeGoodResponse([]);
    }
}
