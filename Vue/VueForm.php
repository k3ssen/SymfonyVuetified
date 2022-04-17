<?php
declare(strict_types=1);

namespace K3ssen\SymfonyVuetified\Vue;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * This VueForm class helps transform Symfony's {@see FormView} into a json-object that can be used by the
 * SvForm vue-component.
 */
class VueForm implements \JsonSerializable
{
    const MAPPED_TYPES = [
        'date' => 'SvDate',
        'collection' => 'SvCollection',
        'hidden' => 'SvHidden',
        'radio' => 'SvRadio',
        'checkbox' => 'SvCheckbox',
        'choice' => 'SvChoice',
        'text' => 'SvText',
        'textarea' => 'SvTextarea',
        'password' => 'SvPassword',
        'file' => 'SvFile',
        'range' => 'SvRange',
    ];

    /**
     * @var array
     */
    protected $vars;

    /**
     * @var VueForm[]
     */
    protected $children = [];

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
        if ($this->vars['data'] === null) {
            $this->vars['data'] = $this->vars['value'] ?? null;
        }
        $this->vars['label'] = $this->vars['label'] ?? ucfirst($this->vars['name']);
        $this->vars['errors'] = str_replace(['ERROR: ', "\n"], ['', ' / '], trim((string) $this->vars['errors'], "\n"));
        return [
            'vars' => $this->vars,
            'children' => $this->children,
            'rendered' => false,
        ];
    }

    protected function addMappedBlockPrefixes()
    {
        $initialPrefixes = $this->vars['block_prefixes'];
        foreach ($initialPrefixes as $index => $block_prefix) {
            if (key_exists($block_prefix, static::MAPPED_TYPES)) {
                array_splice( $this->vars['block_prefixes'], $index, 0, static::MAPPED_TYPES[$block_prefix]);
            }
        }
        if ($this->vars['expanded'] ?? false) {
            if ($this->vars['multiple'] === false) {
                array_splice( $this->vars['block_prefixes'], -1, 0, 'SvRadioGroup');
            } else {
                $this->vars['block_prefixes'][] = 'SvCheckboxGroup';
            }
        }
    }

    public function __toString()
    {
        return json_encode($this);
    }
}