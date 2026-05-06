<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ClientRepository $clientRepository, InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'clients' => $clientRepository->findAll(),
            'invoices' => $invoiceRepository->findAll(),
        ]);
    }
}
