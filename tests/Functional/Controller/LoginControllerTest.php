<?php
//
//namespace App\Tests\Functional\Controller;
//
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//
//class LoginControllerTest extends WebTestCase
//{
//
//    /**
//     * @group login
//     * @group login_success
//     * @group login_success_200
//     */
//    public function testIndex()
//    {
//        $client = static::createClient();
//
//        $client->request('GET', '/connexion');
//
//        $this->assertResponseIsSuccessful();
//        $this->assertSelectorExists('form[action="/connexion"]');
//        $this->assertSelectorExists('input[name="_username"]');
//        $this->assertSelectorExists('input[name="_password"]');
//
//        $crawler = $client->getCrawler();
//
//        $lastUsername = $crawler->filter('#username')->attr('value');
//        $error = $crawler->filter('.bg-red-100');
//
//        $this->assertStringContainsString($lastUsername, $client->getResponse()->getContent());
//        if ($error->count() > 0) {
//            $this->assertStringContainsString($error->text(), $client->getResponse()->getContent());
//        }
//    }
//
//    /**
//     * @group logout
//     */
//    public function testLogout()
//    {
//        $client = static::createClient();
//
//        $client->request('GET', '/deconnexion');
//
//        $this->assertResponseStatusCodeSame(302);
//
//        $client->followRedirect();
//    }
//
//
//}