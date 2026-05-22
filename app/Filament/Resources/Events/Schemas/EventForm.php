<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'link', 'bulletList', 'orderedList', 'blockquote', 'codeBlock', 'redo', 'undo']),
                DateTimePicker::make('start_at'),
                DateTimePicker::make('end_at'),
                TextInput::make('location'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('Rp '),
                TextInput::make('whatsapp_admin')
                    ->label('WhatsApp Admin ')
                    ->required()
                    ->tel()
                    ->placeholder('Contoh: +6281234567890'),
                TextInput::make('quota')
                    ->numeric(),
                TextInput::make('link'),
                TextInput::make('link_whatsapp'),
                TextInput::make('link_video'),
                TextInput::make('link_document'),
                Toggle::make('is_active')
                    ->required(),
                FileUpload::make('banner')
                    ->label('Banner')
                    ->image()                      // hanya gambar
                    ->disk('public')               // simpan di disk 'public'
                    ->directory('banners')
                    ->imagePreviewHeight('200')
                    ->downloadable()
                    ->openable()
                    ->maxSize(2048)
                    ->deletable(true)        // 2 MB max
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg']),
            ]);
    }
}
