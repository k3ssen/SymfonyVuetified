<?php
declare(strict_types=1);

namespace App\Vue;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class VueForm implements \JsonSerializable
{
    const MAPPED_TYPES = [
        'date' => 'DateType',
        'collection' => 'CollectionType',
        'hidden' => 'HiddenType',
        'radio' => 'RadioType',
        'checkbox' => 'CheckboxType',
        'choice' => 'ChoiceType',
        'text' => 'TextType',
        'textarea' => 'TextareaType',
        'password' => 'PasswordType',
        'file' => 'FileType',
//        'vich_image' => 'WysiwygType',
    ];

    /**
     * @var array
     */
    private $vars;

    /** @var VueForm[] */
    private $children = [];

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
        if ($this->vars['compound']) {
            $this->vars['data'] = null;
            $this->vars['value'] = null;
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
                array_splice( $this->vars['block_prefixes'], $index, 0, static::MAPPED_TYPES[$block_prefix]);
            }
        }
        if ($this->vars['expanded'] ?? false) {
            if ($this->vars['multiple'] === false) {
                array_splice( $this->vars['block_prefixes'], -1, 0, 'RadioGroupType');
            } else {
                $this->vars['block_prefixes'][] = 'CheckboxGroupType';
            }
        }
    }

    public function __toString()
    {
        return json_encode($this);
    }
}