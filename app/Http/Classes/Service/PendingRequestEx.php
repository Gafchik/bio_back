<?php

namespace App\Http\Classes\Service;

use Illuminate\Http\Client\PendingRequest;

class PendingRequestEx extends PendingRequest
{
    public function setHeaders(array $headers)
    {
        return tap($this, function ($request) use ($headers) {
            return $this->options['headers'] = $headers;
        });
    }
}
