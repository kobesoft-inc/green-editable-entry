<?php

namespace Green\EditableEntry\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * 編集可能なコンポーネント（レイアウト系）
 *
 * 表示モードと編集モードを切り替えるコンテナコンポーネント
 * データを持たず、viewSchemaとeditSchemaの表示を切り替える
 */
class EditableEntry extends Component
{
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
    public static function make(string|null $id = null): static
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

        // デフォルトのhintActionsを設定（Actionオブジェクトを使用）
        $this->hintActions(fn() => [
            // 編集アクション（表示モード時）
            $this->makeEditAction(),

            // 保存アクション
            $this->makeSaveAction(),

            // キャンセルアクション
            $this->makeCancelAction(),
        ]);
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

    /**
     * 表示モードのスキーマを設定
     */
    public function viewSchema(array|Closure $schema): static
    {
        return $this->schema($schema);
    }

    /**
     * 編集モードのスキーマを設定
     */
    public function editSchema(array|Closure $schema): static
    {
        return $this->childComponents($schema, 'edit');
    }

    /**
     * 子スキーマを作成
     *
     * editキーの場合は親コンポーネントを設定せず、
     * 独立したスキーマとして作成します。
     *
     * @param string $key スキーマのキー（'edit' または 'default'）
     * @return Schema スキーマインスタンス
     */
    protected function makeChildSchema(string $key): Schema
    {
        if ($key === 'edit') {
            // editの場合は親コンポーネントを設定しない
            return Schema::make($this->getLivewire());
        }

        return parent::makeChildSchema($key);
    }

    /**
     * 子スキーマの設定をカスタマイズ
     *
     * editキーの場合:
     * - statePathをeditableEntryData.{componentId}に設定（親のstatePathを無視）
     * - recordとmodelを設定
     * - fill()は編集モード開始時に別途実行される
     *
     * @param Schema $schema スキーマインスタンス
     * @param string $key スキーマのキー（'edit' または 'default'）
     * @return Schema 設定されたスキーマインスタンス
     */
    protected function configureChildSchema(Schema $schema, string $key): Schema
    {
        if ($key === 'edit') {
            return $schema
                ->statePath('editableEntryData.' . $this->getId())
                ->model($this->getModel())
                ->record($this->getRecord());
        }

        return parent::configureChildSchema($schema, $key);
    }

    /**
     * 編集開始アクションを作成
     *
     * Livewire経由でActionを取得し、このコンポーネントのIDを引数として設定
     *
     * @return Action
     */
    private function makeEditAction(): Action
    {
        return $this->getLivewire()
            ->startEditableEntryAction()
            ->arguments(['componentId' => $this->getId()]);
    }

    /**
     * 保存アクションを作成
     *
     * Livewire経由でActionを取得し、このコンポーネントのIDを引数として設定
     *
     * @return Action
     */
    private function makeSaveAction(): Action
    {
        return $this->getLivewire()
            ->saveEditableEntryAction()
            ->arguments(['componentId' => $this->getId()]);
    }

    /**
     * キャンセルアクションを作成
     *
     * Livewire経由でActionを取得し、このコンポーネントのIDを引数として設定
     *
     * @return Action
     */
    private function makeCancelAction(): Action
    {
        return $this->getLivewire()
            ->cancelEditableEntryAction()
            ->arguments(['componentId' => $this->getId()]);
    }

}
