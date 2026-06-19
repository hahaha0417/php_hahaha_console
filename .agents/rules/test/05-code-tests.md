# code / code_tests 規範

- 規則：`code` 是本專案自訂 classmap PHP 原始碼位置之一，新增或調整 `code` 相關類別時，優先沿用 `code` 內既有結構，例如 `config`、`enum`、`parameter`、`trait`。
  - 目前範例：`code/config/...`
  - 目前範例：`code/enum/...`
  - 目前範例：`code/parameter/...`
  - 目前範例：`code/trait/...`
- 規則：`code` 相關正式測試檔目標位置固定為 `code/code_tests`，不要把它當成預設放在根目錄 `tests/` 的區域。
  - 建議：`code/code_tests/config/...`
  - 建議：`code/code_tests/enum/...`
  - 避免：`tests/Feature/...`
  - 避免：`tests/Unit/...`
- 規則：若工具或 Laravel 預設流程產生的測試檔先落在根目錄 `tests/`，必須手動整理到 `code/code_tests`，不要直接保留在根目錄。
- 規則：未經使用者明確要求，不要隨便幫 `code` 區域新增測試檔；若需求只是調整規則、閱讀程式、或小幅修改邏輯，不要主動補測試。
- 規則：只要需求明確屬於 `code`，Codex 預設應先到 `code` 與 `code/code_tests` 找相關檔案，再決定是否需要查看 `app/`、`resources/`、`routes/` 或其他區域。
