<?php

namespace Green\EditableEntry\Actions\Concerns;

/**
 * アクションにコンポーネントIDを設定・取得する機能を提供
 */
trait HasComponentId
{
    /**
     * コンポーネントIDを設定
     *
     * @param string|null $componentId
     * @return static
     */
    public function componentId(?string $componentId): static
    {
        $this->arguments(['componentId' => $componentId]);

        return $this;
    }

    /**
     * コンポーネントIDを取得
     *
     * @return string|null
     */
    public function getComponentId(): ?string
    {
        return $this->getArguments()['componentId'];
    }

    /**
     * 現在、編集中かを取得する
     *
     * @return bool
     */
    public function isEditing(): bool
    {
        return $this->getLivewire()->editingComponentId === $this->getComponentId();
    }
}
