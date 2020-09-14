<?php
declare(strict_types=1);

namespace PhpMvcCore\ValidationRules;

use Envms\FluentPDO\Query;
use Sirius\Validation\Rule\AbstractRule;

class Exists extends AbstractRule
{
    public const MESSAGE = 'The selected {column} is invalid.';
    public const LABELED_MESSAGE = 'The selected {label} is invalid.';

    public const OPTION_TABLE = 'table';
    public const OPTION_COLUMN = 'column';

    /**
     * @var array
     */
    protected $options = [
        self::OPTION_TABLE => 'users',
        self::OPTION_COLUMN => 'id'
    ];

    /**
     * @var array
     */
    protected $optionsIndexMap = [
        0 => self::OPTION_TABLE,
        1 => self::OPTION_COLUMN
    ];

    public function validate($value, string $valueIdentifier = null): bool
    {
        $this->value = $value;
        return $this->success = $this->verifyIfColumnExist();
    }

    /**
     * @return bool
     */
    private function verifyIfColumnExist(): bool
    {
        $result = \container(Query::class)->from($this->options[self::OPTION_TABLE])
            ->where($this->options[self::OPTION_COLUMN], $this->value)
            ->select($this->options[self::OPTION_COLUMN])
            ->fetch();
        return !empty($result);
    }
}
