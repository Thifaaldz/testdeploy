<?php

namespace App\Services\Spreadsheets;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class RemoteSpreadsheetDownloader
{
    public function download(string $url): string
    {
        $resolvedUrl = $this->normalizeUrl($url);
        $response = Http::timeout(30)
            ->accept('*/*')
            ->get($resolvedUrl);

        $response->throw();

        $contentType = strtolower($response->header('Content-Type', ''));
        $extension = str_contains($contentType, 'csv') || str_ends_with(strtolower(parse_url($resolvedUrl, PHP_URL_PATH) ?? ''), '.csv')
            ? 'csv'
            : 'xlsx';

        $temporaryPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'stats-' . Str::uuid() . ".{$extension}";

        if (file_put_contents($temporaryPath, $response->body()) === false) {
            throw new RuntimeException('Gagal menyimpan spreadsheet sementara.');
        }

        return $temporaryPath;
    }

    public function normalizeUrl(string $url): string
    {
        if (! str_contains($url, 'docs.google.com/spreadsheets')) {
            return $url;
        }

        preg_match('/\/d\/([^\/]+)/', $url, $matches);
        $spreadsheetId = $matches[1] ?? null;

        if (! $spreadsheetId) {
            return $url;
        }

        return "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=xlsx";
    }
}
