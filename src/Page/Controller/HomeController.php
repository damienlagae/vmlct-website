<?php

declare(strict_types=1);

namespace App\Page\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'home')]
final class HomeController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('@Page/home/index.html.twig');
    }
}
