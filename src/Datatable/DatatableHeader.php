<?php
declare(strict_types=1);

namespace App\Datatable;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DatatableHeader implements \JsonSerializable
{
    public const ALIGN_START = 'start';
    public const ALIGN_CENTER = 'center';
    public const ALIGN_END = 'end';

    public const SEARCH_EQUALS = 'equals';  // results in '= :search' comparison
    public const SEARCH_CONTAINS = 'contains'; // results in 'LIKE "%:search%"'
    public const SEARCH_STARTS_WITH = 'starts_with'; // results in 'LIKE ":search%"'
    public const SEARCH_ENDS_WITH = 'ends_with'; // results in 'LIKE "%:search"'

    /**
     * @var string|null
     */
    private $field;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $text;

    /**
     * @var boolean
     */
    private $sortable = true;

    /**
     * @var boolean
     */
    private $divider = false;

    /**
     * @var string
     */
    private $align = 'left';

    /**
     * @var string|null
     */
    private $width;

    /**
     * @var string|null
     */
    private $search = self::SEARCH_CONTAINS;

    /**
     * @var string|null
     */
    private $dql = null;

    public function __construct($fieldName, array $options = [])
    {
        $this->field = $fieldName;
        $this->value = $fieldName;
        $accessor = new PropertyAccessor();
        foreach ($options as $key => $value) {
            $accessor->setValue($this, $key, $value);
        }
    }

    public static function getAlignChoices(): array
    {
        return [
            static::ALIGN_START,
            static::ALIGN_CENTER,
            static::ALIGN_END,
        ];
    }

    public static function getSearchChoices(): array
    {
        return [
            static::SEARCH_EQUALS,
            static::SEARCH_CONTAINS,
            static::SEARCH_STARTS_WITH,
            static::SEARCH_ENDS_WITH,
        ];
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function isSortable(): ?bool
    {
        return $this->sortable;
    }

    public function setSortable(bool $sortable): self
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function isDivider(): ?bool
    {
        return $this->divider;
    }

    public function setDivider(bool $divider): self
    {
        $this->divider = $divider;
        return $this;
    }

    public function getAlign(): ?string
    {
        return $this->align;
    }

    public function setAlign(?string $align): self
    {
        if ($align && !in_array($align, static::getAlignChoices())) {
            throw new \InvalidArgumentException('Invalid align value "%s". Valid options are: %s', $align, implode(', ', static::getAlignChoices()));
        }
        $this->align = $align;
        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(?string $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): self
    {
        if ($search && !in_array($search, static::getSearchChoices(), true)) {
            throw new \InvalidArgumentException(sprintf(
                'Search option "%s" is not valid. Valid options are: %s',
                $search,
                implode(', ', static::getSearchChoices())
            ));
        }
        $this->search = $search;
        return $this;
    }

    public function geDql(): ?string
    {
        return $this->dql;
    }

    public function setDql(?string $dql): self
    {
        $this->dql = $dql;
        $this->search = null; // TODO: support search for dql
        return $this;
    }

    public function getSearchComparison(string $alias, $value): ?Comparison
    {
        $field = $alias . '.'. $this->getField();
        switch ($this->search) {
            case static::SEARCH_EQUALS:
                return Criteria::expr()->eq($field, $value);
                break;
            case static::SEARCH_CONTAINS:
                return Criteria::expr()->contains($field, $value);
                break;
            case static::SEARCH_STARTS_WITH:
                return Criteria::expr()->startsWith($field, $value);
                break;
            case static::SEARCH_ENDS_WITH:
                return Criteria::expr()->endsWith($field, $value);
                break;
        }
        return null;
    }

    public function jsonSerialize()
    {
        $headerInfo = [];
        foreach ($this as $key => $value) {
            if ($value) {
                $headerInfo[$key] = $value;
            }
        }
        return $headerInfo;
    }
}