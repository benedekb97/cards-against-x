<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\DeckImport;
use App\Entity\Enum\Role;
use App\Entity\UserInterface;
use App\Form\ImportDeckType;
use App\Message\ImportDeckMessage;
use App\Repository\DeckRepositoryInterface;
use App\Service\DeckImportFileUploaderServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeckController extends AbstractController
{
    public function __construct(
        private readonly DeckRepositoryInterface $deckRepository,
        private readonly DeckImportFileUploaderServiceInterface $deckImportFileUploaderService,
        private readonly MessageBusInterface $messageBus,
        private readonly EntityManagerInterface $entityManager
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
    public function viewImport(Request $request, int $importId): Response
    {
        // TODO: Add view import page
        dd($importId);
    }
}