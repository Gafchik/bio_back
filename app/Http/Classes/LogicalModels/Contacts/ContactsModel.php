<?php

namespace App\Http\Classes\LogicalModels\Contacts;

use App\Models\MySql\Biodeposit\{
    Contact_translations,
    Contacts as ContactsTable,
};

class ContactsModel
{
    public function __construct(
        private ContactsTable $contacts,
        private Contact_translations $contactsTranc,
    ){}
    public function getContacts(): array
    {
        return [
            'contacts' => $this->contacts->where('status','=',true)->get()->toArray(),
            'contactsTranc' => $this->contactsTranc->get()->toArray(),
        ];
    }
}
