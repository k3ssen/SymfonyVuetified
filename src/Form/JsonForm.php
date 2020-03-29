<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class JsonForm implements \JsonSerializable
{
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
        unset($this->vars['form']);
        foreach ($formView->children as $childName => $child) {
            $this->children[$childName] = new self($child);
        }
    }

    public function jsonSerialize()
    {
        $this->vars['errors'] = (string) $this->vars['errors'];

        return [
            'vars' => $this->vars,
            'children' => $this->children,
            'rendered' => false,
        ];
    }

    public function __toString()
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }
}