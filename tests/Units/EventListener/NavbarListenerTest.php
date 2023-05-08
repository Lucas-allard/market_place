<?php

namespace App\Tests\Units\EventListener;

use App\Event\NavbarEvent;
use App\EventListener\NavbarListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class NavbarListenerTest extends TestCase
{

    /**
     * @group event
     * @group navbar
     * @group navbar-event-listener-on-kernel-controller
     */
    public function testOnKernelController(): void
    {
        // Create a mock NavbarEvent
        $categories = [
            ['name' => 'Category 1'],
            ['name' => 'Category 2'],
        ];
        $navbarEvent = new NavbarEvent($categories);

        // Create a mock request and set the NavbarEvent in the request attributes
        $request = new Request();
        $request->attributes->set('navbar_event', $navbarEvent);

        // Create a mock Twig environment
        $twigMock = $this->createMock(Environment::class);

        // Create a mock RequestStack
        $requestStackMock = $this->createMock(RequestStack::class);

        $requestStackMock->method('getCurrentRequest')
            ->willReturn($request);

        $twigMock->method('render')
            ->with('components/_navbar.html.twig', ['categories' => $categories])
            ->willReturn('<ul><li>Category 1</li><li>Category 2</li></ul>');

        // Create a NavbarListener instance and call the onKernelController method
        $navbarListener = new NavbarListener($requestStackMock, $twigMock);

        try {
            $navbarListener->onKernelController();
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
        }

        // Assert that the navbar is correctly generated
        $this->expectOutputString('<ul><li>Category 1</li><li>Category 2</li></ul>');

    }

    /**
     * @group event
     * @group navbar
     * @group navbar-event-listener-on-kernel-controller
     */
//    public function testNavbarContentIsCorrectlyGenerated(): void
//    {
//        // Create a mock NavbarEvent
//        $categories = [
//            ['name' => 'Category 1'],
//            ['name' => 'Category 2'],
//        ];
//        $navbarEvent = new NavbarEvent($categories);
//
//        // Create a mock Twig environment that returns the expected HTML for the navbar
//        $expectedNavbarHtml = '<ul><li>Category 1</li><li>Category 2</li></ul>';
//        $twigMock = $this->createMock(Environment::class);
//        $twigMock->method('render')
//            ->with('navbar.html.twig', ['categories' => $categories])
//            ->willReturn($expectedNavbarHtml);
//
//        // Create a mock Response
//        $response = new Response('<html><body>{navbar}</body></html>');
//
//        // Create a mock RequestStack
//        $requestStackMock = $this->createMock(RequestStack::class);
//        $requestStackMock->method('getCurrentRequest')
//            ->willReturn(new Request(['_navbar_event' => $navbarEvent]));
//
//        // Create a NavbarListener instance and call the onKernelController method
//        $navbarListener = new NavbarListener($requestStackMock, $twigMock);
//        $navbarListener->onKernelController($response);
//
//        // Assert that the NavbarListener updates the response content with the expected navbar HTML
//        $expectedHtml = '<html><body>' . $expectedNavbarHtml . '</body></html>';
//        $this->assertSame($expectedHtml, $response->getContent());
//    }

}