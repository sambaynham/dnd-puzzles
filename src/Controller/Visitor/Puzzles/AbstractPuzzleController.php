<?php

namespace App\Controller\Visitor\Puzzles;

use App\Controller\AbstractBaseController;
use App\Services\Puzzle\Service\Interfaces\PuzzleServiceInterface;
use App\Services\Quotation\Service\QuotationService;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractPuzzleController extends AbstractBaseController
{
    public function __construct(
        protected readonly PuzzleServiceInterface $puzzleService,
        protected readonly SerializerInterface $serializer,
        QuotationService $quotationService,
    ) {
        parent::__construct($quotationService);
    }
}
