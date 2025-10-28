<?php

namespace Green\EditableEntry\Schemas\Components\Concerns;

use Closure;
use Filament\Schemas\Schema;
use RuntimeException;

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
     */
    public function getChildSchema($key = null): ?Schema
    {
        // keyがnullの場合、編集モードに応じてviewまたはeditスキーマを返す
        if ($key === null) {
            $key = $this->isEditing() ? 'edit' : 'view';
        }

        // 親クラスのgetChildSchemaを呼び出して、Schemaオブジェクトを取得
        $schema = parent::getChildSchema($key);

        return $schema;
    }

    /**
     * デフォルトスキーマを取得する
     *
     * viewとeditの両方のスキーマを返す
     */
    public function getDefaultChildSchemas(): array
    {
        return ['default' => []];
    }

    /**
     * 子スキーマの設定をカスタマイズ
     *
     * editキーの場合:
     * - statePathの検証（ルート変数がLivewireのpublic配列として定義されているか）
     * - recordとmodelを設定
     * - fill()は編集モード開始時に別途実行される
     *
     * @param Schema $schema スキーマインスタンス
     * @param string $key スキーマのキー（'edit' または 'default'）
     * @return Schema 設定されたスキーマインスタンス
     * @throws RuntimeException statePathが適切に設定されていない場合
     */
    protected function configureChildSchema(Schema $schema, string $key): Schema
    {
        if ($key === 'edit') {
            $this->validateStatePath($schema);

            return $schema
                ->model($this->getModel())
                ->record($this->getRecord());
        }

        return parent::configureChildSchema($schema, $key);
    }

    /**
     * editスキーマのstatePathを検証
     *
     * statePathのルート変数がLivewireコンポーネントにpublic配列として定義されているか確認
     *
     * @param Schema $schema 検証するスキーマ
     * @throws RuntimeException statePathが適切に設定されていない場合
     */
    private function validateStatePath(Schema $schema): void
    {
        // statePathの絶対パスを取得
        $statePath = $schema->getStatePath(isAbsolute: true);

        // ルート変数名を取得（例: "data.person.name" から "data" を取得）
        $rootVariableName = explode('.', $statePath)[0] ?? null;

        if (!$rootVariableName) {
            throw new RuntimeException('EditableSection requires statePath. Set statePath("data") on schema.');
        }

        // Livewireコンポーネントを取得
        $livewire = $this->getLivewire();

        // ルート変数がLivewireに存在し、かつpublic配列として定義されているかチェック
        $reflection = new \ReflectionClass($livewire);

        try {
            $property = $reflection->getProperty($rootVariableName);

            // プロパティがpublicかチェック
            if (!$property->isPublic()) {
                throw new RuntimeException(
                    "Property '$$rootVariableName' must be public. Add: public array $$rootVariableName = []; or set statePath(\"data\") on schema."
                );
            }

            // プロパティの型が配列かチェック
            $propertyType = $property->getType();
            if ($propertyType && $propertyType->getName() !== 'array') {
                throw new RuntimeException(
                    "Property '$$rootVariableName' must be array type. Add: public array $$rootVariableName = []; or set statePath(\"data\") on schema."
                );
            }

        } catch (\ReflectionException) {
            throw new RuntimeException(
                "Property '$$rootVariableName' not found in Livewire. Add: public array $$rootVariableName = []; or set statePath(\"data\") on schema."
            );
        }
    }
}
