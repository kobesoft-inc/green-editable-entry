<?php

namespace Green\EditableEntry\Actions;

use Filament\Actions\Action;
use Green\EditableEntry\Actions\Concerns\HasComponentId;

/**
 * 編集モードをキャンセルするアクション
 */
class CancelEditableEntryAction extends Action
{
    use HasComponentId;

    public static function getDefaultName(): ?string
    {
        return 'cancelEditableEntry';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('green-editable-entry::editableEntry.actions.cancel'));
        $this->link();
        $this->size('xs');

        // 編集モード時のみ表示
        $this->visible(fn () => $this->isEditing());

        // キャンセル処理
        $this->action(function (): void {
            $livewire = $this->getLivewire();
            $livewire->editingComponentId = null;
        });
    }
}
