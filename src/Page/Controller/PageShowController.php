<?php

declare(strict_types=1);

namespace App\Page\Controller;

use App\Page\Repository\PageRepository;
use App\Shared\Content\BlockSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Catch-all controller for dynamic pages. Registered with a very low priority
 * so that all module routes (news, team, sponsors, admin, ...) win — only
 * paths nothing else claims fall through here.
 *
 * The `path` requirement allows slashes so an editable `over/api`-style path
 * can resolve to a single Page row.
 */
final class PageShowController extends AbstractController
{
    #[Route(
        '/{path}',
        name: 'page_show',
        requirements: ['path' => '.+'],
        methods: ['GET'],
        priority: -100,
    )]
    public function __invoke(string $path, PageRepository $repository, BlockSerializer $serializer): Response
    {
        $page = $repository->findOnePublishedByPath($path);
        if (null === $page) {
            throw new NotFoundHttpException();
        }

        return $this->render('@Page/show.html.twig', [
            'page' => $page,
            'blocks' => $serializer->deserialize($page->getContent()),
        ]);
    }
}
