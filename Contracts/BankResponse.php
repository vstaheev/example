<?php

namespace App\Banks\Contracts;

use App\Banks\Collections\DataCollection;
use App\Banks\Fields\BankResponseFields;

/**
 * Class BankResponse
 * @package App\Banks\Contracts
 */
abstract class BankResponse
{
    protected DataCollection $data;

    protected BankResponseFields $fields;

    public function __construct(BankResponseFields $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function create(array $data): BankResponse
    {
        $this->data = new DataCollection($data);

        return $this;
    }

    public function getData(): DataCollection
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $result = $this->data->findByKeyRecursive($key);

        return is_array($result) && !$result ? null : $result;
    }

    abstract public function getCardholderId(): ?string;

    abstract public function getAvailableBalance(): float;

    abstract public function getLedgerBalance(): float;
}