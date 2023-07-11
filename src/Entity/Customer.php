<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $shippingAddress = "";

    /**
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\LessThan('today', message: 'Vous ne pouvez pas être né dans le futur')]
    private ?DateTimeInterface $birthDate = null;

    /**
     * @var Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Order::class)]
    private ?Collection $orders;

    public function __construct()
    {
        parent::__construct();
        $this->orders = new ArrayCollection();
        $this->setRoles(['ROLE_CUSTOMER']);
    }

    /**
     * @return string|null
     */
    public function getShippingAddress(): ?string
    {
        return $this->shippingAddress;
    }

    /**
     * @param string|null $shippingAddress
     * @return Customer
     */
    public function setShippingAddress(?string $shippingAddress): Customer
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }


    /**
     * @return DateTimeInterface|null
     */
    public function getBirthDate(): ?DateTimeInterface
    {
        return $this->birthDate;
    }

    /**
     * @param DateTimeInterface|null $birthDate
     * @return Customer
     */
    public function setBirthDate(?DateTimeInterface $birthDate): Customer
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @param Collection $orders
     * @return Customer
     */
    public function setOrders(Collection $orders): Customer
    {
        foreach ($orders as $order) {
            $this->addOrder($order);
        }
        return $this;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function addOrder(Order $order): Customer
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCustomer($this);
        }

        return $this;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function removeOrder(Order $order): Customer
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }
}