<?php

namespace App\Controller;

use App\Business\EventBusiness;
use App\Dto\EventDto;
use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/events', name: 'api_events')]
class EventController extends AbstractController
{
    #[Route('/current-year', name: 'current', methods: ['GET'])]
    public function getCurrentYearEvents(
        EventBusiness $eventBusiness
    ): Response
    {
        $events = $eventBusiness->getCurrentYearEvents();

        return $this->json($events, 200, [], ['groups' => ['event']]);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function getEvents(
        EventBusiness $eventBusiness,
        #[MapQueryParameter] ?int $page,
        #[MapQueryParameter] ?int $limit,
        #[MapQueryParameter] ?string $sort,
        #[MapQueryParameter] ?string $order,
        #[MapQueryParameter] ?string $name = null,
    ): JsonResponse
    {
        $events = $eventBusiness->getEvents(
            $page ?? 1,
            $limit ?? 50,
            $sort,
            $order,
            $name,
        );

        return $this->json($events, Response::HTTP_OK, [], ['groups' => ['event']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createEvent(
        EventBusiness $eventBusiness,
        Request $request,
        SerializerInterface $serializer,
        #[MapUploadedFile] UploadedFile|null $eventImage,
    ): Response
    {
        $eventDto = $request->request->get('event');
        $eventDto = $serializer->deserialize($eventDto, EventDto::class, 'json');

        $event = $eventBusiness->updateOrCreateEvent(null, $eventDto, $eventImage);

        return $this->json($event, Response::HTTP_CREATED, [], ['groups' => ['event']]);
    }

    #[Route('/{event}', name: 'update', methods: ['POST'])]
    public function updateEvent(
        EventBusiness $eventBusiness,
        Request $request,
        SerializerInterface $serializer,
        Event $event,
        #[MapUploadedFile] UploadedFile|null $eventImage,
    ): Response
    {
        $eventDto = $request->request->get('event');
        $eventDto = $serializer->deserialize($eventDto, EventDto::class, 'json');

        $event = $eventBusiness->updateOrCreateEvent($event, $eventDto, !empty($eventImage) ? $eventImage : null);

        return $this->json($event, Response::HTTP_OK, [], ['groups' => ['event']]);
    }

    #[Route('/{event}', name: 'delete', methods: ['DELETE'])]
    public function deleteEvent(
        EventBusiness $eventBusiness,
        Event $event
    ): Response
    {
        $eventBusiness->deleteEvent($event);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{event}/image', name: 'delete_image', methods: ['DELETE'])]
    public function deleteEventImage(
        EventBusiness $eventBusiness,
        Event $event
    ): Response
    {
        $eventBusiness->deleteEventImage($event);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
