<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Event;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('registrations.event'))
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

                        return asset('storage-public/' . $path);
                    })
                    ->height(50)
                    ->square(),

                TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                TextColumn::make('registrations')
                    ->label('Event & Status Bayar')
                    ->state(function ($record) {
                        if ($record->registrations->isEmpty()) {
                            return [];
                        }

                        return $record->registrations->map(function ($registration) {
                            $status = match (true) {
                                (float) $registration->amount <= 0 => 'Gratis',
                                filled($registration->paid_at) => 'Lunas',
                                default => 'Belum Bayar',
                            };

                            return ($registration->event?->title ?? 'Event') . ' - ' . $status;
                        })->all();
                    })
                    ->badge()
                    ->color(fn (string $state) => match (true) {
                        str_contains($state, 'Belum Bayar') => 'danger',
                        str_contains($state, 'Lunas') => 'success',
                        default => 'gray',
                    })
                    ->placeholder('Belum daftar event'),

                TextColumn::make('created_at')
                    ->label('Waktu Register')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'Peserta',
                    ]),

                SelectFilter::make('event_id')
                    ->label('Event')
                    ->options(fn () => Event::query()->orderBy('title')->pluck('title', 'id'))
                    ->query(function ($query, array $data) {
                        if (filled($data['value'] ?? null)) {
                            $query->whereHas(
                                'registrations',
                                fn ($q) => $q->where('event_id', $data['value'])
                            );
                        }
                    }),

                TernaryFilter::make('payment_status')
                    ->label('Status Bayar')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Bayar')
                    ->falseLabel('Belum Bayar')
                    ->queries(
                        true: fn ($query) => $query->whereHas(
                            'registrations',
                            fn ($q) => $q->whereNotNull('paid_at')
                        ),
                        false: fn ($query) => $query->whereHas(
                            'registrations',
                            fn ($q) => $q->whereNull('paid_at')->where('amount', '>', 0)
                        ),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
