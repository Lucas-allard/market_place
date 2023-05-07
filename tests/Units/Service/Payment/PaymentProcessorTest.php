<?php

namespace App\Tests\Service\Payment;

use App\Entity\Payment;
use App\Service\Payment\PaymentProcessor;
use App\Service\Stripe\StripeConnexion;
use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Stripe;
use Stripe\Exception\ApiErrorException;

class PaymentProcessorTest extends TestCase
{
    private PaymentProcessor $paymentProcessor;
    private Payment $payment;
    private string $stripeToken;

    /**
     * @throws ApiErrorException
     */
    protected function setUp(): void
    {
        $this->paymentProcessor = new PaymentProcessor();
        $this->payment = new Payment();
        StripeConnexion::init();
        $this->stripeToken = Stripe\Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 8,
                'exp_year' => 2023,
                'cvc' => '314',
            ],
        ])->id;
    }

    /**
     * @group service
     * @group payment-processor
     * @group payment-processor-process-success
     */
    public function testProcessSuccess(): void
    {

        $this->payment->setStatus(Payment::STATUS_PENDING);
        $this->payment->setAmount(100);
        $this->payment->setCurrency('eur');
        $this->payment->setDescription('Test paiement');
        $this->payment->setPaymentToken($this->stripeToken);


        $isValid = $this->paymentProcessor->process($this->payment);
        $this->assertTrue($isValid);
    }


    /**
     * @group service
     * @group payment-processor
     * @group payment-processor-process-failed
     */
    public function testProcessFailed(): void
    {
        $this->payment->setStatus(Payment::STATUS_PENDING);
        $this->payment->setAmount(100);
        $this->payment->setCurrency('eur');
        $this->payment->setDescription('Test paiement');
        $isValid = $this->paymentProcessor->process($this->payment);
        $this->assertFalse($isValid);
    }

    /**
     * @group service
     * @group payment-processor
     * @group payment-processor-charge-payment
     * @throws ReflectionException
     */
    public function testChargePaymentSuccess(): void
    {

        $this->payment->setStatus(Payment::STATUS_PENDING);
        $this->payment->setAmount(100);
        $this->payment->setCurrency('eur');
        $this->payment->setDescription('Test paiement');
        $this->payment->setPaymentToken($this->stripeToken);

        $reflection = new ReflectionClass($this->paymentProcessor);
        $method = $reflection->getMethod('chargePayment');
        $method->setAccessible(true);

        $this->expectNotToPerformAssertions();

        try {
            $method->invoke($this->paymentProcessor, $this->payment);
        } catch (ApiErrorException $e) {
            $this->fail('Charge should not fail');
        }

    }

    /**
     * @group service
     * @group payment-processor
     * @group payment-processor-charge-payment-whithout-token
     * @throws ReflectionException
     */
    public function testChargePaymentWhithoutToken()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Your card was declined.");

        $payment = new Payment();
        $payment->setAmount(1000);
        $payment->setCurrency('usd');
        $payment->setDescription('Test payment');
        $payment->setPaymentToken('tok_chargeDeclined');

        $reflection = new ReflectionClass($this->paymentProcessor);
        $method = $reflection->getMethod('chargePayment');
        $method->setAccessible(true);

        $method->invoke($this->paymentProcessor, $payment);
    }

    /**
     * @group service
     * @group payment-processor
     * @group payment-processor-charge-payment-without-amount
     * @throws ReflectionException
     */
    public function testChargePaymentWithoutAmount()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("This value must be greater than or equal to 1");

        $payment = new Payment();
        $payment->setAmount(0);
        $payment->setCurrency('usd');
        $payment->setDescription('Test payment');
        $payment->setPaymentToken($this->stripeToken);

        $reflection = new ReflectionClass($this->paymentProcessor);
        $method = $reflection->getMethod('chargePayment');
        $method->setAccessible(true);

        $method->invoke($this->paymentProcessor, $payment);
    }

    /**
     * @group service
     * @group payment-processor
     * @group payment-processor-charge-payment-without-currency
     * @throws ReflectionException
     */
    public function testChargePaymentWithoutCurrency()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid currency: . Stripe currently supports these currencies: usd, aed, afn, all, amd, ang, aoa, ars, aud, awg, azn, bam, bbd, bdt, bgn, bhd, bif, bmd, bnd, bob, brl, bsd, bwp, byn, bzd, cad, cdf, chf, clp, cny, cop, crc, cve, czk, djf, dkk, dop, dzd, egp, etb, eur, fjd, fkp, gbp, gel, gip, gmd, gnf, gtq, gyd, hkd, hnl, hrk, htg, huf, idr, ils, inr, isk, jmd, jod, jpy, kes, kgs, khr, kmf, krw, kwd, kyd, kzt, lak, lbp, lkr, lrd, lsl, mad, mdl, mga, mkd, mmk, mnt, mop, mro, mur, mvr, mwk, mxn, myr, mzn, nad, ngn, nio, nok, npr, nzd, omr, pab, pen, pgk, php, pkr, pln, pyg, qar, ron, rsd, rub, rwf, sar, sbd, scr, sek, sgd, shp, sle, sll, sos, srd, std, szl, thb, tjs, tnd, top, try, ttd, twd, tzs, uah, ugx, uyu, uzs, vnd, vuv, wst, xaf, xcd, xof, xpf, yer, zar, zmw, usdc, btn, ghs, eek, lvl, svc, vef, ltl');
        $this->payment->setAmount(1000);
        $this->payment->setCurrency('');
        $this->payment->setDescription('Test payment');
        $this->payment->setPaymentToken($this->stripeToken);

        $reflection = new ReflectionClass($this->paymentProcessor);
        $method = $reflection->getMethod('chargePayment');
        $method->setAccessible(true);

        $method->invoke($this->paymentProcessor, $this->payment);
    }

    /**
     * @group service
     * @group payment-processor
     * @group payment-processor-check-payment-status
     * @throws ReflectionException
     */
    public function testCheckPaymentStatusSuccess(): void
    {
        $this->payment->setStatus(Payment::STATUS_PENDING);
        $reflection = new ReflectionClass($this->paymentProcessor);
        $method = $reflection->getMethod('checkPaymentStatus');
        $method->setAccessible(true);
        $this->expectNotToPerformAssertions();
        $method->invoke($this->paymentProcessor, $this->payment->getStatus());
    }

    /**
     * @group service
     * @group payment-processor
     * @group payment-processor-check-payment-status-failed
     * @throws ReflectionException
     */
    public function testCheckPaymentStatusFailed(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Payment is not pending");
        $this->payment->setStatus('test');
        $reflection = new ReflectionClass($this->paymentProcessor);
        $method = $reflection->getMethod('checkPaymentStatus');
        $method->setAccessible(true);
        $method->invoke($this->paymentProcessor, $this->payment->getStatus());
    }
}