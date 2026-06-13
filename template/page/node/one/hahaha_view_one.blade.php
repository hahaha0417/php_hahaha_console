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
                color: #10233a;
                background: rgb(60, 60, 60);
            }
            .page_shell_ { max-width: 1080px; margin: 0 auto; padding: 32px 20px 64px; }
            .hero_ {
                overflow: hidden;
                position: relative;
                padding: 32px;
                border: 1px solid rgba(148, 163, 184, 0.35);
                border-radius: 28px;
                background: rgba(255, 255, 255, 0.88);
                box-shadow: 0 24px 80px rgba(15, 23, 42, 0.12);
            }
            .hero_badge_ {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 14px;
                padding: 8px 14px;
                border-radius: 999px;
                background: #10233a;
                color: #ffffff;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.16em;
                text-transform: uppercase;
            }
            .hero_title_ { margin: 0; font-size: clamp(32px, 7vw, 58px); line-height: 1.05; }
            .hero_description_ { max-width: 700px; margin: 16px 0 0; color: #475569; font-size: 16px; line-height: 1.8; }
            .toolbar_ { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-top: 28px; }
            .toolbar_label_ { display: block; margin-bottom: 8px; color: #334155; font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
            .toolbar_select_ { min-width: 220px; padding: 14px 16px; border: 1px solid #cbd5e1; border-radius: 16px; background: #ffffff; color: #0f172a; font-size: 15px; }
            .toolbar_hint_ { max-width: 440px; color: #64748b; font-size: 14px; line-height: 1.7; }
            .grid_ { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 24px; }
            .card_ {
                position: relative;
                min-height: 220px;
                padding: 22px;
                border: 1px solid rgba(148, 163, 184, 0.35);
                border-radius: 24px;
                background: rgba(255, 255, 255, 0.9);
                box-shadow: 0 18px 50px rgba(14, 116, 144, 0.08);
            }
            .card_tag_ {
                display: inline-block;
                margin-bottom: 18px;
                padding: 6px 10px;
                border-radius: 999px;
                background: #ecfeff;
                color: #0f766e;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }
            .card_title_ { margin: 0 0 10px; font-size: 22px; line-height: 1.2; }
            .card_description_ { margin: 0; color: #475569; font-size: 15px; line-height: 1.8; }
            .footer_ { margin-top: 24px; color: #64748b; font-size: 13px; }
            @media (max-width: 900px) { .grid_ { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            @media (max-width: 640px) {
                .page_shell_ { padding: 20px 14px 40px; }
                .hero_ { padding: 22px; border-radius: 22px; }
                .toolbar_ { align-items: stretch; }
                .toolbar_select_ { width: 100%; }
                .grid_ { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <main class="page_shell_">
            <section class="hero_">
                <div class="hero_badge_">Node MVC Template</div>
                <h1 class="hero_title_">{{ $page_config_->Page_Title_ }}</h1>
                <p class="hero_description_">{{ $page_config_->Page_Subtitle_ }}</p>

                <form method="get" class="toolbar_">
                    <div>
                        <label class="toolbar_label_" for="type_">模板類型</label>
                        <select class="toolbar_select_" id="type_" name="type" onchange="this.form.submit()">
                            @foreach ($page_config_->Type_Options_ as $type_key_ => $type_name_)
                                <option value="{{ $type_key_ }}" @selected($page_config_->Selected_Type_ === $type_key_)>{{ $type_name_ }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="toolbar_hint_">
                        這個模板示範 `template/page/node/one` 可直接套用 node 規則，
                        讓 Codex 先讀分析快取，再只打開這個模板資料夾需要的檔案。
                    </div>
                </form>
            </section>

            <section class="grid_">
                @foreach ($page_config_->One_Cards_ as $card_)
                    <article class="card_">
                        <div class="card_tag_">{{ $card_['tag'] }}</div>
                        <h2 class="card_title_">{{ $card_['title'] }}</h2>
                        <p class="card_description_">{{ $card_['description'] }}</p>
                    </article>
                @endforeach
            </section>

            <div class="footer_">
                route: `template.page.demo.one`
            </div>
        </main>
    </body>
</html>
