<?php

namespace Green\EditableEntry\Concerns;

use Filament\Schemas\Components\Component;
use Green\EditableEntry\Actions\CancelEditableEntryAction;
use Green\EditableEntry\Actions\SaveEditableEntryAction;
use Green\EditableEntry\Actions\StartEditableEntryAction;
use Green\EditableEntry\Schemas\Components\EditableEntry;

/**
 * EditableEntryコンポーネントの編集・保存機能を提供するtrait
 *
 * Livewireページクラスでuseすることで、インラインで編集可能な
 * EditableEntryコンポーネントの機能を有効にします。
 */
trait HasEditableEntry
{
    /**
     * 現在編集中のコンポーネントID
     * 同時に複数のコンポーネントを編集できないため、単一の文字列で管理
     */
    public ?string $editingComponentId = null;

    /**
     * 指定されたcomponentIdを持つコンポーネントを取得
     *
     * @param string $componentId コンポーネントID
     * @return Component|null 見つかったコンポーネント、なければnull
     */
    public function getEditableEntry(string $componentId): ?Component
    {
        foreach ($this->getCachedSchemas() as $schema) {
            if ($c = $schema->getComponent(fn($c) => method_exists($c, 'getId') && $c->getId() === $componentId)) {
                /** @var EditableEntry $c */
                return $c;
            }
        }

        return null;
    }

    /**
     * 編集開始アクション
     *
     * @param string $componentId コンポーネントIDをアクション名に含める
     * @return StartEditableEntryAction
     */
    public function startEditableEntryAction(string $componentId): StartEditableEntryAction
    {
        return StartEditableEntryAction::make("startEditableEntry.$componentId")
            ->livewire($this);
    }

    /**
     * キャンセルアクション
     *
     * @param string $componentId コンポーネントIDをアクション名に含める
     * @return CancelEditableEntryAction
     */
    public function cancelEditableEntryAction(string $componentId): CancelEditableEntryAction
    {
        return CancelEditableEntryAction::make("cancelEditableEntry.$componentId")
            ->livewire($this);
    }

    /**
     * 保存アクション
     *
     * @param string $componentId コンポーネントIDをアクション名に含める
     * @return SaveEditableEntryAction
     */
    public function saveEditableEntryAction(string $componentId): SaveEditableEntryAction
    {
        return SaveEditableEntryAction::make("saveEditableEntry.$componentId")
            ->livewire($this);
    }
}
