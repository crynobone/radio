<?php

namespace Radio\Http\Controllers;

class ScriptsController
{
    public function __invoke(string $path)
    {
        switch ($path) {
            case 'radio.js':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../resources/dist/js/radio.js', 'application/javascript; charset=utf-8');
            case 'radio.js.map':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../resources/dist/js/radio.js.map', 'application/json; charset=utf-8');
        }

        abort(404);
    }

    protected function getHttpDate($timestamp)
    {
        return sprintf('%s GMT', gmdate('D, d M Y H:i:s', $timestamp));
    }

    protected function pretendResponseIsFile($path, $contentType)
    {
        abort_if(! file_exists($path), 404);

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
