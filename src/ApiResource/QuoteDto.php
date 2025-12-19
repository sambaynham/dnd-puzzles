<?php

declare(strict_types=1);
namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Services\Quotation\Domain\Quotation;
use App\State\DiceStateProvider;
use App\State\QuoteProvider;

#[ApiResource(
    uriTemplate: '/quote',
    operations: [
        new Get()
    ],
    stateless: false,
    provider: QuoteProvider::class
)]
class QuoteDto
{
    public function __construct(
        public string $quote,
        public string $citation) {

    }
    public static function makeFromQuoteEntity(Quotation $quotation): static {
        return new static(
            quote: $quotation->getQuotation(),
            citation: $quotation->getCitation()
        );
    }
}
