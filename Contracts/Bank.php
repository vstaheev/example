<?php

namespace App\Banks\Contracts;

/**
 * Class Bank
 * @package App\Banks\Contracts
 */
abstract class Bank
{
    protected BankResponse $response;
    protected array $config;

    /**
     * Bank constructor.
     * @param array $config
     * @param BankResponse $response
     */
    public function __construct(array $config, BankResponse $response)
    {
        $this->config = $config;
        $this->response = $response;
    }

    /**
     * Запрос на выпуск карты
     * @param array $data
     * @return BankResponse
     */
    abstract public function cardIssue(array $data): BankResponse;

    /**
     * Получить данные карты
     * @param array $data
     * @return BankResponse
     */
    abstract public function cardInquiry(array $data): BankResponse;

    /**
     * Обновить данные карты
     * @param array $data
     * @return BankResponse
     */
    abstract public function updateCard(array $data): BankResponse;

    /**
     * @param array $data
     * @return BankResponse
     */
    abstract public function depositToCard(array $data): BankResponse;

    /**
     * Транзакции
     * @param array $data
     * @return BankResponse
     */
    abstract public function viewStatement(array $data): BankResponse;

    /**
     * @param array $data
     * @return BankResponse
     */
    abstract public function getPin(array $data): BankResponse;

    /**
     * @param array $data
     * @return BankResponse
     */
    abstract public function upgradeCard(array $data): BankResponse;

    /**
     * @param array $data
     * @return BankResponse
     */
    abstract public function changeCardStatus(array $data): BankResponse;

    /**
     * @param array $data
     * @return BankResponse
     */
    abstract public function bankPayment(array $data): BankResponse;

    /**
     * @param array $data
     * @return BankResponse
     */
    abstract public function bankPaymentStatement(array $data): BankResponse;

    /**
     * @param array $data
     * @return BankResponse
     */
    abstract public function bankPaymentStatementById(array $data): BankResponse;
}