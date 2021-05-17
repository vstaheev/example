<?php

namespace App\Banks\Clients;

use App\Banks\Contracts\Bank;
use App\Banks\Contracts\BankResponse;
use App\Banks\Exceptions\BankConnectException;
use App\Banks\Exceptions\BankRequestException;
use App\Services\EventLog;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class PfsBankClient
 * @package App\Banks\Clients
 */
class PfsBankClient extends Bank
{
    public const RESPONSE_OK_CODE = '0000';

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function cardInquiry(array $data): BankResponse
    {
        return $this->sendRequest('CardInquiry', $data);
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function cardIssue(array $data): BankResponse
    {
        return $this->sendRequest('CardIssue', $data);
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function updateCard(array $data): BankResponse
    {
        return $this->sendRequest('UpdateCard', $data);
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function depositToCard(array $data): BankResponse
    {
        return $this->sendRequest('DepositToCard', $data);
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function viewStatement(array $data): BankResponse
    {
        return $this->sendRequest('ViewStatement', $data);
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function getPin(array $data): BankResponse
    {
        return $this->sendRequest('GetPin', $data);
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function upgradeCard(array $data): BankResponse
    {
        return $this->sendRequest('UpgradeCard', $data);
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function changeCardStatus(array $data): BankResponse
    {
        return $this->sendRequest('ChangeCardStatus', $data);
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function bankPayment(array $data): BankResponse
    {
        return $this->sendRequest('BankPayment', $data, 'A33');
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function bankPaymentStatement(array $data): BankResponse
    {
        return $this->sendRequest('BankPaymentStatement', $data);
    }

    /**
     * @param array $data
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    public function bankPaymentStatementById(array $data): BankResponse
    {
        return $this->sendRequest('BankPaymentStatementById', $data);
    }

    /**
     * @param string      $method
     * @param array       $data
     * @param string|null $rootElement
     * @return BankResponse
     * @throws BankConnectException
     * @throws BankRequestException
     */
    protected function sendRequest(string $method, array $data, string $rootElement = null): BankResponse
    {
        $data = [
            'Username'     => $this->config['username'],
            'Password'     => $this->config['password'],
            'MessageID'    => (string)Str::uuid(),
            'APISigniture' => $method,
            'Data'         => $this->arrayToXml($rootElement ?? $method, $data),
        ];

        $httpClient = new HttpClient;

        try {
            $response = $httpClient->post($this->config['api_url'], ['form_params' => $data]);
        } catch (GuzzleException $e) {
            throw new BankConnectException;
        }

        if ($response->getStatusCode() !== 200) {
            throw new HttpException($response->getStatusCode(), $response->getReasonPhrase());
        }

        $headers = $response->getHeaders();

        $response = $this->xmlToArray($response->getBody()->getContents());

        EventLog::info('PFS', $method, [
            'endpoint'    => $this->config['api_url'],
            'form_params' => $data,
            'response'    => $response,
            'headers'     => $headers,
        ]);

        if ($response['ErrorCode'] !== self::RESPONSE_OK_CODE) {
            throw new BankRequestException($response['Description'], $response['ErrorCode'], $data);
        }

        return $this->response->create($response);
    }

    /**
     * @param string $method
     * @param array  $data
     * @return string
     */
    private function arrayToXml(string $method, array $data): string
    {
        return trim(str_replace('<?xml version="1.0"?>', '', ArrayToXml::convert($data, $method)));
    }

    /**
     * @param string $response
     * @return array
     */
    private function xmlToArray(string $response): array
    {
        $response = (array)simplexml_load_string($response);
        $response = (array)simplexml_load_string($response[0]);

        return json_decode(json_encode($response), true);
    }
}