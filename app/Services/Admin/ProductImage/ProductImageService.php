<?php

namespace App\Services\Admin\ProductImage;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function getByProduct(
        Product $product
    ): Collection {
        return $product->images()->get();
    }

    public function create(
        Product $product,
        array $data
    ): ProductImage {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $data['image'];

        $path = $uploadedFile->store(
            'products',
            'public'
        );

        try {
            return DB::transaction(
                function () use ($product, $data, $path) {
                    $isPrimary = (bool) (
                        $data['is_primary'] ?? false
                    );

                    if (
                        !$product->images()->exists()
                        || $isPrimary
                    ) {
                        $product->images()
                            ->update([
                                'is_primary' => false,
                            ]);

                        $isPrimary = true;
                    }

                    $productImage = $product
                        ->images()
                        ->create([
                            'image' => $path,
                            'alt' => $data['alt'] ?? null,
                            'sort_order' => $data['sort_order'] ?? 0,
                            'is_primary' => $isPrimary,
                        ]);

                    if ($productImage->is_primary) {
                        $product->update([
                            'thumbnail' => $productImage->image,
                        ]);
                    }

                    return $productImage;
                }
            );
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);

            throw $exception;
        }
    }

    public function setPrimary(
        Product $product,
        ProductImage $image
    ): ProductImage {
        $this->ensureBelongsToProduct(
            $product,
            $image
        );

        DB::transaction(function () use ($product, $image) {
            $product->images()
                ->where('id', '!=', $image->id)
                ->update([
                    'is_primary' => false,
                ]);

            $image->update([
                'is_primary' => true,
            ]);

            $product->update([
                'thumbnail' => $image->image,
            ]);
        });

        return $image->refresh();
    }

    public function delete(
        Product $product,
        ProductImage $image
    ): void {
        $this->ensureBelongsToProduct(
            $product,
            $image
        );

        $path = $image->image;

        DB::transaction(function () use ($product, $image) {
            $wasPrimary = $image->is_primary;

            $image->delete();

            if (!$wasPrimary) {
                return;
            }

            $nextImage = $product->images()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            if ($nextImage) {
                $nextImage->update([
                    'is_primary' => true,
                ]);

                $product->update([
                    'thumbnail' => $nextImage->image,
                ]);

                return;
            }

            $product->update([
                'thumbnail' => null,
            ]);
        });

        if ($path) {
            Storage::disk('public')
                ->delete($path);
        }
    }

    private function ensureBelongsToProduct(
        Product $product,
        ProductImage $image
    ): void {
        abort_unless(
            $image->product_id === $product->id,
            404
        );
    }
}