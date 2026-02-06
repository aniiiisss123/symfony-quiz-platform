<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Exercice;
use App\Form\ReponseType;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/exercice/{id_e}/reponse')]
class ReponseController extends AbstractController
{
    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        ExerciceRepository $exerciceRepository,
        int $id_e
    ): Response {
        dump("Looking for exercise with ID: ".$id_e);
        
        $exercice = $exerciceRepository->findOneBy(['id_e' => $id_e]);
        
   ;

        if (!$exercice) {
            $allExercises = $exerciceRepository->findAll();
            $exerciseIds = array_map(fn($e) => $e->getId_e(), $allExercises);
            dump("Existing exercise IDs:", $exerciseIds);
            
            throw $this->createNotFoundException('Exercice non trouvé pour l\'ID '.$id_e. 
                ' (Existing IDs: '.implode(', ', $exerciseIds).')');
        }

        $reponse = new Reponse();
        $reponse->setExercice($exercice);
        $reponse->setEleve_id(2); 
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponseText = trim(strtolower($reponse->getReponse()));
            $correctAnswer = trim(strtolower($exercice->getCorrectAnswer()));
            
            $reponse->setNote_exercice(
                $reponseText === $correctAnswer ? $exercice->getScore() : 0
            );

            $entityManager->persist($reponse);
            $entityManager->flush();

            $this->addFlash('success', 'Réponse enregistrée avec succès!');
            
        
        }

        return $this->render('reponse/new.html.twig', [
            'form' => $form->createView(),
            'exercice' => $exercice
        ]);
    }
}