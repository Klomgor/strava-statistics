<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Strava\InsufficientStravaAccessTokenScopes;
use App\Domain\Strava\InvalidStravaAccessToken;
use App\Domain\Strava\Strava;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[AsController]
final readonly class AppRequestHandler
{
    public function __construct(
        private FilesystemOperator $buildStorage,
        private Strava $strava,
        private Environment $twig,
    ) {
    }

    #[Route(path: '/{wildcard?}', requirements: ['wildcard' => '.*'], methods: ['GET'], priority: -10)]
    public function handle(Request $request): Response
    {
        if ($this->buildStorage->fileExists('index.html')) {
            return new Response($this->buildStorage->read('index.html'), Response::HTTP_OK);
        }

        try {
            $this->strava->verifyAccessToken();
        } catch (InvalidStravaAccessToken|InsufficientStravaAccessTokenScopes) {
            // Refresh token has not been set up properly or does not have the required scopes, initialize authorization flow.
            return new RedirectResponse('/strava-oauth', Response::HTTP_FOUND);
        }

        return new Response($this->twig->render('html/setup.html.twig'), Response::HTTP_OK);
    }
}
