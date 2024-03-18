<?php

namespace App\Http\Classes\LogicalModels\Contacts;

use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;
use App\Http\Classes\Structure\Lang;

class Contacts
{

    public function __construct(
        private ContactsModel $model
    ){

    }
    public function getContacts(): array
    {
        $data = $this->model->getContacts();
        return $this->prepareContact(
            contacts: $data['contacts'],
            contactsTranc: $data['contactsTranc'],
        );
    }
    private function prepareContact(array $contacts, array $contactsTranc): array
    {
        $result = [];
        foreach ($contacts as $contact)
        {
            $item = [];
            foreach (Lang::ARRAY_LANG as $lang){
                $locale = TransformArrayHelper::callbackSearchFirstInArray(
                    array: $contactsTranc,
                    callback: function($tr) use ($lang,$contact){
                        return $tr['contact_id'] === $contact['id']
                            && $tr['locale'] === $lang;
                    }
                );
                $item['locale'][$lang] = $locale;
            }
            $result[] = [
                ...$contact,
                ...$item,
            ];
        }
        return $result;
    }
}
