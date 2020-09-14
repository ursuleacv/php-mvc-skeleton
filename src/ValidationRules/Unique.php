<?php
declare(strict_types=1);

namespace PhpMvcCore\ValidationRules;

use Envms\FluentPDO\Query;
use Sirius\Validation\Rule\AbstractRule;

class Unique extends AbstractRule
{
    public const MESSAGE = 'The {column} has already been taken';
    public const LABELED_MESSAGE = 'The {label} has already been taken';

    // These are for IDE
    public const OPTION_TABLE = 'table';
    public const OPTION_COLUMN = 'column';
    public const OPTION_EXCEPT = 'except';
    public const OPTION_ID_COLUMN = 'idColumn';

    /**
     * Default options
     * @var array
     */
    protected $options = [
        self::OPTION_TABLE => 'users',
        self::OPTION_COLUMN => 'email',
        self::OPTION_EXCEPT => null,
        self::OPTION_ID_COLUMN => 'id'
    ];

    /**
     * if you want to let the user pass the options as a CSV (eg: 'this,that')
     * you need to provide a `optionsIndexMap` property which will convert the options list
     * into an associative array of options
     * @var array
     */
    protected $optionsIndexMap = [
        0 => self::OPTION_TABLE,
        1 => self::OPTION_COLUMN,
        2 => self::OPTION_EXCEPT,
        3 => self::OPTION_ID_COLUMN
    ];

    public function validate($value, string $valueIdentifier = null): bool
    {
        $this->value = $value;
        $id = $this->getIdFromTable();

        if ($id) {
            $this->success = $id == $this->options[self::OPTION_EXCEPT];
        } else {
            $this->success = true;
        }
        return $this->success;
    }

    /**
     * @return int|null
     */
    private function getIdFromTable(): ?int
    {
        $r = \container(Query::class)->from($this->options[self::OPTION_TABLE])
            ->where($this->options[self::OPTION_COLUMN], $this->value)
            ->fetch();

        if ($r && isset($r['id'])) {
            return (int) $r['id'];
        }
        return null;
    }
}
