<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/clients', name: 'app_client_')]
final class ClientController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ClientRepository $clientRepository): Response
    {
        $clients = $clientRepository->findBy(['owner' => $this->getUser()]);

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setOwner($this->getUser());
            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Client créé avec succès.');

            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/new.html.twig', [
            'form' => $form,
        ]);
    }
}
