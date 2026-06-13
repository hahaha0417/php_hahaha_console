<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $page_config_->Page_Title_ }}</title>
        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: "Segoe UI", sans-serif;
                color: #f8fafc;
                background: rgb(60, 60, 60);
            }
            .page_wrap_ { max-width: 1120px; margin: 0 auto; padding: 28px 18px 56px; }
            .hero_panel_ {
                padding: 28px;
                border: 1px solid rgba(148, 163, 184, 0.22);
                border-radius: 28px;
                background: rgba(15, 23, 42, 0.52);
                backdrop-filter: blur(10px);
            }
            .hero_eyebrow_ {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 999px;
                background: rgba(125, 211, 252, 0.18);
                color: #bae6fd;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .hero_title_ { margin: 18px 0 12px; font-size: clamp(30px, 6vw, 54px); line-height: 1.08; }
            .hero_copy_ { max-width: 760px; margin: 0; color: #cbd5e1; font-size: 16px; line-height: 1.8; }
            .switcher_ { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
            .switcher_link_ {
                padding: 11px 16px;
                border: 1px solid rgba(186, 230, 253, 0.2);
                border-radius: 999px;
                color: #e0f2fe;
                text-decoration: none;
                background: rgba(15, 23, 42, 0.36);
            }
            .switcher_link_.is_active_ { background: #f59e0b; border-color: #f59e0b; color: #172554; font-weight: 700; }
            .timeline_ { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 24px; }
            .timeline_card_ {
                padding: 22px;
                border-radius: 24px;
                background: rgba(15, 23, 42, 0.5);
                border: 1px solid rgba(148, 163, 184, 0.18);
                box-shadow: 0 20px 48px rgba(2, 6, 23, 0.24);
            }
            .timeline_step_ { color: #fbbf24; font-size: 14px; font-weight: 800; letter-spacing: 0.14em; }
            .timeline_title_ { margin: 14px 0 10px; font-size: 24px; }
            .timeline_description_ { margin: 0; color: #cbd5e1; line-height: 1.8; }
            .footer_ { margin-top: 24px; color: #bfdbfe; font-size: 13px; }
            @media (max-width: 860px) { .timeline_ { grid-template-columns: 1fr; } }
        </style>
    </head>
    <body>
        <main class="page_wrap_">
            <section class="hero_panel_">
                <div class="hero_eyebrow_">Node Multiple Template</div>
                <h1 class="hero_title_">{{ $page_config_->Page_Title_ }}</h1>
                <p class="hero_copy_">{{ $page_config_->Page_Subtitle_ }}</p>

                <nav class="switcher_">
                    @foreach ($page_config_->Focus_Options_ as $focus_key_ => $focus_name_)
                        <a class="switcher_link_ {{ $page_config_->Selected_Focus_ === $focus_key_ ? 'is_active_' : '' }}" href="{{ route('template.page.demo.multiple', ['focus' => $focus_key_]) }}">
                            {{ $focus_name_ }}
                        </a>
                    @endforeach
                </nav>
            </section>

            <section class="timeline_">
                @foreach ($page_config_->Timeline_Items_ as $timeline_item_)
                    <article class="timeline_card_">
                        <div class="timeline_step_">{{ $timeline_item_['step'] }}</div>
                        <h2 class="timeline_title_">{{ $timeline_item_['title'] }}</h2>
                        <p class="timeline_description_">{{ $timeline_item_['description'] }}</p>
                    </article>
                @endforeach
            </section>

            <div class="footer_">
                route: `template.page.demo.multiple`
            </div>
        </main>
    </body>
</html>
