<?php

namespace Green\EditableEntry\Actions;

use Filament\Actions\Action;
use Green\EditableEntry\Actions\Concerns\HasComponentId;

/**
 * 編集モードを開始するアクション
 */
class StartEditableEntryAction extends Action
{
    use HasComponentId;

    public static function getDefaultName(): ?string
    {
        return 'startEditableEntry';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('green-editable-entry::editableEntry.actions.edit'));
        $this->icon('heroicon-o-pencil');
        $this->link();
        $this->size('xs');

        // 表示モード時のみ表示
        $this->visible(fn() => !$this->isEditing());

        // 編集モード開始処理
        $this->action(function (): void {

            $livewire = $this->getLivewire();
            $componentId = $this->getComponentId();

            if (!$componentId) {
                return;
            }

            // 編集中のコンポーネントとしてマーク
            $livewire->editingComponentId = $componentId;

            // EditableEntryコンポーネントを取得
            $editableEntry = $livewire->getEditableEntry($componentId);

            if (!$editableEntry) {
                return;
            }

            // editスキーマを取得し、レコードの現在値でフォームを初期化
            $editSchema = $editableEntry->getChildSchema('edit');
            $editSchema?->fill($editableEntry->getRecord()->attributesToArray());
        });
    }
}
