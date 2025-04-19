<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_pelanggan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nomor_telepon')
                    ->required()
                    ->tel()
                    ->maxLength(15),
                Forms\Components\Textarea::make('alamat')
                    ->nullable(),
                Forms\Components\Select::make('metode_pembayaran')
                    ->options([
                        'transfer' => 'Transfer',
                        'tempo' => 'Tempo (Bayar Nanti)',
                    ])
                    ->required(),
                Forms\Components\Select::make('status_pembayaran')
                    ->options([
                        'pending' => 'Pending',
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_order')
                    ->default(now())
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_jatuh_tempo')
                    ->default(fn () => now()->addDays(7))
                    ->visible(fn ($get) => $get('metode_pembayaran') === 'tempo'),
                Forms\Components\TextInput::make('total')
                    ->disabled()
                    ->prefix('Rp')
                    ->numeric(),
                Forms\Components\Textarea::make('catatan')
                    ->nullable(),
                Forms\Components\HasManyRepeater::make('orderItems')
                    ->relationship('orderItems')
                    ->schema([
                        Forms\Components\Select::make('menu_id')
                            ->relationship('menu', 'nama')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, $livewire) => 
                                $set('harga_satuan', $state ? \App\Models\Menu::find($state)->harga : 0)),
                        Forms\Components\TextInput::make('jumlah')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, $get, callable $set) => 
                                $set('subtotal', $state * $get('harga_satuan'))),
                        Forms\Components\TextInput::make('harga_satuan')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                    ])
                    ->defaultItems(1)
                    ->disableItemCreation(false)
                    ->disableItemDeletion(false)
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No. Order')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nomor_telepon'),
                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'transfer' => 'Transfer',
                        'tempo' => 'Tempo',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status_pembayaran')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'Pending',
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'lunas' => 'success',
                        'pending' => 'warning',
                        'belum_lunas' => 'danger',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('tanggal_order')
                    ->date(),
                Tables\Columns\TextColumn::make('tanggal_jatuh_tempo')
                    ->date()
                    ->color(fn ($record) => Carbon::parse($record->tanggal_jatuh_tempo)->isPast() && $record->status_pembayaran !== 'lunas' ? 'danger' : ''),
                Tables\Columns\TextColumn::make('total')
                    ->money('idr'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('metode_pembayaran')
                    ->options([
                        'transfer' => 'Transfer',
                        'tempo' => 'Tempo',
                    ]),
                Tables\Filters\SelectFilter::make('status_pembayaran')
                    ->options([
                        'pending' => 'Pending',
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}