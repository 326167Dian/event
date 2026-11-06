<?php

namespace App\Filament\Resources\Registrations\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('event.title')
                    ->label('Event')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fullname')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('foto')
                    ->label('Bukti Bayar')
                    ->getStateUsing(function ($record) {
                        $foto = $record->foto ?: $record->user?->foto;

                        if (!$foto) {
                            return null;
                        }

                        $path = ltrim($foto, '/');

                        if (str_starts_with($path, 'public/')) {
                            $path = substr($path, 7);
                        }

                        return asset('storage-public/' . $path);
                    })
                    ->height(44)
                    ->square(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('idr')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'qris' => 'QRIS',
                        'manual' => 'Transfer',
                        default => '-',
                    })
                    ->color(fn (?string $state) => $state === 'qris' ? 'info' : 'gray')
                    ->placeholder('-'),

                TextColumn::make('payment_status')
                    ->label('Status QRIS')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'settlement', 'capture' => 'Lunas',
                        'pending' => 'Menunggu',
                        'expire' => 'Kedaluwarsa',
                        'cancel', 'deny', 'failure' => 'Gagal',
                        default => $state,
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'settlement', 'capture' => 'success',
                        'pending' => 'warning',
                        'expire', 'cancel', 'deny', 'failure' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('-')
                    ->toggleable(),

                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'waiting_approval' => 'Waiting Approval',
                        'approved' => 'Approve',
                        'rejected' => 'Tolak',
                    ])
                    ->selectablePlaceholder(false),

                TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'waiting_approval' => 'Waiting Approval',
                        'approved'         => 'Approved',
                        'rejected'         => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Metode')
                    ->options([
                        'qris' => 'QRIS',
                        'manual' => 'Transfer manual',
                    ]),
                Tables\Filters\SelectFilter::make('event_id')
                    ->relationship('event', 'title')
                    ->label('Event'),
            ]);
    }
}
