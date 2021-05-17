<?php

namespace App\Banks\Clients;

use App\Banks\Contracts\Bank;
use App\Banks\Contracts\BankResponse;

/**
 * Class PfsBankClient
 * @package App\Banks\Clients
 */
class PfsBankClientMock extends Bank
{
    public const RESPONSE_OK_CODE = '0000';

    public function cardIssue(array $data): BankResponse
    {
        return $this->sendRequest('CardIssue', [
            'Cardholderid' => '500000000006',
            'AvailableBalance' => 0,
            'LedgerBalance' => 0,
        ]);
    }

    public function depositToCard(array $data): BankResponse
    {
        return $this->sendRequest('DepositToCard', [
            'ReferenceID' => time(),
        ]);
    }

    public function cardInquiry(array $data): BankResponse
    {
        return $this->sendRequest('CardInquiry', [
            'cardinfo' => [
                'AccountBaseCurrency' => 826,
                'CardType' => 20,
                'AccountNumber' => '717949358724',
                'CardStatus' => 0,
                'PinTriesExceeded' => 0,
                'BadPinTries' => 0,
                'ExpirationDate' => 2106,
                'Client' => '',
                'PhonecardNumber' => '',
                'AvailBal' => '000000012200',
                'LedgerBal' => '000000012200',
                'DistributorCode' => 1873,
                'LoadAmount' => '000000000',
                'CompanyName' => '',
                'CardStyle' => '01',
                'DeliveryType' => 'VC',
                'SortCode' => '13-72-24',
                'SortCodeAccountNumber' => '00621674',
                'Bic' => 'PFSRIE21',
                'Iban' => 'IE09PFSR99107000628841',
            ],
            'cardholder' => [
                'FirstName' => 'WALTER',
                'MiddleInitial' => '',
                'LastName' => 'HARDING',
                'Address1' => 'FLAT 1',
                'Address2' => 'ISLE OF WIGHT',
                'City' => 'N/A',
                'State' => '',
                'Zip' => 'PO38 1LR',
                'CountryCode' => 'GB',
                'Phone' => '5664376',
                'DOB' => '05081953',
                'CardHolderID' => '400000625514',
                'CardNumber' => '599911******5656',
            ],
        ]);
    }

    public function updateCard(array $data): BankResponse
    {
        return $this->sendRequest('UpdateCard');
    }

    /**
     * @param  string  $method
     * @param  array  $data
     * @return BankResponse
     */
    protected function sendRequest(string $method, array $data = []): BankResponse
    {
        return $this->response->create([
            'ErrorCode' => self::RESPONSE_OK_CODE,
            'Description' => $method . ' successful',
            $method => $data,
        ]);
    }
}