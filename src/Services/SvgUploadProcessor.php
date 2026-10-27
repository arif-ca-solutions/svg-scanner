<?php

namespace TwoBase\SvgUploadSecurity\Services;

use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\UploadedFile;
use TwoBase\SvgUploadSecurity\Exceptions\UnsafeSvgUploadException;

class SvgUploadProcessor
{
    public function __construct(
        protected string $mode = 'sanitize',
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(config('svg-upload-security.mode', 'sanitize'));
    }

    /**
     * @return UploadedFile|null Sanitized replacement file, or null if not an SVG candidate
     */
    public function processUploadedFile(UploadedFile $file): ?UploadedFile
    {
        $original = file_get_contents($file->getRealPath() ?: $file->getPathname());
        if ($original === false || $original === '') {
            throw new UnsafeSvgUploadException('Empty or unreadable SVG upload.');
        }

        $byMetadata = $this->isSvgCandidate($file);
        $byContent = $this->looksLikeSvg($original);

        if (! $byMetadata && ! (config('svg-upload-security.sniff_content', true) && $byContent)) {
            return null;
        }

        if (! $byContent) {
            throw new UnsafeSvgUploadException('File metadata indicates SVG but content is not valid SVG.');
        }

        $sanitizer = new Sanitizer();
        $sanitizer->removeRemoteReferences(true);
        $sanitizer->minify(false);

        $clean = $sanitizer->sanitize($original);
        if ($clean === false || trim($clean) === '') {
            throw new UnsafeSvgUploadException('SVG sanitization produced no safe content.');
        }

        if ($this->mode === 'reject') {
            if (! $this->contentEquivalent($original, $clean)) {
                throw new UnsafeSvgUploadException('SVG upload contained disallowed content.');
            }

            return null;
        }

        if ($clean === $original) {
            return null;
        }

        return $this->replacementUploadedFile($file, $clean);
    }

    public function isSvgCandidate(UploadedFile $file): bool
    {
        $mimeTypes = config('svg-upload-security.mime_types', ['image/svg+xml']);
        $extensions = config('svg-upload-security.extensions', ['svg']);

        $mime = strtolower((string) $file->getMimeType());
        $clientMime = strtolower((string) $file->getClientMimeType());
        $ext = strtolower((string) $file->getClientOriginalExtension());

        foreach ($mimeTypes as $allowed) {
            $allowed = strtolower($allowed);
            if ($mime === $allowed || $clientMime === $allowed) {
                return true;
            }
        }

        return in_array($ext, $extensions, true);
    }

    protected function looksLikeSvg(string $content): bool
    {
        $snippet = ltrim(substr($content, 0, 4096));

        return str_contains($snippet, '<svg')
            || (str_starts_with($snippet, '<?xml') && str_contains($snippet, '<svg'));
    }

    protected function contentEquivalent(string $original, string $clean): bool
    {
        return hash('sha256', $this->normalizeForCompare($original))
            === hash('sha256', $this->normalizeForCompare($clean));
    }

    protected function normalizeForCompare(string $svg): string
    {
        return preg_replace('/\s+/u', ' ', trim($svg)) ?? trim($svg);
    }

    protected function replacementUploadedFile(UploadedFile $original, string $cleanContent): UploadedFile
    {
        $tmp = tempnam(sys_get_temp_dir(), 'svg_san_');
        if ($tmp === false) {
            throw new UnsafeSvgUploadException('Could not create temporary file for sanitized SVG.');
        }

        file_put_contents($tmp, $cleanContent);

        return new UploadedFile(
            $tmp,
            $original->getClientOriginalName(),
            'image/svg+xml',
            UPLOAD_ERR_OK,
            true
        );
    }
}
