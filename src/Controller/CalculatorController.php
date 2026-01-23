<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ReversePolishNotation;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalculatorController extends AbstractController
{
    #[Route('/', name: 'calculator_form', methods: ['GET'])]
    public function form(): Response
    {
        return $this->render('calculator/index.html.twig', [
            'expression' => '',
            'result' => null,
            'error' => null,
        ]);
    }

    #[Route('/evaluate', name: 'calculator_evaluate', methods: ['POST'])]
    public function evaluate(Request $request, ReversePolishNotation $calculator): JsonResponse
    {
        $expression = trim((string) $request->request->get('expression', ''));
        $result = null;
        $error = null;

        try {
            $result = $calculator->evaluate($expression);
        } catch (InvalidArgumentException $exception) {
            $error = $exception->getMessage();
        }

        return $this->json([
            'expression' => $expression,
            'result' => $result,
            'error' => $error,
        ]);
    }
}
