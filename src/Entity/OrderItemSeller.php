<?php

namespace App\Entity;

use App\Repository\OrderItemSellerRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemSellerRepository::class)]
class OrderItemSeller extends AbstractEntity
{
    /**
     * @var Seller|null
     */
    #[ORM\ManyToOne(inversedBy: 'orderItemSellers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seller $seller = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $shippingDate = null;

    const STATUS_CART = 'cart';
    const STATUS_PENDING = 'pending';
    const STATUS_ON_DELIVERY = 'on_delivery';

    /**
     * @var Order|null
     */
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItemSellers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    public function __construct()
    {
        parent::__construct();
        $this->createdAt = new DateTime();
        $this->status = self::STATUS_CART;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Seller|null
     */
    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    /**
     * @param Seller|null $seller
     * @return $this
     */
    public function setSeller(?Seller $seller): static
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getShippingDate(): ?DateTimeInterface
    {
        return $this->shippingDate;
    }

    /**
     * @param DateTimeInterface|null $shippingDate
     * @return $this
     */
    public function setShippingDate(?DateTimeInterface $shippingDate): static
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     * @return OrderItemSeller
     */
    public function setOrder(?Order $order): OrderItemSeller
    {
        $this->order = $order;
        return $this;
    }
}
