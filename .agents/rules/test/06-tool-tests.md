# tool / tests 規範

- 規則：`tool` 是本專案工具型功能與 page 的主要原始碼位置，新增或調整 `tool` 相關類別時，優先沿用 `tool` 內既有結構，例如 `page`、其下的 `route`、`controller`、`config`、`view`、`test`。
  - 目前範例：`tool/page/log_viewer/...`
  - 目前範例：`tool/page/queue_viewer/...`
- 規則：`tool` 相關測試檔固定放在 `tool/tests`。
  - 建議：`tool/tests/page/log_viewer/...`
  - 建議：`tool/tests/page/queue_viewer/...`
  - 避免：`tests/Feature/...`
  - 避免：`tests/Unit/...`
- 規則：`tool/tests` 內的資料夾層級應盡量對應 `tool` 被測類別的結構，方便從原始碼直接找到對應測試。
  - 建議：`page/log_viewer/...` 對應 `tool/page/log_viewer/...`
  - 建議：`page/queue_viewer/...` 對應 `tool/page/queue_viewer/...`
- 規則：只要需求明確屬於 `tool`，Codex 預設應先到 `tool` 與 `tool/tests` 找相關檔案，再決定是否需要查看 `app/`、`code/`、`resources/`、`routes/` 或其他區域。
