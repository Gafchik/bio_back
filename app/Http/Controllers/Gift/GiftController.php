<?php

namespace App\Http\Controllers\Gift;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\Gift\Gift;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Facades\PdfFacade;
use App\Models\MySql\Biodeposit\{Trees,Gifts};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GiftController extends BaseController
{
    private const LANG_PATH = [
        'gift_certificate' => 'pdfView/gift_certificate/gift_certificate',
    ];
    private const TEMPLATE_PATH = [
        'gift_certificate' => 'pdfView.gift_certificate.gift_certificate',
    ];
    public function __construct(
        private Gift $model
    )
    {
        parent::__construct();
    }

    public function createGift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'int', 'in:2,3'],
            'treesToGift' => ['required', 'array'],
            'treesToGift.*.id' => ['required', 'int', 'exists:' . Trees::class . ',id',],
            'freezeMoneyYear' => ['required', 'int', 'min:0'],
            'freezeSellYear' => ['required', 'int', 'min:3'],
            'isKnowUser' => ['required', 'boolean',],
            'notifyDate' => ['nullable', 'date', 'after_or_equal:today'],
            'email' => [
                'nullable',
                Rule::requiredIf(fn() => !!$request->isKnowUser),
                'email',
            ],
        ]);
        try {
            $this->model->createGift($validated);
            return $this->makeGoodResponse([]);
        } catch (BaseException $e) {
            return $this->makeBadResponse($e);
        }
    }
    public function getGiftInfo(): JsonResponse
    {
        $result = $this->model->getGiftInfo();
        return $this->makeGoodResponse($result);
    }
    public function cancelMyGift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gift_id' => ['required', 'int', 'exists:' . Gifts::class . ',id',],
        ]);
        try {
            $this->model->cancelMyGift($validated['gift_id']);
            return $this->makeGoodResponse([]);
        } catch (BaseException $e) {
            return $this->makeBadResponse($e);
        }
    }
    public function downloadGiftCertificate(Request $request)
    {
        $validated = $request->validate([
            'gift_id' => ['required', 'int', 'exists:' . Gifts::class . ',id',],
        ]);
        try {
            $templateData = $this->model->getGiftCertificateData($validated['gift_id']);
            $templateData['trans_prefix'] = self::LANG_PATH['gift_certificate'];
            $pdf = PdfFacade::getPdf(
                pathTemplate: self::TEMPLATE_PATH['gift_certificate'],
                templateData: $templateData,
                format: '',
                orientation: '',
            );
            return response($pdf->output())
                ->header('Content-Type', 'application/pdf');
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
    public function getGiftByCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);
        try {
            $this->model->getGiftByCode($validated['code']);
            return $this->makeGoodResponse([]);
        } catch (BaseException $e) {
            return $this->makeBadResponse($e);
        }
    }
}

