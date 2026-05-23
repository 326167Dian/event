<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('no_tlp')
                    ->label('No. Telepon')
                    ->searchable(),

                ImageColumn::make('foto')
                    ->label('Bukti Bayar')
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
                    ->height(50)
                    ->square(),

                TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Waktu Register')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
