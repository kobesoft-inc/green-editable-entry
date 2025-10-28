<?php

namespace Green\EditableEntry\Schemas\Components;

use Filament\Schemas\Components\Section;
use Green\EditableEntry\Schemas\Components\Concerns\HasEditableEntryActions;
use Green\EditableEntry\Schemas\Components\Concerns\HasEditableEntrySchema;

/**
 * 編集可能なSection
 *
 * Sectionを拡張し、ヘッダー部に編集ボタンを配置
 * 表示モードと編集モードを切り替え可能
 *
 * 使用時は必ずIDを指定してください：
 * EditableSection::make('見出し')->id('section_id')
 */
class EditableSection extends Section
{
    use HasEditableEntryActions;
    use HasEditableEntrySchema;

    /**
     * 初期設定（デフォルトアクションを設定）
     */
    protected function setUp(): void
    {
        parent::setUp();

        // afterHeaderに編集可能アクションを追加
        $this->afterHeader(fn () => $this->getEditableEntryActions());
    }
}
