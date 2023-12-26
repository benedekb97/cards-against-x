<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\DeckImport;
use App\Entity\DeckImportInterface;
use App\Entity\DeckInterface;
use App\Entity\Enum\DeckType;
use App\Entity\Enum\ImportStatus;
use App\Entity\Enum\Role;
use App\Entity\UserInterface;
use App\Form\DeckType as DeckFormType;
use App\Form\ImportDeckType;
use App\Message\ImportDeckMessage;
use App\Repository\DeckImportRepositoryInterface;
use App\Repository\DeckRepositoryInterface;
use App\Service\DeckImportFileUploaderServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeckController extends AbstractController
{
    public function __construct(
        private readonly DeckRepositoryInterface $deckRepository,
        private readonly DeckImportFileUploaderServiceInterface $deckImportFileUploaderService,
        private readonly MessageBusInterface $messageBus,
        private readonly EntityManagerInterface $entityManager,
        private readonly DeckImportRepositoryInterface $deckImportRepository,
    ) {}

    #[Route('/decks', name: 'decks.index', methods: [Request::METHOD_GET])]
    #[IsGranted(Role::ROLE_USER->value)]
    public function index(): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $decks = $this->deckRepository->getDecksForUser($user);

        $form = $this->createForm(ImportDeckType::class, [], ['action' => $this->generateUrl('decks.import')]);

        return $this->render(
            'decks/index.html.twig',
            [
                'decks' => $decks,
                'importForm' => $form,
            ]
        );
    }

    #[Route('/decks/import', name: 'decks.import', methods: [Request::METHOD_POST])]
    #[IsGranted(Role::ROLE_USER->value)]
    public function import(Request $request): Response
    {
        $form = $this->createForm(ImportDeckType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $importFile */
            $importFile = $form->getData()['file'];

            $filePath = $this->deckImportFileUploaderService->upload($importFile);

            /** @var UserInterface $user */
            $user = $this->getUser();

            $deckImport = new DeckImport();

            $deckImport->setCreatedBy($user);
            $deckImport->setFilePath($filePath);

            $this->entityManager->persist($deckImport);
            $this->entityManager->flush();

            $this->messageBus->dispatch(new ImportDeckMessage($deckImport->getId()));

            return new RedirectResponse(
                $this->generateUrl('decks.import.view', ['importId' => $deckImport->getId()])
            );
        }

        return new RedirectResponse(
            $this->generateUrl('decks.index')
        );
    }

    #[Route('/decks/imports/{importId}', name: 'decks.import.view', methods: [Request::METHOD_GET])]
    #[IsGranted(Role::ROLE_USER->value)]
    public function viewImport(int $importId): Response
    {
        /** @var DeckImportInterface $deckImport */
        $deckImport = $this->deckImportRepository->find($importId);

        if (null === $deckImport) {
            throw new NotFoundHttpException();
        }

        if ($this->getUser() !== $deckImport->getCreatedBy()) {
            throw new NotFoundHttpException();
        }

        if ($deckImport->getStatus() === ImportStatus::IMPORTED) {
            return new RedirectResponse(
                $this->generateUrl('decks.view', ['deckId' => $deckImport->getDeck()->getId(), 'view' => 'general'])
            );
        }

        return $this->render('decks/deck_import.html.twig', [
            'deckImport' => $deckImport
        ]);
    }

    #[Route('/decks/{deckId}/{view}', name: 'decks.view')]
    public function view(int $deckId, string $view = 'general'): Response
    {
        /** @var DeckInterface $deck */
        $deck = $this->deckRepository->find($deckId);

        if (null === $deck) {
            throw new NotFoundHttpException();
        }

        if ($deck->getCreatedBy() !== $this->getUser() && $deck->getType() !== DeckType::PUBLIC) {
            throw new NotFoundHttpException();
        }

        if ('general' === $view) {
            $form = $this->createForm(DeckFormType::class, ['name' => $deck->getName(), 'type' => $deck->getType()]);
        }

        return $this->render(
            'decks/view.html.twig',
            [
                'deck' => $deck,
                'view' => $view,
                'form' => $form ?? null,
            ]
        );
    }
}