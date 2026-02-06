<?php
namespace App\Controller;

use App\Entity\Exercice;
use App\Entity\Quiz;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/quiz/{quiz_id}/exercice')]
class ExerciceController extends AbstractController
{
    #[Route('/', name: 'app_exercice_index', methods: ['GET'])]
    public function index(Quiz $quiz, ExerciceRepository $exerciceRepository): Response
    {
        return $this->render('exercice/index.html.twig', [
            'quiz' => $quiz,
            'exercices' => $exerciceRepository->findBy(['quiz_id' => $quiz])
        ]);
    }

   
    #[Route('/new', name: 'app_exercice_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        Quiz $quiz,
        SluggerInterface $slugger
    ): Response {
        $exercice = new Exercice();
        $exercice->setQuiz_Id($quiz);
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imagePath')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
    
                $uploadDir = 'C:\\Users\\Saidi\\Desktop\\QuizPi\\public\\assets\\uploads';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // Create the directory with write permissions
                }
    
                try {
                    $imageFile->move($uploadDir, $newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Failed to upload file: ' . $e->getMessage());
                    return $this->redirectToRoute('app_exercice_new', ['quiz_id' => $quiz->getQuiz_Id()]);
                }
    
                $exercice->setImagePath($newFilename);
            }
    
            $entityManager->persist($exercice);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_exercice_index', [
                'quiz_id' => $quiz->getQuiz_Id(),
            ]);
        }
    
        return $this->render('exercice/new.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView(),
            'quiz' => $quiz,
        ]);
    }
    #[Route('/{id_e}', name: 'app_exercice_show', methods: ['GET'])]
    public function show(exercice $exercice): Response
    {
        $imagePath = 'assets/uploads/' . $exercice->getImagePath();
                return $this->render('exercice/show.html.twig', [
            'exercice' => $exercice,
            'imagePath' => $imagePath,  
        ]);
    }


    #[Route('/{id_e}/edit', name: 'app_exercice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Exercice $exercice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_exercice_index', [
                'quiz_id' => $exercice->getQuiz_id()->getQuiz_id()
            ]);
        }

        return $this->render('exercice/edit.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView(),
            'quiz' => $exercice->getQuiz_id()
        ]);
    }

    #[Route('/{id_e}', name: 'app_exercice_delete', methods: ['POST'])]
    public function delete(Request $request, Exercice $exercice, EntityManagerInterface $entityManager): Response
    {
        $quizId = $exercice->getQuiz_id()->getQuiz_id();
        
        if ($this->isCsrfTokenValid('delete'.$exercice->getId_e(), $request->request->get('_token'))) {
            $entityManager->remove($exercice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_exercice_index', [
            'quiz_id' => $quizId
        ]);
    }
}