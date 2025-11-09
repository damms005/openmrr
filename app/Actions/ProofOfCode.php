<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\Process;

final readonly class ProofOfCode
{
    private const ALGORITHM = 'aes-256-cbc';

    public function getLastCommitHash(): string
    {
        $result = Process::run('git rev-parse HEAD');

        if ($result->failed()) {
            throw new \RuntimeException('Failed to get git commit hash: ' . $result->errorOutput());
        }

        return trim($result->output());
    }

    public function verifyToken(string $encryptedToken): array
    {
        try {
            $currentCommitHash = $this->getLastCommitHash();
            $decrypted = $this->decrypt($encryptedToken, $currentCommitHash);

            if ($decrypted === false) {
                return [
                    'verified' => false,
                    'message' => 'Decryption failed - token does not match current commit hash',
                    'commit_hash' => $currentCommitHash,
                ];
            }

            return [
                'verified' => true,
                'message' => 'Code verified successfully',
                'decrypted_value' => $decrypted,
                'commit_hash' => $currentCommitHash,
            ];
        } catch (\Exception $e) {
            return [
                'verified' => false,
                'message' => 'Decryption failed: ' . $e->getMessage(),
            ];
        }
    }

    private function decrypt(string $encryptedData, string $commitHash): string|false
    {
        $decoded = base64_decode($encryptedData, true);

        if ($decoded === false) {
            return false;
        }

        $ivLength = openssl_cipher_iv_length(self::ALGORITHM);
        $iv = substr($decoded, 0, $ivLength);
        $ciphertext = substr($decoded, $ivLength);

        $key = hash('sha256', $commitHash, true);

        return openssl_decrypt($ciphertext, self::ALGORITHM, $key, OPENSSL_RAW_DATA, $iv);
    }
}
