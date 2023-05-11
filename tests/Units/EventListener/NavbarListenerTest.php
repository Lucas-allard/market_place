<?php
namespace App\Tests\EventListener;

use App\Repository\CategoryRepository;
use App\EventListener\NavbarListener;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class NavbarListenerTest extends TestCase
{
    /**
     * @group event-listener
     * @group navbar-listener
     * @group navbar-listener-on-kernel-request
     */
    public function testOnKernelRequest()
    {
        $twig = $this->createMock(Environment::class);
        $categoryRepository = $this->createMock(CategoryRepository::class);

        $navbarListener = new NavbarListener($twig, $categoryRepository);

        $twig->expects($this->once())
            ->method('addGlobal')
            ->with(
                $this->equalTo('categories'),
                $this->equalTo($categoryRepository->getParentsAndChildrenCategoriesInSeparatedArrays())
            );

        $navbarListener->onKernelRequest();
    }
}
