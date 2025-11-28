<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

class ImageProcessor
{
    private ImageManager $manager;

    public function __construct(?ImageManager $manager = null)
    {
        if ($manager) {
            $this->manager = $manager;
            return;
        }

        $this->manager = extension_loaded('imagick')
            ? ImageManager::imagick()
            : ImageManager::gd();
    }

    /**
     * Process an image (uploaded or existing) and store it on the public disk.
     *
     * @param UploadedFile|string $source Uploaded file instance or absolute path to an image file.
     * @param string $directory Target directory inside the public disk (e.g. "orders/items/covers").
     * @param array $options {
     *     @var int|null    $max_width   Max width for resize (maintains ratio).
     *     @var int|null    $max_height  Max height for resize (maintains ratio).
     *     @var int         $quality     Encode quality (0-100).
     *     @var string|null $format      Desired output format (jpg, png, webp, auto|null to keep).
     *     @var string|null $filename    Force filename without directory.
     *     @var bool        $optimize    Whether to resize if exceeds max dimensions.
     * }
     */
    public function processAndStore(UploadedFile|string $source, string $directory, array $options = []): ?string
    {
        $options = array_merge([
            'max_width' => 1600,
            'max_height' => 1600,
            'quality' => 85,
            'format' => 'auto',
            'filename' => null,
            'optimize' => true,
        ], $options);

        $directory = $this->normalizeDirectory($directory);

        $image = $this->createImageInstance($source);
        if (!$image) {
            return null;
        }

        $image = $image->orient();

        if ($options['optimize']) {
            $image = $this->resizeImage($image, $options['max_width'], $options['max_height']);
        }

        $format = $this->determineFormat($image, $source, $options['format']);
        $quality = (int) $options['quality'];

        $filename = $options['filename'] ?? $this->generateFilename($format, $source);
        $relativePath = $directory . '/' . $filename;

        $encoded = $image->encodeByExtension($format, $quality);

        Storage::disk('public')->put($relativePath, $encoded->toString());

        return $relativePath;
    }

    /**
     * Remove a stored image from the public disk.
     */
    public function delete(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    private function createImageInstance(UploadedFile|string $source): ?ImageInterface
    {
        try {
            if ($source instanceof UploadedFile) {
                return $this->manager->read($source->getRealPath());
            }

            if (is_string($source) && file_exists($source)) {
                return $this->manager->read($source);
            }
        } catch (\Throwable $exception) {
            \Log::warning('ImageProcessor failed to read image', [
                'error' => $exception->getMessage(),
            ]);
        }

        return null;
    }

    private function resizeImage(ImageInterface $image, ?int $maxWidth, ?int $maxHeight): ImageInterface
    {
        $width = $image->width();
        $height = $image->height();

        if (($maxWidth && $width > $maxWidth) || ($maxHeight && $height > $maxHeight)) {
            return $image->scaleDown($maxWidth ?? $width, $maxHeight ?? $height);
        }

        return $image;
    }

    private function determineFormat(ImageInterface $image, UploadedFile|string $source, ?string $format): string
    {
        $format = $format ? strtolower($format) : null;

        if ($format === 'auto' || !$format) {
            // Keep original extension when possible
            $format = $this->guessSourceFormat($source) ?? 'jpg';

            // If image has transparency avoid jpeg
            if ($format === 'jpg' && $this->hasTransparency($image)) {
                $format = 'png';
            }
        }

        if ($format === 'jpeg') {
            $format = 'jpg';
        }

        return in_array($format, ['jpg', 'png', 'webp']) ? $format : 'jpg';
    }

    private function guessSourceFormat(UploadedFile|string $source): ?string
    {
        if ($source instanceof UploadedFile) {
            return strtolower($source->getClientOriginalExtension()) ?: null;
        }

        if (is_string($source)) {
            $extension = pathinfo($source, PATHINFO_EXTENSION);
            return $extension ? strtolower($extension) : null;
        }

        return null;
    }

    private function hasTransparency(ImageInterface $image): bool
    {
        try {
            return $image->pickColor(0, 0, 'rgba')[3] < 1;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function generateFilename(string $format, UploadedFile|string $source): string
    {
        $base = Str::uuid()->toString();

        if ($source instanceof UploadedFile) {
            $original = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
            $base = $this->slugify($original) . '_' . $base;
        } elseif (is_string($source)) {
            $original = pathinfo($source, PATHINFO_FILENAME);
            if ($original) {
                $base = $this->slugify($original) . '_' . $base;
            }
        }

        return $base . '.' . $format;
    }

    private function slugify(string $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9\-]+/', '-', $value);
        $value = trim($value ?? '', '-');

        return $value ?: Str::uuid()->toString();
    }

    private function normalizeDirectory(string $directory): string
    {
        return trim(str_replace('\\', '/', $directory), '/');
    }
}

