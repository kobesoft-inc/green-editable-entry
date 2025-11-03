<div
    {{
        $attributes
            ->merge([
                'id' => $getId(),
                'wire:key' => 'editable-' . $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-sc-editable-entry'])
    }}
>
    @if($getLabel())
        <div class="fi-sc-section-label-ctn">
            <div class="fi-sc-section-label">
                {{ $getLabel() }}
            </div>
            @if($actionsSchema = $getChildSchema('actions'))
                <div class="fi-sc-flex fi-vertical-align-center">
                    {{ $actionsSchema }}
                </div>
            @endif
        </div>
    @endif
    <div class="fi-sc-editable-content">
        {{ $getChildSchema() }}
    </div>
</div>
