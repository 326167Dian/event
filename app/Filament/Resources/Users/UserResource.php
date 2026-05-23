<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Peserta Register';

    protected static ?string $pluralModelLabel = 'Peserta Register';

    protected static ?string $modelLabel = 'Peserta';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Peserta')
                    ->schema([
                        TextEntry::make('name')->label('Nama'),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('no_tlp')->label('No. Telepon')->default('-'),
                        TextEntry::make('role')->label('Role')->badge(),
                        TextEntry::make('created_at')->label('Waktu Register')->dateTime(),
                    ])->columns(2),

                Section::make('Bukti Pembayaran')
                    ->schema([
                        ImageEntry::make('foto')
                            ->label('Foto Bukti Transfer')
                            ->getStateUsing(function ($record) {
                                if (!$record->foto) {
                                    return null;
                                }

                                $path = ltrim($record->foto, '/');

                                if (str_starts_with($path, 'public/')) {
                                    $path = substr($path, 7);
                                }

                                return asset('storage/' . $path);
                            })
                            ->height(350)
                            ->extraImgAttributes([
                                'style' => 'object-fit: contain; border: 1px solid #e5e7eb; border-radius: 8px;',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
