<?php

namespace Green\EditableEntry\Schemas\Components\Concerns;

use Closure;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;

/**
 * EditableEntry のスキーマ機能を提供する Trait
 *
 * 表示モード（viewSchema）と編集モード（editSchema）のスキーマ管理を行います。
 */
trait HasEditableEntrySchema
{
    /**
     * 表示モードのスキーマを設定
     */
    public function viewSchema(array|Closure $schema): static
    {
        return $this->childComponents($schema, 'view');
    }

    /**
     * 編集モードのスキーマを設定
     */
    public function editSchema(array|Closure $schema): static
    {
        return $this->childComponents($schema, 'edit');
    }

    /**
     * スキーマを取得する
     *
     * @param null $key スキーマのキー
     * @return Schema|null
     */
    public function getChildSchema($key = null): ?Schema
    {
        if ($key === null) {
            if ($this->isEditing()) {
                return parent::getChildSchema('edit');
            } else {
                return parent::getChildSchema('view');
            }
        }

        return parent::getChildSchema($key);
    }

    /**
     * デフォルトスキーマを取得する
     *
     * デフォルトスキーマは特にないので、空の配列を返す。
     *
     * @return array
     */
    public function getDefaultChildSchemas(): array
    {
        return ['default' => []];
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
}
