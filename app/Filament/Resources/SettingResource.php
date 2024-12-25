<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Controls';

    public static function canCreate(): bool
    {
        if (Setting::count() > 0) {
            return false;
        } else {
            return true;
        }
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Forms\Components\TextInput::make('school_name')
                        ->required()
                        ->maxLength(255)
                        ->default('Edusoft Schools'),
                        Select::make('frontend_theme')
                        ->required()
                        ->options(
                            [
                                'default' => 'Default',
                                'dark' => 'Dark',
                                'light' => 'Light',
                            ]
                        )
                        ->default('default')
                        ->label('App Theme'),
                    Forms\Components\Textarea::make('caption')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('address')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('contact')
                        ->columnSpanFull(),
                    FileUpload::make('logo')
                        ->image()
                        ->avatar()
                        ->imageEditor()

                        ->circleCropper(),
                    FileUpload::make('principal_signature')
                        ->image()
                        ->avatar()
                        ->imageEditor()

                        ->circleCropper(),
                    FileUpload::make('favicon')
                        ->image()
                        ->avatar()
                        ->imageEditor()
                        ->circleCropper(),
                    Forms\Components\TextInput::make('meta_title')
                        ->maxLength(255)
                        ->default(null),
                    Forms\Components\TextInput::make('principal_name')
                        ->maxLength(255)
                        ->default(null),
                    Forms\Components\Textarea::make('meta_description')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('meta_keywords')
                        ->columnSpanFull(),
                   
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_name'),
                Tables\Columns\TextColumn::make('logo'),
                Tables\Columns\TextColumn::make('favicon'),
                Tables\Columns\TextColumn::make('meta_title')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('frontend_theme')
                    ->label('App Theme'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
