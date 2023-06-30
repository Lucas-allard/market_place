<?php

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsDoctrineListener(event: Events::postUpdate, priority: 1, connection: 'default')]
class OrderPendingListener
{
    private MailerInterface $mailer;
    private string $adminEmail;

    public function __construct(MailerInterface $mailer, string $adminEmail)
    {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
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
        }
    }
}