@php
    // 基本情報を取得
    $componentId = $getId();
    $livewire = $getLivewire();

    // hintActionsを取得
    $hintActions = $getHintActions();

    // 編集状態を取得
    $isEditing = $livewire->editingComponentId === $componentId;
@endphp

{{-- 全体を囲む要素 --}}
<div wire:key="editable-{{ $componentId }}">
    {{-- ラベルとhintActions --}}
    @if($getLabel())
        <div class="fi-sc-section-label-ctn">
            <div class="fi-sc-section-label">
                {{ $getLabel() }}
            </div>

            @if(count($hintActions) > 0)
                <div class="fi-sc-flex fi-vertical-align-center">
                    @foreach($hintActions as $hintAction)
                        @if($hintAction->isVisible())
                            {{ $hintAction }}
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- コンテンツ --}}
    <div class="fi-sc-editable-content">
        @if($isEditing)
            {{-- 編集モード: editスキーマを表示 --}}
            {{ $getChildSchema('edit') }}
        @else
            {{-- 表示モード: viewスキーマを表示 --}}
            {{ $getChildSchema() }}
        @endif
    </div>
</div>
