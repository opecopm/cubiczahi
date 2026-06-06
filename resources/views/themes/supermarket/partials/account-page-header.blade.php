@php
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
    $icon = $icon ?? null;
    $breadcrumb = $breadcrumb ?? [];
    $breadcrumbVariant = $breadcrumbVariant ?? 'light';
@endphp

<div style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;">
    <div style="background:linear-gradient(135deg,#064e3b 0%,#059669 60%,#10b981 100%); padding:36px 0 56px; position:relative; overflow:hidden;">
        <div style="position:absolute;bottom:-40px;left:0;right:0;height:60px;background:#f0fdf4;clip-path:ellipse(55% 100% at 50% 100%);"></div>

        <div class="container-xl position-relative" style="z-index:2;">
            @include('themes.supermarket.partials.breadcrumb', [
                'variant' => $breadcrumbVariant,
                'items' => $breadcrumb,
            ])

            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-start gap-3">
                    @if($icon)
                        <div style="width:48px;height:48px;border-radius:16px;background:rgba(255,255,255,0.14);display:flex;align-items:center;justify-content:center;font-size:1.35rem;line-height:1;">
                            {{ $icon }}
                        </div>
                    @endif

                    <div>
                        <div style="font-weight:700;letter-spacing:.6px;text-transform:uppercase;font-size:.72rem;color:rgba(255,255,255,0.75);">
                            Account Settings
                        </div>
                        <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#fff;margin:0.4rem 0 0.3rem;line-height:1.1;">
                            {{ $title }}
                        </h1>
                        @if($subtitle)
                            <p style="color:rgba(255,255,255,0.75);font-size:0.93rem;margin:0;">
                                {{ $subtitle }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
