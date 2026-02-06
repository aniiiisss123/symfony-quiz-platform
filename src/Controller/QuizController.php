<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Note;
use App\Entity\Reponse;
use App\Entity\Exercice;


use App\Form\QuizType;
use App\Repository\QuizRepository;
use App\Repository\NoteRepository;
use App\Repository\ExerciceRepository;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Route('/quiz')]
class QuizController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index1(): Response
    {
        return $this->render('/base.html.twig', [
            'controller_name' => 'QuizController',
        ]);
    }
    
    #[Route('/', name: 'app_quiz_index', methods: ['GET'])]
    public function index(Request $request, QuizRepository $quizRepository): Response
    {
        $search = $request->query->get('search', '');
    
        if ($search) {
            $quizzes = $quizRepository->createQueryBuilder('q')
                ->where('LOWER(q.title) LIKE :search')
                ->setParameter('search', '%' . strtolower($search) . '%')
                ->getQuery()
                ->getResult();
        } else {
            $quizzes = $quizRepository->findAll();
        }
    
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }
    #[Route('/new', name: 'app_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && !$form->isValid()) {
        }
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quiz);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_quiz_index');
        }
    
        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
            'is_new' => true,
        ]);
    }
    #[Route('/{quiz_id}', name: 'app_quiz_show', methods: ['GET'])]
    public function show(Quiz $quiz): Response
    {
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

  
    #[Route('/{quiz_id}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
            'is_new' => false, 
        ]);
    }

#[Route('/{quiz_id}', name: 'app_quiz_delete', methods: ['POST'])]
public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$quiz->getQuiz_Id(), $request->request->get('_token'))) {
        foreach ($quiz->getExercices() as $exercice) {
            $entityManager->remove($exercice);
        }
        
       
        $entityManager->remove($quiz);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
}
#[Route('/eleve/quizzes', name: 'app_quiz_list')]
public function list(QuizRepository $quizRepository, NoteRepository $noteRepository, ExerciceRepository $exerciceRepository): Response
{
    $eleveId = 1; 
    $quizzes = $quizRepository->findAll();

    $notes = [];
    $totalScores = []; 
    $passedQuizCount = 0; 
    $totalPassedScore = 0; 
    $studentScoreSum = 0; 
    $totalScoresSum = 0; 

    foreach ($quizzes as $quiz) {
        $note = $noteRepository->findOneBy(['eleve_id' => $eleveId, 'quiz' => $quiz->getQuiz_Id()]);
        $notes[$quiz->getQuiz_Id()] = $note;

        $totalExerciseScore = array_reduce(
            $exerciceRepository->findBy(['quiz_id' => $quiz->getQuiz_Id()]),
            fn($carry, $exercise) => $carry + $exercise->getScore(),
            0
        );
        $totalScores[$quiz->getQuiz_Id()] = $totalExerciseScore;

        $totalScoresSum += $totalExerciseScore;

        if ($note) {
            $studentScoreSum += $note->getNote_totale();
        }

        if ($note && $note->getNote_totale() >= ($totalExerciseScore / 2)) { 
            $passedQuizCount++;
            $totalPassedScore += $note->getNote_totale();
        }
    }

    $percentage = $totalScoresSum > 0 ? ($studentScoreSum * 100 / $totalScoresSum) : 0;

    $averageScore = $passedQuizCount > 0 ? $totalPassedScore / $passedQuizCount : 0;

    return $this->render('quiz_list.html.twig', [
        'quizzes' => $quizzes,
        'notes' => $notes,
        'totalScores' => $totalScores, 
        'passedQuizCount' => $passedQuizCount, 
        'averageScore' => $averageScore, 
        'percentage' => $percentage, 
    ]);
}
#[Route('/quiz/{quiz_id}/take', name: 'app_quiz_take')]
public function takeQuiz(
    Quiz $quiz, 
    Request $request, 
    EntityManagerInterface $em,
    NoteRepository $noteRepository
): Response {
    $form = $this->createFormBuilder()->getForm();
    $form->handleRequest($request);

    $feedback = null;
    $corrections = [];
    $totalScore = null;
    $requiredScore = $quiz->getTotalscore();
    $eleveId = 1; 
    $timeSpent = $request->request->get('timeSpent', null); // Retrieve time spent from the form data

    if ($form->isSubmitted() && $form->isValid()) {
        $responses = $request->request->all()['responses'] ?? [];

        foreach ($responses as $exerciceId => $reponseText) {
            $exercice = $em->getRepository(Exercice::class)->find($exerciceId);
            if ($exercice) {
                $isCorrect = strtolower(trim($reponseText)) === strtolower(trim($exercice->getCorrectAnswer()));
                $score = $isCorrect ? $exercice->getScore() : 0;
                $totalScore += $score;

                $reponse = new Reponse();
                $reponse->setExercice($exercice);
                $reponse->setEleve_id($eleveId);
                $reponse->setReponse($reponseText);
                $reponse->setNote_Exercice($score);
                $em->persist($reponse);

                $corrections[] = [
                    'question' => $exercice->getQuestion(),
                    'correctAnswer' => $exercice->getCorrectAnswer(),
                    'studentAnswer' => $reponseText,
                    'isCorrect' => $isCorrect,
                ];
            }
        }

        if ($totalScore >= $requiredScore) {
            $feedback = 'Très bien ! Vous avez atteint la note requise.';
        } elseif ($totalScore < $requiredScore / 2) {
            $feedback = 'Votre score est très inférieur à la note requise. Consultez les corrections pour mieux comprendre vos erreurs.';
        } else {
            $feedback = 'Bon effort, mais vous n\'avez pas atteint la note requise. Consultez les corrections pour améliorer.';
        }

        $note = $noteRepository->findOneBy([
            'eleve_id' => $eleveId,
            'quiz' => $quiz->getQuiz_id()
        ]);
        if (!$note) {
            $note = new Note();
            $note->setQuiz($quiz);
            $note->setEleve_id($eleveId);
        }
        $note->setNote_totale($totalScore);
        $note->setTimeSpent($timeSpent); // Save time spent in the Note entity
        $em->persist($note);
        $em->flush();
    }

    return $this->render('quiz_take.html.twig', [
        'quiz' => $quiz,
        'exercises' => $quiz->getExercices(),
        'form' => $form->createView(),
        'duration' => $quiz->getDuration(),
        'feedback' => $feedback,
        'corrections' => $corrections,
        'totalScore' => $totalScore,
        'requiredScore' => $requiredScore,
        'timeSpent' => $timeSpent,
    ]);
}
#[Route('formateur/quizs/stats', name: 'formateur_quizs_stats', methods: ['GET'])]
public function formateurStats(
    QuizRepository $quizRepository,
    NoteRepository $noteRepository,
    ExerciceRepository $exerciceRepository
): Response {
    $quizzes = $quizRepository->findAll();

    $quizs = [];
    $totalQuizzes = count($quizzes);
    $totalExercises = 0;
    $totalStudents = 0;
    $averageScores = [];

    foreach ($quizzes as $quiz) {
        $notes = $noteRepository->findBy(['quiz' => $quiz->getQuiz_Id()]);
        $exercises = $exerciceRepository->findBy(['quiz_id' => $quiz->getQuiz_Id()]);

        $totalGrades = array_reduce($notes, fn($carry, $note) => $carry + $note->getNote_totale(), 0);
        $averageScore = count($notes) > 0 ? $totalGrades / count($notes) : 0;
        $highestScore = count($notes) > 0 ? max(array_map(fn($note) => $note->getNote_totale(), $notes)) : 0;
        $quizTotalScore = array_reduce($exercises, fn($carry, $exercise) => $carry + $exercise->getScore(), 0);

        $totalExercises += count($exercises);
        $totalStudents += count($notes);
        $averageScores[] = $averageScore;

        $quizs[] = [
            'title' => $quiz->getTitle(),
            'date' => $quiz->getCreationDate(),
            'total_students' => count($notes),
            'average_score' => $averageScore,
            'total_score' => $quizTotalScore,
            'highest_score' => $highestScore,
        ];
    }

    $overallAverageScore = $totalQuizzes > 0 ? array_sum($averageScores) / $totalQuizzes : 0;

    $topQuiz = null;
    $lowQuiz = null;
    $mostStudentsQuiz = null;

    if (!empty($quizs)) {
        usort($quizs, fn($a, $b) => $b['average_score'] <=> $a['average_score']);
        $topQuiz = $quizs[0];

        usort($quizs, fn($a, $b) => $a['average_score'] <=> $b['average_score']);
        $lowQuiz = $quizs[0];

        usort($quizs, fn($a, $b) => $b['total_students'] <=> $a['total_students']);
        $mostStudentsQuiz = $quizs[0];
    }

    return $this->render('quiz_formateur.html.twig', [
        'totalQuizzes' => $totalQuizzes,
        'totalExercises' => $totalExercises,
        'averageScore' => $overallAverageScore,
        'totalStudents' => $totalStudents,
        'quizs' => $quizs,
        'topQuiz' => $topQuiz,
        'lowQuiz' => $lowQuiz,
        'mostStudentsQuiz' => $mostStudentsQuiz,
    ]);
}
#[Route('/admin/quizs', name: 'admin_quizs')]
public function adminindex(QuizRepository $quizRepository, ExerciceRepository $exerciceRepository, NoteRepository $noteRepository): Response
{
    $quizs = $quizRepository->findAll();
    
    $totalExercises = $exerciceRepository->createQueryBuilder('e')
        ->select('COUNT(e.id_e)')
        ->getQuery()
        ->getSingleScalarResult();
    
    $averageScore = $noteRepository->createQueryBuilder('n')
        ->select('AVG(n.note_totale)')
        ->getQuery()
        ->getSingleScalarResult();
    
    $recentQuizs = $quizRepository->createQueryBuilder('q')
        ->select('COUNT(q.quiz_id)')
        ->where('q.creationdate >= :date')
        ->setParameter('date', new \DateTime('-7 days'))
        ->getQuery()
        ->getSingleScalarResult();
    
    return $this->render('quiz_management.html.twig', [
        'quizs' => $quizs,
        'totalExercises' => $totalExercises,
        'averageScore' => $averageScore ?? 0,
        'recentQuizs' => $recentQuizs,
    ]);
}
#[Route('/formateur/quizs/stats/export', name: 'export_quiz_stats', methods: ['GET'])]
public function exportStatsToExcel(
    QuizRepository $quizRepository,
    NoteRepository $noteRepository,
    ExerciceRepository $exerciceRepository
): Response {
    $quizzes = $quizRepository->findAll();
    
    $data = [];
    $headers = [
        'Quiz Title', 
        'Date', 
        'Total Students', 
        'Average Score', 
        'Total Score', 
        'Highest Score'
    ];
    
    foreach ($quizzes as $quiz) {
        $notes = $noteRepository->findBy(['quiz' => $quiz->getQuiz_Id()]);
        $exercises = $exerciceRepository->findBy(['quiz_id' => $quiz->getQuiz_Id()]);
        
        $totalGrades = array_reduce($notes, fn($carry, $note) => $carry + $note->getNote_totale(), 0);
        $averageScore = count($notes) > 0 ? $totalGrades / count($notes) : 0;
        $highestScore = count($notes) > 0 ? max(array_map(fn($note) => $note->getNote_totale(), $notes)) : 0;
        $quizTotalScore = array_reduce($exercises, fn($carry, $exercise) => $carry + $exercise->getScore(), 0);
        
        $data[] = [
            $quiz->getTitle(),
            $quiz->getCreationDate()->format('Y-m-d'),
            count($notes),
            $averageScore,
            $quizTotalScore,
            $highestScore
        ];
    }
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $sheet->fromArray([$headers], null, 'A1');
    
    $sheet->fromArray($data, null, 'A2');
    
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
    
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    $writer = new Xlsx($spreadsheet);
    
    $response = new StreamedResponse(
        function () use ($writer) {
            $writer->save('php://output');
        }
    );
    
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', 'attachment;filename="quiz_statistics.xlsx"');
    $response->headers->set('Cache-Control', 'max-age=0');
    
    return $response;
}
}
