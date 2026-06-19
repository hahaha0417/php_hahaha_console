# template / tests 規範

- 規則：`template` 是本專案 page template 與 node page 的主要原始碼位置，新增或調整 `template` 相關類別時，優先沿用 `template` 內既有結構，例如 `page/node` 與其下的 `controller`、`view`、`config`、`test`。
  - 目前範例：`template/page/node/one/...`
  - 目前範例：`template/page/node/multiple/...`
- 規則：`template` 相關正式測試檔固定放在 `template/tests`。
  - 建議：`template/tests/page/node/one/...`
  - 建議：`template/tests/page/node/multiple/...`
  - 避免：`tests/Feature/...`
  - 避免：`tests/Unit/...`
- 規則：`template/tests` 內的資料夾層級應盡量對應 `template` 被測類別的結構，方便從原始碼直接找到對應測試。
  - 建議：`page/node/one/...` 對應 `template/page/node/one/...`
  - 建議：`page/node/multiple/...` 對應 `template/page/node/multiple/...`
- 規則：只要需求明確屬於 `template`，Codex 預設應先到 `template` 與 `template/tests` 找相關檔案，再決定是否需要查看 `tool/`、`app/`、`code/`、`resources/`、`routes/` 或其他區域。
