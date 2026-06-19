# app / Tests 規範

- 規則：`app` 是本專案應用層原始碼的主要位置，新增或調整 application 相關類別時，優先放在 `app` 下既有結構中，例如 `Console`、`Http`、`Jobs`、`Models`、`Providers`、`Enums`。
  - 目前範例：`app/Console/Commands/...`
  - 目前範例：`app/Http/Controllers/...`
  - 目前範例：`app/Jobs/...`
  - 目前範例：`app/Models/...`
- 規則：`app` 相關測試檔固定放在 `app/Tests`，不要預設改放到根目錄 `tests/`。
  - 建議：`app/Tests/Http/Controllers/backend/animal/hahaha_test_backend_animal_controller_Test.php`
  - 建議：`app/Tests/Console/Commands/tool/hahaha_test_laravel_migrate_command_Test.php`
  - 避免：`tests/Feature/HahahaBackendAnimalControllerTest.php`
- 規則：`app/Tests` 內的資料夾層級應盡量對應 `app` 被測類別的結構，方便從原始碼直接找到對應測試。
  - 建議：`Http/Controllers/backend/animal/...` 對應 `app/Http/Controllers/backend/animal/...`
  - 建議：`Console/Commands/tool/...` 對應 `app/Console/Commands/tool/...`
  - 建議：`Jobs/...` 對應 `app/Jobs/...`
- 規則：只要需求明確屬於 `app`，Codex 預設應先到 `app` 與 `app/Tests` 找相關檔案，再決定是否需要查看 `code/`、`resources/`、`routes/` 或其他區域。
