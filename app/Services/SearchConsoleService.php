<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SearchConsoleService
{
    private const SCOPE = 'https://www.googleapis.com/auth/webmasters.readonly';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const API_BASE = 'https://searchconsole.googleapis.com/webmasters/v3';

    public function isConfigured(): bool
    {
        return $this->siteUrl() !== '' && $this->serviceAccount() !== null;
    }

    public function siteUrl(): string
    {
        return trim((string) site_setting('gsc_site_url', ''));
    }

    private function serviceAccount(): ?array
    {
        $raw = trim((string) site_setting('gsc_service_account_json', ''));
        if ($raw === '') return null;
        $json = json_decode($raw, true);
        return (is_array($json) && !empty($json['client_email']) && !empty($json['private_key'])) ? $json : null;
    }

    /**
     * Query Search Analytics. Returns rows with clicks/impressions/ctr/position.
     */
    public function searchAnalytics(int $days = 28, array $dimensions = ['date'], int $rowLimit = 100): array
    {
        if (! $this->isConfigured()) return ['error' => 'not_configured'];

        $cacheKey = 'gsc:'.md5($this->siteUrl().$days.implode(',', $dimensions).$rowLimit);
        return Cache::remember($cacheKey, 1800, function () use ($days, $dimensions, $rowLimit) {
            try {
                $token = $this->accessToken();
                if (! $token) return ['error' => 'auth_failed'];

                $startDate = now()->subDays($days)->toDateString();
                $endDate = now()->subDay()->toDateString();
                $endpoint = self::API_BASE.'/sites/'.rawurlencode($this->siteUrl()).'/searchAnalytics/query';

                $res = Http::withToken($token)->timeout(20)->post($endpoint, [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'dimensions' => $dimensions,
                    'rowLimit' => $rowLimit,
                ]);

                if (! $res->successful()) {
                    return ['error' => 'api_error', 'status' => $res->status(), 'body' => $res->body()];
                }
                return ['rows' => $res->json('rows', []), 'startDate' => $startDate, 'endDate' => $endDate];
            } catch (\Throwable $e) {
                return ['error' => 'exception', 'message' => $e->getMessage()];
            }
        });
    }

    /** Mint an OAuth2 access token from the service account using a signed JWT. */
    private function accessToken(): ?string
    {
        $sa = $this->serviceAccount();
        if (! $sa) return null;

        return Cache::remember('gsc:token:'.md5($sa['client_email']), 3300, function () use ($sa) {
            $header = ['alg' => 'RS256', 'typ' => 'JWT'];
            $now = time();
            $claims = [
                'iss' => $sa['client_email'],
                'scope' => self::SCOPE,
                'aud' => self::TOKEN_URL,
                'iat' => $now,
                'exp' => $now + 3600,
            ];
            $b64 = fn ($v) => rtrim(strtr(base64_encode((string) $v), '+/', '-_'), '=');
            $unsigned = $b64(json_encode($header)).'.'.$b64(json_encode($claims));

            $signature = '';
            if (! openssl_sign($unsigned, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256)) {
                return null;
            }
            $jwt = $unsigned.'.'.$b64($signature);

            $res = Http::asForm()->post(self::TOKEN_URL, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);
            return $res->successful() ? (string) $res->json('access_token') : null;
        });
    }
}
