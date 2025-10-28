<?php

namespace Green\EditableEntry\Concerns;

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
     * 編集中のデータを保持する配列
     * キー: コンポーネントID、値: 編集中のデータ配列
     */
    public array $editableEntryData = [];

    /**
     * 現在編集中のコンポーネントID
     * 同時に複数のコンポーネントを編集できないため、単一の文字列で管理
     */
    public ?string $editingComponentId = null;

    /**
     * 指定されたcomponentIdを持つEditableEntryコンポーネントを取得
     *
     * すべてのキャッシュ済みスキーマから検索します。
     * 特定のスキーマ名（'infolist'など）に依存しないため、汎用的に使用できます。
     *
     * @param string $componentId コンポーネントID
     * @return EditableEntry|null 見つかったコンポーネント、なければnull
     */
    public function getEditableEntry(string $componentId): ?EditableEntry
    {
        foreach ($this->getCachedSchemas() as $schema) {
            if ($c = $schema->getComponent(fn($c) => $c instanceof EditableEntry && $c->getId() === $componentId)) {
                /** @var EditableEntry $c */
                return $c;
            }
        }

        return null;
    }

    /**
     * 編集開始アクション
     *
     * @return StartEditableEntryAction
     */
    public function startEditableEntryAction(): StartEditableEntryAction
    {
        return StartEditableEntryAction::make()
            ->livewire($this);
    }

    /**
     * キャンセルアクション
     *
     * @return CancelEditableEntryAction
     */
    public function cancelEditableEntryAction(): CancelEditableEntryAction
    {
        return CancelEditableEntryAction::make()
            ->livewire($this);
    }

    /**
     * 保存アクション
     *
     * @return SaveEditableEntryAction
     */
    public function saveEditableEntryAction(): SaveEditableEntryAction
    {
        return SaveEditableEntryAction::make()
            ->livewire($this);
    }
}
