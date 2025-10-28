<?php

namespace Green\EditableEntry\Schemas\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;

/**
 * EditableEntry のアクション機能を提供する Trait
 *
 * 編集、保存、キャンセルアクションの定義とカスタマイズを管理します。
 */
trait HasEditableEntryActions
{
    /**
     * 編集アクションのカスタマイズクロージャー
     */
    protected ?Closure $configureEditAction = null;

    /**
     * 保存アクションのカスタマイズクロージャー
     */
    protected ?Closure $configureSaveAction = null;

    /**
     * キャンセルアクションのカスタマイズクロージャー
     */
    protected ?Closure $configureCancelAction = null;

    /**
     * 編集アクションをカスタマイズ
     */
    public function configureEditAction(Closure $callback): static
    {
        $this->configureEditAction = $callback;

        return $this;
    }

    /**
     * 保存アクションをカスタマイズ
     */
    public function configureSaveAction(Closure $callback): static
    {
        $this->configureSaveAction = $callback;

        return $this;
    }

    /**
     * キャンセルアクションをカスタマイズ
     */
    public function configureCancelAction(Closure $callback): static
    {
        $this->configureCancelAction = $callback;

        return $this;
    }

    /**
     * カスタマイズされた編集アクションを取得
     */
    public function getEditAction(): Action
    {
        $action = $this->makeEditAction();

        if ($this->configureEditAction) {
            $this->evaluate($this->configureEditAction, ['action' => $action]);
        }

        return $action;
    }

    /**
     * カスタマイズされた保存アクションを取得
     */
    public function getSaveAction(): Action
    {
        $action = $this->makeSaveAction();

        if ($this->configureSaveAction) {
            $this->evaluate($this->configureSaveAction, ['action' => $action]);
        }

        return $action;
    }

    /**
     * カスタマイズされたキャンセルアクションを取得
     */
    public function getCancelAction(): Action
    {
        $action = $this->makeCancelAction();

        if ($this->configureCancelAction) {
            $this->evaluate($this->configureCancelAction, ['action' => $action]);
        }

        return $action;
    }

    /**
     * すべてのアクションを配列で取得
     */
    public function getEditableEntryActions(): array
    {
        return [
            $this->getEditAction(),
            $this->getSaveAction(),
            $this->getCancelAction(),
        ];
    }

    /**
     * 現在編集中かどうかを判定
     */
    public function isEditing(): bool
    {
        return $this->getLivewire()->editingComponentId === $this->getId();
    }

    /**
     * 編集開始アクションを作成
     *
     * Livewire経由でActionを取得し、このコンポーネントのIDを設定
     */
    private function makeEditAction(): Action
    {
        $componentId = $this->getId();

        \Illuminate\Support\Facades\Log::info('makeEditAction', [
            'componentId' => $componentId,
        ]);

        return $this->getLivewire()
            ->startEditableEntryAction($componentId)
            ->componentId($componentId);
    }

    /**
     * 保存アクションを作成
     *
     * Livewire経由でActionを取得し、このコンポーネントのIDを設定
     */
    private function makeSaveAction(): Action
    {
        $componentId = $this->getId();

        return $this->getLivewire()
            ->saveEditableEntryAction($componentId)
            ->componentId($componentId);
    }

    /**
     * キャンセルアクションを作成
     *
     * Livewire経由でActionを取得し、このコンポーネントのIDを設定
     */
    private function makeCancelAction(): Action
    {
        $componentId = $this->getId();

        return $this->getLivewire()
            ->cancelEditableEntryAction($componentId)
            ->componentId($componentId);
    }
}
