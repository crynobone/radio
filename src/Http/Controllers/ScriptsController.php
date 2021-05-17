<?php

declare(strict_types = 1);

namespace Radio\Http\Controllers;

class ScriptsController
{
    public function __invoke(string $path): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        switch ($path) {
            case 'radio.js':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../resources/dist/radio.js', 'application/javascript; charset=utf-8');
            case 'radio.js.map':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../resources/dist/radio.js.map', 'application/json; charset=utf-8');
        }

        abort(404);
    }

    protected function getHttpDate(int $timestamp): string
    {
        return sprintf('%s GMT', gmdate('D, d M Y H:i:s', $timestamp));
    }

    protected function pretendResponseIsFile(string $path, string $contentType): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        abort_unless(file_exists($path), 404);

        $cacheControl = 'public, max-age=31536000';
        $expires = strtotime('+1 year');

        $lastModified = filemtime($path);

        if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '') === $lastModified) {
            return response()->noContent(304, [
                'Expires' => $this->getHttpDate($expires),
                'Cache-Control' => $cacheControl,
            ]);
        }

        return response()->file($path, [
            'Content-Type' => $contentType,
            'Expires' => $this->getHttpDate($expires),
            'Cache-Control' => $cacheControl,
            'Last-Modified' => $this->getHttpDate($lastModified),
        ]);
    }
}
