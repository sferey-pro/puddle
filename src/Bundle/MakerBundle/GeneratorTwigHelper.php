<?php

declare(strict_types=1);

namespace App\Bundle\MakerBundle;

use Symfony\Bundle\MakerBundle\FileManager;

final class GeneratorTwigHelper
{
    public function __construct(
        private FileManager $fileManager,
    ) {
    }

    public function getEntityFieldPrintCode($entity, $field): string
    {
        $twigField = preg_replace_callback('/(?!^)_([a-z0-9])/', static fn ($s) => mb_strtoupper($s[1]), $field['fieldName']);
        $printCode = $entity.'.'.str_replace('_', '', $twigField);

        match ($field['type']) {
            'datetimetz_immutable', 'datetimetz' => $printCode .= ' ? '.$printCode.'|date(\'Y-m-d H:i:s T\') : \'\'',
            'datetime_immutable', 'datetime' => $printCode .= ' ? '.$printCode.'|date(\'Y-m-d H:i:s\') : \'\'',
            'dateinterval' => $printCode .= ' ? '.$printCode.'.format(\'%y year(s), %m month(s), %d day(s)\') : \'\'',
            'date_immutable', 'date' => $printCode .= ' ? '.$printCode.'|date(\'Y-m-d\') : \'\'',
            'time_immutable', 'time' => $printCode .= ' ? '.$printCode.'|date(\'H:i:s\') : \'\'',
            'json' => $printCode .= ' ? '.$printCode.'|json_encode : \'\'',
            'array' => $printCode .= ' ? '.$printCode.'|join(\', \') : \'\'',
            'boolean' => $printCode .= ' ? ux_icon(\'material-symbols:check-circle\') : ux_icon(\'material-symbols:check-cancel\')',
            default => $printCode,
        };

        return $printCode;
    }

    public function getHeadPrintCode($title): string
    {
        if ($this->fileManager->fileExists($this->fileManager->getPathForTemplate('layout.html.twig'))) {
            return <<<TWIG
                {% extends 'layout.html.twig' %}

                {% block title %}$title{% endblock %}

                TWIG;
        }

        return <<<HTML
            <!DOCTYPE html>

            <title>$title</title>

            HTML;
    }
}
