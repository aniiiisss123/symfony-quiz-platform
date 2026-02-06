<?php 
// src/Controller/SignLanguageController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SignLanguageController extends AbstractController
{
    #[Route('/sign/generate', name: 'app_sign_generate', methods: ['GET', 'POST'])]
    public function generate(Request $request, HttpClientInterface $http): Response
    {
        $prompt = $request->request->get('prompt', '');
        $imageUrl = null;
        $error = null;

        if ($request->isMethod('POST')) {
            try {
                $cleanPrompt = strtolower(trim($prompt));
                
                if (empty($cleanPrompt)) {
                    throw new \Exception('Please enter a word or letter');
                }

                // 1. First try: SpreadTheSign API (real ASL videos)
                $response = $http->request(
                    'GET',
                    'https://spreadthesign.com/api/v4/search/',
                    [
                        'query' => [
                            'q' => $cleanPrompt,
                            'lang' => 'us' // US Sign Language
                        ]
                    ]
                );

                $data = $response->toArray();
                
                if (!empty($data['results'])) {
                    $imageUrl = $data['results'][0]['video']['url'];
                }
                // 2. Fallback: ASL Alphabet Images
                else {
                    $aslAlphabet = [
                        'hello' => 'https://www.lifeprint.com/asl101/signjpegs/h/hello.jpg',
                        'yes' => 'https://www.lifeprint.com/asl101/signjpegs/y/yes.jpg',
                        'no' => 'https://www.lifeprint.com/asl101/signjpegs/n/no.jpg',
                        // Add more at: https://www.lifeprint.com/asl101/pages-layout/concepts.htm
                    ];
                    
                    $imageUrl = $aslAlphabet[$cleanPrompt] ?? null;
                    
                    if (!$imageUrl && strlen($cleanPrompt) === 1) {
                        $imageUrl = "https://www.lifeprint.com/asl101/signjpegs/".$cleanPrompt[0]."/".$cleanPrompt[0].".jpg";
                    }
                }

                if (!$imageUrl) {
                    throw new \Exception('Sign not found. Try: Hello, Yes, No, or single letters A-Z');
                }

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return $this->render('sign.html.twig', [
            'imageUrl' => $imageUrl,
            'prompt' => $cleanPrompt ?? $prompt,
            'error' => $error
        ]);
    }
}