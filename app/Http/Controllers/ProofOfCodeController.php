<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ProofOfCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProofOfCodeController
{
    public function __invoke(Request $request, ProofOfCode $proofOfCode): JsonResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return response()->json([
                'verified' => false,
                'message' => 'Missing token query parameter',
            ], 400);
        }

        $result = $proofOfCode->verifyToken($token);

        return response()->json($result);
    }
}
