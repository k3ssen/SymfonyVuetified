<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class JsonForm implements \JsonSerializable
{
    const MAPPED_TYPES = [
        'date' => 'DateType',
        'collection' => 'CollectionType',
    ];

    private array $vars;

    /** @var JsonForm[] */
    private array $children = [];

    public static function create(FormInterface $form): self
    {
        return new self($form->createView());
    }

    public function __construct(FormView $formView)
    {
        $this->vars = $formView->vars;
        $this->addMappedBlockPrefixes();

        foreach ($formView->children as $childName => $child) {
            $this->children[$childName] = new self($child);
        }
        if ($this->vars['prototype'] ?? false) {
            $this->vars['prototype'] = new self($this->vars['prototype']);
        }
    }

    public function jsonSerialize()
    {
        unset($this->vars['form']);
        if ($this->vars['compound']) {
            $this->vars['data'] = null;
            $this->vars['value'] = null;
        }
        if (!$this->vars['data'] && $this->vars['value']) {
            $this->vars['data'] = $this->vars['value'];
        }
        $this->vars['errors'] = (string) $this->vars['errors'];
        return [
            'vars' => $this->vars,
            'children' => $this->children,
            'rendered' => false,
        ];
    }

    private function addMappedBlockPrefixes()
    {
        $initialPrefixes = $this->vars['block_prefixes'];
        foreach ($initialPrefixes as $index => $block_prefix) {
            if (key_exists($block_prefix, static::MAPPED_TYPES)) {
                array_splice( $this->vars['block_prefixes'], $index, 0,  static::MAPPED_TYPES[$block_prefix]);
            }
        }
    }

    public function __toString()
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }
}