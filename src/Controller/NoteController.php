<?php

// src/Controller/NoteController.php
namespace App\Controller;

use App\Entity\Note;
use App\Entity\Quiz;
use App\Repository\NoteRepository;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/quiz/{quizId}/note')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_index', methods: ['GET'])]
    public function index(Quiz $quiz, NoteRepository $noteRepository): Response
    {
        return $this->render('note/index.html.twig', [
            'notes' => $noteRepository->findBy(['quiz' => $quiz]),
            'quiz' => $quiz
        ]);
    }

    #[Route('/calculate', name: 'app_note_calculate', methods: ['POST'])]
    public function calculate(
        Quiz $quiz,
        ReponseRepository $reponseRepository,
        NoteRepository $noteRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $eleveId = 2; 
        $noteTotale = $noteRepository->calculateTotalNote($eleveId, $quiz->getQuiz_id());

        $note = $noteRepository->findNoteForQuiz($eleveId, $quiz->getQuiz_id());
        
        if (!$note) {
            $note = new Note();
            $note->setQuiz($quiz);
            $note->setEleve_id($eleveId);
        }

        $note->setNote_totale($noteTotale);
        
        $entityManager->persist($note);
        $entityManager->flush();

        return $this->render('note/show.html.twig', [
            'note' => $note,
            'quiz' => $quiz
        ]);
    }
}