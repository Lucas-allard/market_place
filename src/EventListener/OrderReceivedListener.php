<?php

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\Payment;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class OrderPendingListener implements EventSubscriberInterface
{
    private MailerInterface $mailer;

    private EntityManagerInterface $entityManager;
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

    public function postUpdate(PostUpdateEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Payment
            && $entity->getOrder()->getOrderStatus() === Order::STATUS_PENDING
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

    public function updateProductQuantity(Order $order): void
    {
        foreach ($order->getOrderItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $product->setQuantity($product->getQuantity() - $orderItem->getQuantity());

            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
    }

    private function updateOrderDate(Order $order): void
    {
        $order->setOrderDate($order->getUpdatedAt());
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}