<?php

namespace App\Livewire\Concerns;

use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

trait HasEventImageUploads
{
    use WithFileUploads;

    /** @var TemporaryUploadedFile|null */
    public mixed $banner = null;

    /** @var TemporaryUploadedFile|null */
    public mixed $inlineImage = null;

    /** @var array<int, array{url: string, path: string}> */
    public array $uploadedInlineImages = [];

    /**
     * Get image validation rules for event forms.
     *
     * @return array<string, array<int, string>>
     */
    public function bannerValidationRules(): array
    {
        return [
            'banner' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * Automatically trigger when an inline description image finishes uploading.
     */
    public function updatedInlineImage(): void
    {
        if (! $this->inlineImage instanceof TemporaryUploadedFile) {
            return;
        }

        $this->validate([
            'inlineImage' => ['required', 'image', 'max:5120'],
        ]);

        $path = $this->inlineImage->store('events/inline', 'public');

        if ($path === false) {
            Flux::toast(variant: 'danger', text: __('Failed to upload inline image.'));

            return;
        }

        $url = Storage::url($path);

        $this->uploadedInlineImages[] = [
            'url' => $url,
            'path' => $path,
        ];

        // Append responsive HTML image tag into description
        $imgTag = "\n<img src=\"{$url}\" alt=\"Event Detail Image\" class=\"w-full max-h-96 object-cover rounded-xl my-4 shadow-md\" />\n";
        $this->description .= $imgTag;

        $this->inlineImage = null;

        Flux::toast(variant: 'success', text: __('Image uploaded and inserted into description!'));
    }

    /**
     * Helper action method to upload inline image.
     */
    public function uploadInlineImage(): void
    {
        $this->updatedInlineImage();
    }

    /**
     * Insert a previously uploaded session inline image tag into description.
     */
    public function insertInlineImageTag(string $url): void
    {
        $imgTag = "\n<img src=\"{$url}\" alt=\"Event Detail Image\" class=\"w-full max-h-96 object-cover rounded-xl my-4 shadow-md\" />\n";
        $this->description .= $imgTag;

        Flux::toast(variant: 'info', text: __('Image tag added to description.'));
    }

    /**
     * Remove banner file selection.
     */
    public function removeBannerSelection(): void
    {
        $this->banner = null;
    }

    /**
     * Store the current banner file to storage and return its path.
     */
    public function storeBannerFile(): ?string
    {
        if ($this->banner instanceof TemporaryUploadedFile) {
            $path = $this->banner->store('events/banners', 'public');

            return $path !== false ? $path : null;
        }

        return null;
    }
}
