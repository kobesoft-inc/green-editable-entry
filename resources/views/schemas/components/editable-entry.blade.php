<div wire:key="editable-{{ $getId() }}">
    {{-- ラベルとhintActions --}}
    @if($getLabel())
        <div class="fi-sc-section-label-ctn">
            <div class="fi-sc-section-label">
                {{ $getLabel() }}
            </div>

            @php($hintActions = $getHintActions())
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
        {{ $getChildSchema() }}
    </div>
</div>
