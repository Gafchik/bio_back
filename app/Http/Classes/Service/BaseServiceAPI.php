<?php

namespace App\Http\Classes\Service;
use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\{
    Headers,
    HttpStatus,
    RequestTypes,
};

use Illuminate\Http\Client\{
    ConnectionException,
    Response,
};
use Illuminate\Support\Facades\App;
class BaseServiceAPI
{
    public const SERVICE_NAME = 'BASE_API_SERVICE_NAME:::';
    public const SEPARATE = ':::';
    private const PAYLOAD = 'PAYLOAD:::';

    protected array $options;
    protected ?BaseException $connectionTimeOutException = null;
    protected ?BaseException $clientException = null;
    protected ?BaseException $serverException = null;
    private ?array $headersAPI = null;
    private PendingRequestEx $httpRequest;

    public function __construct(
        array $options,
        ?BaseException $connectionTimeOutException = null,
        ?BaseException $clientException = null,
        ?BaseException $serverException = null
    ) {
        $this->httpRequest = App::make(PendingRequestEx::class);
        $this->options = $options;
        $this->connectionTimeOutException ??= $connectionTimeOutException;
        $this->clientException ??= $clientException;
        $this->serverException ??= $serverException;
        $this->httpRequest->withOptions($this->options);
    }


    protected function makeRequestJson($url, string $type, array $data = []): Response
    {
        return $this->makeRequest($url, $type, $data);
    }

    protected function makeRequestXML($url, string $type, string $data = ''): Response
    {
        $this->httpRequest->withBody($data, Headers::TEXT_XML);

        return $this->makeRequest($url, $type, []);
    }

    private function makeRequest($url, string $type, array $data = []) : Response
    {
        try {
            if ($this->headersAPI && count($this->headersAPI))
                $this->httpRequest->setHeaders($this->headersAPI);


            $response = ($type === RequestTypes::GET)
                ? $this->httpRequest->get($url)
                : $this->httpRequest->post($url, $data);
        } catch (ConnectionException $exception) {
            if ($this->connectionTimeOutException)
                throw new $this->connectionTimeOutException();
            throw new ConnectionTimeOutExceptions(static::SERVICE_NAME);
        } finally {
            $this->headersAPI = null;
        }

        if ($response->successful()) {

            return $response;
        }
        if ($response->clientError() && $this->clientException)
            throw new $this->clientException($response['textErrorUkr'] ?? $response['textError'] ?? $response);
        if ($response->serverError() && $this->serverException)
            throw new $this->serverException($response);
        return $response;
    }

    protected function setHeaders(array $headers) : void {
        $this->headersAPI = $headers;
    }
}
