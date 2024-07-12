<?php

namespace App\Tests\Func;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Post;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BolgPostTest extends ApiTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        static::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    public function testGetCollection(): void
    {
        $this->createPost('Test Post 1', 'This is the content of the first test post.');
        $this->createPost('Test Post 2', 'This is the content of the second test post.');

        $response = static::createClient()->request('GET', '/api/posts');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Post',
            '@id' => '/api/posts',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 2,
        ]);
    }

    public function testGetItem(): void
    {
        $post = $this->createPost('Test Post', 'This is the content of the test post.');

        $response = static::createClient()->request('GET', '/api/posts/' . $post->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Post',
            '@id' => '/api/posts/' . $post->getId(),
            '@type' => 'Post',
            'id' => $post->getId(),
            'title' => 'Test Post',
            'content' => 'This is the content of the test post.',
            'createdAt' => $post->getCreatedAt()->format(DateTimeImmutable::ATOM),
        ]);
    }

    public function testPutItem(): void
    {
        $post = $this->createPost('Test Post', 'This is the content of the test post.');

        $response = static::createClient()->request('PUT', '/api/posts/' . $post->getId(), [
            'json' => [
                'title' => 'Updated Post',
                'content' => 'This is the updated content.',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Post',
            '@id' => '/api/posts/' . $post->getId(),
            '@type' => 'Post',
            'title' => 'Updated Post',
            'content' => 'This is the updated content.',
        ]);
    }

    public function testDeleteItem(): void
    {
        $post = $this->createPost('Test Post', 'This is the content of the test post.');

        $response = static::createClient()->request('DELETE', '/api/posts/' . $post->getId());

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->findPostById($post->getId()));
    }

    private function createPost(string $title, string $content): Post
    {
        $post = new Post();
        $post->setTitle($title);
        $post->setContent($content);
        $post->setCreatedAt(new DateTimeImmutable());

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    private function findPostById(int $id): ?Post
    {
        return $this->entityManager->getRepository(Post::class)->find($id);
    }
}