<?php

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class OrderReceivedListener implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var string
     */
    private string $adminEmail;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager, string $adminEmail)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->adminEmail = $adminEmail;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate => 'postUpdate',
        ];
    }

    /**
     * @param PostUpdateEventArgs $event
     * @return void
     */
    public function postUpdate(PostUpdateEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Payment
            && $entity->getOrder()->getStatus() === Order::STATUS_COMPLETED
            && $entity->getStatus() === Payment::STATUS_PAID
        ) {
            try {
                $this->sendMailToCustomer($entity->getOrder());
                $this->sendMailToSeller($entity->getOrder());
            } catch (TransportExceptionInterface $e) {

            }

            $this->updateOrderDate($entity->getOrder());
            $this->updateProductQuantity($entity->getOrder());
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMailToCustomer(Order $order): void
    {
        $email = (new Email())
            ->from($this->adminEmail)
            ->to($order->getCustomer()->getEmail())
            ->subject('Votre commande est en cours de préparation')
            ->html('<p>Votre commande est en cours de préparation, vous allez recevoir un mail de confirmation dès que votre commande sera expédiée.</p>');

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMailToSeller(Order $order): void
    {
        foreach ($order->getOrderItems() as $orderItem) {
            $product = $orderItem->getProduct();

            $email = (new Email())
                ->from($this->adminEmail)
                ->to($product->getSeller()->getEmail())
                ->subject('Une commande a été passée')
                ->html('<p>Une commande a été passée pour votre produit ' . $product->getName() . '</p>');

            $this->mailer->send($email);
        }
    }

    /**
     * @param Order $order
     * @return void
     */
    public function updateProductQuantity(Order $order): void
    {
        foreach ($order->getOrderItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $product->setQuantity($product->getQuantity() - $orderItem->getQuantity());

            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
    }

    /**
     * @param Order $order
     * @return void
     */
    private function updateOrderDate(Order $order): void
    {
        $order->setOrderDate($order->getPayment()->getUpdatedAt());
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}