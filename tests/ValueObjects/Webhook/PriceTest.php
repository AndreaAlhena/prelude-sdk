<?php

namespace PreludeSo\SDK\Tests\ValueObjects\Webhook;

use PHPUnit\Framework\TestCase;
use PreludeSo\SDK\ValueObjects\Webhook\Price;

class PriceTest extends TestCase
{
    public function testCanCreatePrice(): void
    {
        $price = new Price(10.50, 'USD');
        
        $this->assertSame(10.50, $price->getAmount());
        $this->assertSame('USD', $price->getCurrency());
    }

    public function testCanCreatePriceWithZeroAmount(): void
    {
        $price = new Price(0.0, 'EUR');
        
        $this->assertSame(0.0, $price->getAmount());
        $this->assertSame('EUR', $price->getCurrency());
    }

    public function testThrowsExceptionForNegativeAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be non-negative');
        
        new Price(-1.0, 'USD');
    }

    public function testThrowsExceptionForEmptyCurrency(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency cannot be empty');
        
        new Price(10.0, '');
    }

    public function testToArray(): void
    {
        $price = new Price(15.75, 'GBP');
        $expected = [
            'amount' => 15.75,
            'currency' => 'GBP'
        ];
        
        $this->assertSame($expected, $price->toArray());
    }

    public function testToString(): void
    {
        $price = new Price(25.99, 'CAD');
        
        $this->assertSame('25.99 CAD', (string) $price);
    }

    public function testToStringWithZeroAmount(): void
    {
        $price = new Price(0.0, 'JPY');
        
        $this->assertSame('0 JPY', (string) $price);
    }
}