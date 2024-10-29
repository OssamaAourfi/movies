<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MovieController extends AbstractController
{
    private $movieRepository;
    
    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
      
    }

    #[Route('/movie', name: 'app_movie')]
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();

        return $this->render('movie/index.html.twig', ['movies' => $movies]);
    }

    #[Route('movie/create', name: 'create_movie')]
    public function create(EntityManagerInterface $em, Request $request, ValidatorInterface $validator): Response
    { 
        if ($request->isMethod('POST')) {
            
            $movie = new Movie();
                     
            /** @var UploadedFile $imageFile */
            $imageFile = $request->files->get('imagePath');
            if ($imageFile) {
                // Generate a new filename (you can use any unique name logic)
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                // Set the directory where the file will be uploaded
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                try {
                    // Move the file to the specified directory
                    $imageFile->move($uploadDir, $newFilename);                   
                    // Set the image path (relative to public directory)
                    $movie->setImagePath('/uploads/' . $newFilename);
                } catch (FileException $e) {
                    // Handle file upload error
                    return new Response('Error uploading file: ' . $e->getMessage(), 500);
                }
            }else{
                    return new Response('Image file is required.', 400);
                }
            $movie->setTitle($request->request->get('title'));
            $movie->setDescription($request->request->get('description'));           
            $movie->setReleaseYear($request->request->get('releaseYear'));
            // Validation des données
            $errors = $validator->validate($movie);

            if (count($errors) > 0) {
                // Retourner les erreurs sous forme de réponse si la validation échoue
                $errorsString = (string) $errors;

                return new Response($errorsString);
            }
                $em->persist($movie);
                $em->flush();
                return $this->redirectToRoute('app_movie');
            }
            return $this->render('movie/create.html.twig');        
    }
  
      #[Route('/movie/{id}', name:'movies',methods:['GET'])]
    public function show(int $id): Response
    {
       $movie = $this->movieRepository->find($id);
       
        return $this->render('movie/show.html.twig',['movie'=> $movie]);
    }

    #[Route('movie/edit/{id}', name: 'edit_movie', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, Movie $movie): Response
    {
        if ($request->isMethod('POST')) {
            // Get data from the request
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $imagePath = $request->request->get('imagePath');
            $releaseYear = $request->request->get('releaseYear');

            // Check if title is provided
            if ($title === null || trim($title) === '') {
                throw new \InvalidArgumentException('Title cannot be empty.');
            }
            $movie->setTitle($title);
            // Set other properties if provided
            $movie->setDescription($description);
            $movie->setImagePath($imagePath);
            if ($releaseYear !== null) {
                $movie->setReleaseYear((int)$releaseYear);
            }

            // Persist changes
            $em->flush();

            // Redirect to the movie show page or another appropriate route
            return $this->redirectToRoute('movies', ['id' => $movie->getId()]);
        }

        // For GET requests, render the edit form (template)
        return $this->render('movie/edit.html.twig', [
            'movie' => $movie,
        ]);
    }

#[Route('/movie/delete/{id}', name:'delete_movie')]
public function delete(EntityManagerInterface $em,Movie $movie): Response{
        if (!$movie) {
            throw $this->createNotFoundException('Movie not found');
        }
        $em->remove($movie);
        $em->flush();
        return $this->redirectToRoute('app_movie');
}


    
}