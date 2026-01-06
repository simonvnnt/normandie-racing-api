<?php

namespace App\Business;

use App\Dto\EventDto;
use App\Entity\Event;
use App\Helper\FileHelper;
use App\Repository\EventRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class EventBusiness
{
    public function __construct(
        private EventRepository $eventRepository,
        private FileHelper $fileHelper,
        private EntityManagerInterface $em
    )
    {}

    /**
     * @return Event[]
     */
    public function getCurrentYearEvents(): array
    {
        $now = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $currentYear = $now->format('Y');

        return $this->eventRepository->findByYear($currentYear);
    }

    public function getEvents(
        int     $page = 1,
        int     $limit = 50,
        ?string $sort = null,
        ?string $order = null,
        ?string $name = null
    ): array
    {
        $eventsPaginated = $this->eventRepository->findPaginated(
            $page,
            $limit,
            $sort,
            $order,
            $name
        );

        return [
            'pagination' => [
                'totalItems' => $eventsPaginated['total'],
                'pageIndex' => $page,
                'itemsPerPage' => $limit
            ],
            'events' => $eventsPaginated['items']
        ];
    }

    public function updateOrCreateEvent(?Event $event, EventDto $eventDto, ?UploadedFile $eventImage): Event
    {
        if ($event === null) {
            $event = new Event();
        }

        $event->setName($eventDto->name);
        $event->setFromDate(new DateTime($eventDto->fromDate));
        $event->setToDate(new DateTime($eventDto->toDate));

        if ($eventImage !== null) {
            $filename = $this->fileHelper->normalizeFilename($event->getName());
            $eventImage = $this->fileHelper->saveFile($eventImage, 'events/', $filename . '.' . $eventImage->getClientOriginalExtension());

            $event->setImagePath($eventImage->getPathname());
        }

        $this->em->persist($event);
        $this->em->flush();

        return $event;
    }

    public function deleteEvent(Event $event): void
    {
        $this->fileHelper->deleteFile($event->getImagePath());

        $this->em->remove($event);
        $this->em->flush();
    }
}
