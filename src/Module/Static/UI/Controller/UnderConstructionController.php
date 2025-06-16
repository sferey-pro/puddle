<?php

declare(strict_types=1);

namespace App\Module\Static\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UnderConstructionController extends AbstractController
{
    public function __invoke(): Response
    {
        $this->addFlash('notice', 'Problème rencontré.');

        return $this->render('under-construction.html.twig');
    }
}
