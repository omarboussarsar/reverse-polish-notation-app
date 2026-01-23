<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ReversePolishNotation;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalculatorController extends AbstractController
{
    #[Route('/', name: 'calculator', methods: ['GET', 'POST'])]
    public function index(Request $request, ReversePolishNotation $calculator): Response
    {
        $expression = trim((string) $request->request->get('expression', ''));
        $result = null;
        $error = null;

        if ($request->isMethod('POST')) {
            try {
                $result = $calculator->evaluate($expression);
            } catch (InvalidArgumentException $exception) {
                $error = $exception->getMessage();
            }
        }

        return $this->render('calculator/index.html.twig', [
            'expression' => $expression,
            'result' => $result,
            'error' => $error,
        ]);
    }
}
