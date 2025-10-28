<?php

namespace Green\EditableEntry\Actions\Concerns;

/**
 * アクションにコンポーネントIDを設定・取得する機能を提供
 */
trait HasComponentId
{
    /**
     * このアクションが属するコンポーネントのID
     */
    protected ?string $componentId = null;

    /**
     * コンポーネントIDを設定
     *
     * Livewireのマウント時にcomponentIdを引数として渡すように設定
     */
    public function componentId(?string $componentId): static
    {
        $this->componentId = $componentId;

        return $this;
    }

    /**
     * コンポーネントIDを取得
     *
     * まずargumentsから取得を試み、なければプロパティから取得
     */
    public function getComponentId(): ?string
    {
        return $this->componentId;
    }

    /**
     * 現在、編集中かを取得する
     */
    public function isEditing(): bool
    {
        $componentId = $this->getComponentId();
        return $componentId && $this->getLivewire()->editingComponentId === $componentId;
    }
}
