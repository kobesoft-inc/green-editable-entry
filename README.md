# Green Editable Entry

Filament の Infolist エントリーにインライン編集機能を提供する PHP パッケージです。別のフォーム画面に遷移することなく、その場でデータを編集できます。

## 機能

- **インライン編集**: 表示モードから直接エントリーを編集
- **シームレスな統合**: Filament の Infolist コンポーネントと完全に統合
- **アクションベースのアーキテクチャ**: 専用の Action クラスによる関心の分離
- **アクションのカスタマイズ**: 編集、保存、キャンセルボタンの外観と動作を自由にカスタマイズ
- **バリデーション対応**: Filament のフォームシステムを通じた完全なバリデーション
- **リレーション対応**: 関連データ（Repeater など）の保存をサポート
- **多言語対応**: 英語と日本語を標準サポート

## 動作要件

- PHP 8.1 以上
- Laravel 12.0 以上
- Filament 4.1 以上

## インストール

Composer でパッケージをインストールできます：

```bash
composer require kobesoft/green-editable-entry
```

サービスプロバイダーは自動的に登録されます。

## 使い方

### 1. Livewire ページに Trait を追加

Filament のページクラス（例：ViewPerson、ViewCompany）に `HasEditableEntry` trait を追加します：

```php
use Green\EditableEntry\Concerns\HasEditableEntry;

class ViewPerson extends ViewRecord
{
    use HasEditableEntry;

    // ... ページのコード
}
```

### 2. Infolist で EditableEntry コンポーネントを使用

標準の Infolist エントリーを `EditableEntry` コンポーネントに置き換えます：

```php
use Green\EditableEntry\Schemas\Components\EditableEntry;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            EditableEntry::make('name')
                ->viewSchema([TextEntry::make('name')])
                ->editSchema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                ]),

            EditableEntry::make('email')
                ->viewSchema([TextEntry::make('email')])
                ->editSchema([
                    TextInput::make('email')
                        ->email()
                        ->required()
                ]),
        ]);
}
```

### 3. コンポーネントの構造

各 `EditableEntry` コンポーネントには2つのモードがあります：

- **表示モード**: `->viewSchema()` で指定したコンポーネントを使用してデータを表示
- **編集モード**: `->editSchema()` で指定したコンポーネントを使用して編集可能なフォームを表示

現在のモードに応じて、アクションボタン（編集、保存、キャンセル）が自動的にレンダリングされます。

## アーキテクチャ

### コンポーネント

- **EditableEntry**: 表示モードと編集モードを管理するメインのスキーマコンポーネント
- **HasEditableEntry**: Livewire ページに編集機能を提供する Trait
- **StartEditableEntryAction**: 編集モードを開始するアクション
- **SaveEditableEntryAction**: 変更を保存するアクション
- **CancelEditableEntryAction**: 編集をキャンセルするアクション
- **HasComponentId**: アクションでコンポーネント ID を管理する Trait

### 状態管理

編集状態はページレベルで管理されます：

```php
public array $editableEntryData = [];      // 編集中のデータを保存
public ?string $editingComponentId = null;  // 現在編集中のコンポーネントを追跡
```

同時に編集できるコンポーネントは1つだけです。

### ワークフロー

1. **編集開始**: 編集ボタンをクリックして編集モードに入る
   - 現在のレコードデータがフォームに読み込まれる
   - コンポーネントが編集モードに切り替わる

2. **編集中**: フォームフィールドを変更
   - 変更は `$editableEntryData` に保存される
   - バリデーションルールが適用される

3. **保存またはキャンセル**:
   - **保存**: バリデーション実行後、データベースに保存し、表示モードに戻る
   - **キャンセル**: 変更を破棄して表示モードに戻る

## 応用例

### アクションのカスタマイズ

各アクション（編集、保存、キャンセル）の外観や動作をカスタマイズできます。クロージャーの `$action` パラメータを通じて、アクションの設定を変更します：

```php
EditableEntry::make('name')
    ->viewSchema([TextEntry::make('name')])
    ->editSchema([
        TextInput::make('name')
            ->required()
    ])
    ->configureEditAction(function (Action $action) {
        $action
            ->color('primary')
            ->icon('heroicon-o-pencil-square')
            ->label('カスタム編集');
    })
    ->configureSaveAction(function (Action $action) {
        $action
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation();
    })
    ->configureCancelAction(function (Action $action) {
        $action
            ->color('gray')
            ->label('戻る');
    });
```

#### カスタマイズ可能な項目

- `color()` - アクションの色（primary, success, danger, gray など）
- `icon()` - Heroicon のアイコン
- `label()` - ボタンのラベルテキスト
- `size()` - ボタンのサイズ（xs, sm, md, lg）
- `requiresConfirmation()` - 確認ダイアログを表示
- その他、Filament Action の全メソッドが利用可能

### 複雑なフォーム

編集スキーマは、リレーションを含むすべての Filament フォームコンポーネントをサポートします：

```php
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Grid as InfolistGrid;

EditableEntry::make('details')
    ->label('詳細情報')
    ->viewSchema([
        InfolistGrid::make(2)->schema([
            TextEntry::make('field1')->label('フィールド1'),
            TextEntry::make('field2')->label('フィールド2'),
        ]),
    ])
    ->editSchema([
        Grid::make(2)->schema([
            TextInput::make('field1')
                ->label('フィールド1')
                ->required(),
            Select::make('field2')
                ->label('フィールド2')
                ->options(['option1' => 'オプション1', 'option2' => 'オプション2']),
            Repeater::make('items')
                ->label('アイテム')
                ->schema([
                    TextInput::make('item_name')->label('アイテム名'),
                ])
                ->columnSpanFull(),
        ]),
    ]);
```

## 多言語対応

パッケージには以下の翻訳が含まれています：
- 英語 (`lang/en/editableEntry.php`)
- 日本語 (`lang/ja/editableEntry.php`)

翻訳をカスタマイズする場合は、以下のコマンドで公開できます：

```bash
php artisan vendor:publish --tag=green-editable-entry-translations
```

## ライセンス

MIT ライセンス。詳細は [License File](LICENSE.md) をご覧ください。

## クレジット

[Kobesoft Inc.](https://github.com/kobesoft-inc) により開発
