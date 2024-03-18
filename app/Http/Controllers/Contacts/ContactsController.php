<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Classes\LogicalModels\Contacts\Contacts;
use App\Http\Controllers\BaseControllers\BaseController;

class ContactsController extends BaseController
{
    public function __construct(
        private Contacts $model
    )
    {
        parent::__construct();
    }
    public function getContacts()
    {
        return $this->makeGoodResponse(
            $this->model->getContacts()
        );
    }
}
