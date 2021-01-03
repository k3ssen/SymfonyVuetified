<?php

namespace App\Maker;

use Symfony\Bundle\MakerBundle\Generator as BaseGenerator;

/**
 * Overrides the Symfony\Bundle\MakerBundle\Generator service to use custom templates.
 */
class Generator extends BaseGenerator
{
    public function __construct(BaseGenerator $generator)
    {
        parent::__construct(
            $this->getProp($generator, 'fileManager'),
            $this->getProp($generator, 'namespacePrefix'),
            $this->getProp($generator, 'phpCompatUtil')
        );
    }

    private function getProp(BaseGenerator $generator, string $propertyName) {
        $reflectionProperty = new \ReflectionProperty($generator, $propertyName);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($generator);
    }

    public function generateTemplate(string $targetPath, string $templateName, array $variables = [])
    {
        $templatePath = __DIR__.'/../../templates/generator/' . $templateName;
        if (file_exists($templatePath)) {
            $templateName = $templatePath;
        }
        parent::generateTemplate($targetPath, $templateName, $variables);
    }
}