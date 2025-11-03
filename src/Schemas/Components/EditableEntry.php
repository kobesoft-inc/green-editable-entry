<?php

namespace Green\EditableEntry\Schemas\Components;

use Closure;
use Filament\Schemas\Components\Component;
use Green\EditableEntry\Schemas\Components\Concerns\HasEditableEntryActions;
use Green\EditableEntry\Schemas\Components\Concerns\HasEditableEntrySchema;

/**
 * 編集可能なコンポーネント（レイアウト系）
 *
 * 表示モードと編集モードを切り替えるコンテナコンポーネント
 * データを持たず、viewSchemaとeditSchemaの表示を切り替える
 */
class EditableEntry extends Component
{
    use HasEditableEntryActions;
    use HasEditableEntrySchema;

    protected string $view = 'green-editable-entry::schemas.components.editable-entry';

    /**
     * ラベル
     */
    protected string|Closure|null $label = null;

    /**
     * ヒントアクション
     */
    protected array|Closure|null $hintActions = null;

    /**
     * コンポーネントを作成
     */
    public static function make(?string $id = null): static
    {
        $static = app(static::class);
        $static->configure();

        if ($id) {
            $static->id($id);
        }

        return $static;
    }

    /**
     * 初期設定（デフォルトアクションを設定）
     */
    protected function setUp(): void
    {
        parent::setUp();

        // アクションを子スキーマとして追加（Filamentのアクションシステムと統合）
        $this->childComponents(fn () => $this->getEditableEntryActions(), 'actions');

        // デフォルトのhintActionsも設定（後方互換性のため）
        $this->hintActions(fn () => $this->getEditableEntryActions());
    }

    /**
     * ラベルを設定
     */
    public function label(string|Closure|null $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * ラベルを取得
     */
    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    /**
     * ヒントアクションを設定
     */
    public function hintActions(array|Closure $actions): static
    {
        $this->hintActions = $actions;

        return $this;
    }

    /**
     * ヒントアクションを取得
     */
    public function getHintActions(): array
    {
        return $this->evaluate($this->hintActions) ?? [];
    }
}
