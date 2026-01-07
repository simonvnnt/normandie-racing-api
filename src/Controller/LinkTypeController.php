<?php

namespace App\Controller;

use App\Repository\LinkTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/link-types', name: 'api_link_types_')]
class LinkTypeController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function getLinkTypes(
        LinkTypeRepository $linkTypeRepository
    ): Response
    {
        $linkTypes = $linkTypeRepository->findAll();

        return $this->json($linkTypes, Response::HTTP_OK, [], ['groups' => ['linkType']]);
    }
}
