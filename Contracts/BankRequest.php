<?php

namespace App\Banks\Contracts;

use App\Banks\Fields\BankRequestFields;
use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Class BankRequest
 * @package App\Banks\Contracts
 */
abstract class BankRequest
{
    protected const TRUE = true;
    protected const FALSE = false;
    protected const NULL = null;
    protected const DATE_FORMAT = null;

    protected array $data;
    protected BankRequestFields $fields;

    /**
     * BankRequest constructor.
     * @param BankRequestFields $fields
     */
    public function __construct(BankRequestFields $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->data;
    }

    /**
     * Clear $data
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments): BankRequest
    {
        if (substr($name, 0, 3) === 'set') {
            $attr = Str::replaceFirst('set', '', $name);

            $constants = $this->fields->list();
            $nameToConst = Str::upper(Str::snake($attr));

            $value = $arguments[0];

            if (is_bool($value)) {
                $value = $value ? static::TRUE : static::FALSE;
            }

            if (is_null($value)) {
                $value = static::NULL;
            }

            if ($value instanceof Carbon) {
                $formatMethodName = 'get' . $attr . 'Format';

                if (method_exists($this, $formatMethodName)) {
                    $value = $value->format($this->$formatMethodName());
                } elseif (static::DATE_FORMAT) {
                    $value = $value->format(static::DATE_FORMAT);
                }
            }

            $this->data[$constants[$nameToConst] ?? $attr] = $value;

            return $this;
        }

        throw new BadMethodCallException('Call to undefined method ' . static::class . '::' . $name . '()');
    }
}