<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


use App\Entity\Category;
use App\Entity\Post;
class PostController extends AbstractController     
{
    private $doctrine;

    public function __construct(PersistenceManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
   // #[Route('/post', name: 'app_post')]
    /* public function index(PostRepository $postRepository): Response
    {
        
        $posts = $postRepository->findAll();
        
        
        
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    } */
    


    #[Route('/updated-post/{id}', name: 'post_update', methods: ['GET', 'POST'])]
    public function updatePost(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = $entityManager->getRepository(Post::class)->find($id);
        

        if (!$post) {
            // Handle the case when the post is not found
            return $this->redirectToRoute('app_post');
        }
    
        if ($request->isMethod('POST')) {
            $post = $entityManager->getRepository(Post::class)->find($request->request->get('post_id'));
            $post->setTitle($request->request->get('title'));
            $post->setContent($request->request->get('content'));
            $post->setCreatedAt(new \DateTimeImmutable()); // Update the updatedAt field instead of createdAt
    
            // Assuming you have a repository for the Category entity
            $category = $entityManager->getRepository(Category::class)->find($request->request->get('category_id'));
            $post->setCategory($category);
    
            $entityManager->persist($post);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_post');
        }
    
        
    }


    #[Route('/post/{id}', name: 'post_detail')]
    public function show(int $id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);
        
        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }
        
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
    #[Route('/post', name: 'app_post')]
    public function index(Request $request ,PersistenceManagerRegistry $doctrine): Response
    {
        $page = $request->query->getInt('page', 1); // Get page number from query parameter, default to 1 if not provided
        $postsPerPage = 4; // Number of posts per page

        // Get the Post repository
        $postRepository = $this->doctrine->getManager()->getRepository(Post::class);

        // Call the getPaginatedPosts method to fetch paginated posts
        $paginator = $postRepository->getPaginatedPosts($page, $postsPerPage);

        // Fetch the paginated results
        $paginatedPosts = $paginator->getIterator()->getArrayCopy();

        // Render your template, passing the paginated posts and any other data
        $categories = $this->doctrine->getManager()->getRepository(Category::class)->findAll();
       
            return $this->render('post/index.html.twig', [
                'posts' => $paginatedPosts,
                'paginator' => $paginator,
                'categories'=>$categories
            ]);
    }
    #[Route('/posts/create', name: 'app_post_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        if ($request->isMethod('POST')) {
            $post = new Post();
            $post->setTitle($request->request->get('title'));
            $post->setContent($request->request->get('content'));
            $post->setCreatedAt(new \DateTimeImmutable());

            // Assuming you have a repository for the Category entity
            $category = $entityManager->getRepository(Category::class)->find($request->request->get('category_id'));
            $post->setCategory($category);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/create.html.twig',[
            'categories' => $categories,
        ]);
    }
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, PostRepository $postRepository): Response
    {
        $postResults = [];
        
        $query = $request->query->get('q');
        if($query){
            $page = $request->query->getInt('page', 1); // Get page number from query parameter, default to 1 if not provided
            $postsPerPage = 4; // Number of posts per page

        // Get the Post repository
        

            $postRepository = $this->doctrine->getManager()->getRepository(Post::class);

        // Call the getPaginatedPosts method to fetch paginated posts
        $paginator = $postRepository->getPaginatedPosts($page, $postsPerPage,$query);

        // Fetch the paginated results
        $paginatedPosts = $paginator->getIterator()->getArrayCopy();
            

        }


        return $this->render('post/index.html.twig', [
           
            'posts' => $paginatedPosts,
             'paginator' => $paginator,
            

        ]);
    }
    
    #[Route('/posts/{id}', name: 'post_delete', methods: ['GET'])]
    public function deletePost(Request $request, EntityManagerInterface $em, $id): Response
    {
        $post = $em->getRepository(Post::class)->find($id);

        if ($post) {
           
        

        $em->remove($post);
        $em->flush();

        return  $this->redirectToRoute('app_post');;
    }
    return $this->render('post/deletedEror.html.twig',[
        'error' => 'eror'
    ]);
    }
}

