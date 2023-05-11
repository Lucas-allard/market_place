<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private CategoryRepository $categoryRepository;

    /**
     * @throws NotSupported
     * @throws Exception
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->entityManager->getConnection()->beginTransaction();

        $this->categoryRepository = $this->entityManager->getRepository(Category::class);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(CategoryRepository::class, $this->categoryRepository);
    }

    /**
     * @group repository
     * @group category-repository
     * @group category-repository-save
     */
    public function testSave(): void
    {
        $category = new Category();
        $category->setName('Category 1');
        $category->setSlug('category-1');
        $this->categoryRepository->save($category, true);
        $this->assertNotNull($category->getId());
    }

    /**
     * @group repository
     * @group category-repository
     * @group category-repository-remove
     */
    public function testRemove(): void
    {
        $category = new Category();
        $category->setName('Category 1');
        $category->setSlug('category-1');

        $this->categoryRepository->save($category, true);
        $this->assertNotNull($category->getId());

        $this->categoryRepository->remove($category, true);
        $this->assertNull($this->categoryRepository->findOneBy(['id' => $category->getId()]));
    }

    /**
     * @throws Exception
     */
    public
    function tearDown(): void
    {
        parent::tearDown();

        $connection = $this->entityManager->getConnection();

        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        $this->entityManager->close();
    }
}