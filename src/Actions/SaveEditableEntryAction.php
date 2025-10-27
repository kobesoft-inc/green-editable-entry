<?php

namespace Green\EditableEntry\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Green\EditableEntry\Actions\Concerns\HasComponentId;

/**
 * 保存アクション
 */
class SaveEditableEntryAction extends Action
{
    use HasComponentId;

    public static function getDefaultName(): ?string
    {
        return 'saveEditableEntry';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('green-editable-entry::editableEntry.actions.save'));
        $this->icon('heroicon-o-arrow-down-tray');
        $this->link();
        $this->size('xs');

        // 編集モード時のみ表示
        $this->visible(fn() => $this->isEditing());

        // 保存処理
        $this->action(function (): void {

            $livewire = $this->getLivewire();
            $componentId = $this->getComponentId();

            if (!$componentId) {
                return;
            }

            // EditableEntryコンポーネントを取得
            $editableEntry = $livewire->getEditableEntry($componentId);

            if (!$editableEntry) {
                return;
            }

            // editスキーマを取得
            $editSchema = $editableEntry->getChildSchema('edit');

            if (!$editSchema) {
                return;
            }

            // バリデーション + dehydrate + mutateを実行して状態を取得
            $state = $editSchema->getState();

            // メインレコードを更新
            $record = $editableEntry->getRecord();
            $record->update($state);

            // リレーションシップを保存（Repeaterなどの関連データ）
            $editSchema->saveRelationships();

            // 編集状態をクリア
            $livewire->editingComponentId = null;
            unset($livewire->editableEntryData[$componentId]);

            // 成功通知を表示
            Notification::make()
                ->title(__('green-editable-entry::editableEntry.notifications.saved'))
                ->success()
                ->send();

            // ページ全体を再レンダリング
            $livewire->dispatch('$refresh');
        });
    }
}
