<?php

namespace App\Filament\Resources\BranchStores\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Intervention\Image\Drivers\Gd\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
class BranchStoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('address')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->directory('images')
                    ->image()
                    ->maxSize(2048)
                    ->rules(['dimensions:max_width=2000,max_height=2000'])                    
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {

                        if (! $state instanceof TemporaryUploadedFile) {
                            return;
                        }

                        $fileName = 'logo' . time() . '.' . $state->getClientOriginalExtension();

                        $storedPath = $state->storePubliclyAs('images', $fileName, 'public'); // images/xxx.jpg

                        $publicPath = $storedPath;
                        $storagePath = Storage::disk('public')->path($publicPath);

                        $realPath = realpath($storagePath);
                        if (! $realPath) {
                            dd("Path tidak valid: " . $storagePath);
                        }

                        $manager = new ImageManager(new Driver());

                        $image = $manager->read($realPath)
                            ->cover(250, 100)
                            ->scaleDown(width: 1200)
                            ->encode(new JpegEncoder(quality: 80));

                        Storage::disk('public')->put($publicPath, (string) $image);

                        // ⬇️ INI KUNCI PREVIEW
                        $set('logo', $publicPath);
                    })                 
            ]);
    }
}