<?php

namespace App\Filament\Resources\Registrations;

use App\Filament\Resources\Registrations\Pages\CreateRegistration;
use App\Filament\Resources\Registrations\Pages\EditRegistration;
use App\Filament\Resources\Registrations\Pages\ListRegistrations;
use App\Filament\Resources\Registrations\Pages\ViewRegistration;
use App\Filament\Resources\Registrations\Schemas\RegistrationForm;
use App\Filament\Resources\Registrations\Tables\RegistrationsTable;
use App\Models\Registration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'fullname';

    protected static ?string $navigationLabel = 'Pendaftaran Event';

    protected static ?string $pluralModelLabel = 'Pendaftaran Event';

    protected static ?string $modelLabel = 'Pendaftaran Event';

    public static function form(Schema $schema): Schema
    {
        return RegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pendaftar')
                    ->schema([
                        TextEntry::make('fullname')->label('Nama Lengkap'),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('phone')->label('No. Telepon'),
                        TextEntry::make('event.title')->label('Event'),
                        TextEntry::make('status')->label('Status')->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'approved'         => 'success',
                                'rejected'         => 'danger',
                                'waiting_approval' => 'warning',
                                default            => 'gray',
                            }),
                        TextEntry::make('amount')->label('Jumlah Pembayaran')
                            ->money('IDR'),
                        TextEntry::make('admin_note')->label('Catatan Admin')->default('-'),
                    ])->columns(2),

                Section::make('Bukti Pembayaran')
                    ->schema([
                        ImageEntry::make('foto')
                            ->label('Foto Bukti Transfer')
                            ->getStateUsing(function ($record) {
                                return $record->foto ?: $record->user?->foto;
                            })
                            ->disk('public')
                            ->height(350)
                            ->extraImgAttributes([
                                'style' => 'object-fit: contain; border: 1px solid #e5e7eb; border-radius: 8px;',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return RegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegistrations::route('/'),
            'create' => CreateRegistration::route('/create'),
            'edit' => EditRegistration::route('/{record}/edit'),
            'view' => ViewRegistration::route('/{record}'),
        ];
    }
}

