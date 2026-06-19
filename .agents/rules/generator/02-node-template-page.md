# node / template page 規範

- 規則：本專案的 node page 採 `classmap` 管理，不採 PSR-4 自動對應路徑。
  - 依據：`composer.json` 的 `autoload.classmap` 已包含 `code`
  - 規則：調整 `code/...` 內 node 相關類別位置或 namespace 後，要記得執行 `composer dump-autoload`
- 規則：page template 範本放在 `template/page/node/...`，未來新頁面優先由 template 複製或延伸。
  - 目前範例：`template/page/node/one`
  - 目前範例：`template/page/node/multiple`
- 規則：template page 的共用 namespace 使用 `hahaha\template\page\demo;` 這種 classmap namespace，不依資料夾層級強制切分。
  - 建議：`namespace hahaha\template\page\demo;`
  - 避免：`namespace hahaha\template\page\demo\one\node;`
  - 避免：依 PSR-4 心智假設 namespace 必須跟目錄完全一致
- 規則：node page 允許兩種檔案擺法，但都不再額外多一層 `node` 資料夾。
  - 扁平式：直接放在頁面目錄底下，例如 `template/page/node/one/hahaha_controller_one.php`
  - 分類式：放在頁面目錄下的 `controller`、`view`、`config`、`test` 等子資料夾，例如 `template/page/node/multiple/controller/hahaha_controller_multiple.php`
- 規則：不要再使用 flat(node) 當作額外命名規則；是否扁平或分資料夾，只是檔案擺放方式，不是另一套 namespace 規格。
- 規則：`node` 是頁面組織概念，不是 route 名稱的一部分。
  - 建議路由：`/template/page/demo/one`
  - 避免路由：`/template/page/demo/one/node`
- 規則：page template 路由集中在 `routes/web/template.php`，使用 `Route::group` / `prefix` / `name` 管理。
  - 建議：`Route::prefix('template/page/demo')->name('template.page.demo.')->group(...)`
- 規則：controller 命名使用 `hahaha_controller_xxx.php`。
  - 建議：`hahaha_controller_one`
  - 建議：`hahaha_controller_multiple`
- 規則：view 命名使用 `hahaha_view_xxx.blade.php`。
  - 建議：`hahaha_view_one.blade.php`
  - 建議：`hahaha_view_multiple.blade.php`
- 規則：原本 node page 中作為頁面資料來源的 `model`，在這套規格中改為 `config`。
  - 建議：`hahaha_config_one.php`
  - 建議：`hahaha_config_multiple.php`
  - 避免：在這類 template page 繼續新增 `hahaha_model_one.php` 當頁面設定載體
- 規則：`config` 類別主要承接頁面顯示資料、切換選項、卡片內容、文案等可編輯設定；真正資料庫存取邏輯若需要，需另外明確設計，不把 template page config 當 Eloquent model 使用。
- 規則：node page 的測試檔跟著頁面一起放在 page 目錄內，視該頁面採扁平或分類式結構決定位置。
  - 扁平式範例：`template/page/node/one/hahaha_test_one.php`
  - 分類式範例：`template/page/node/multiple/test/hahaha_test_multiple.php`
- 規則：node / template 分析快取使用 `library/hahaha_laravel_lib/Console/Commands/ai/node/hahaha_cache_node_project_analysis.php` 對 classmap 目錄掃描產生。
  - 主要用途：提供 Codex 先讀專案摘要與 node/page 樹狀分析，減少逐檔探索成本與 token 消耗
  - 目前輸出位置：`storage/app/ai-context/node/`
- 規則：專案分析快取必須能辨識 `controller`、`view`、`config`、`test` 與其他 `hahaha_???_xxx.php` 類型檔案，並保留頁面樹狀結構摘要，方便 AI 快速判讀。
- 規則：Codex 每次處理需求前，不論是 node 或非 node 的需求，應先讀 `storage/app/ai-context/node/work-target-analysis.md`，再讀 `storage/app/ai-context/node/page-node-analysis.md`，再讀 `storage/app/ai-context/node/project-analysis.md`，最後只打開和需求直接相關的 `controller`、`view`、`config`、`test` 檔案。
  - 主要目的：先用快取縮小範圍，再進一步讀實際檔案，降低 token 消耗
  - 建議順序：`storage/app/ai-context/node/work-target-analysis.md` → `storage/app/ai-context/node/page-node-analysis.md` → `storage/app/ai-context/node/project-analysis.md` → 需求直接相關檔案
  - 適用範圍：包含 `template/`、`tool/`、`code/`、`library/`、`app/`、`routes/`、`resources/` 與其他需求相關區域
  - 避免：一開始就大範圍逐檔掃描 `template/`、`tool/`、`code/`、`library/`、`app/`、`routes/`、`resources/`

## 如何要求 Codex 建立 node page

- 若要我依 node 規則建立 page，請直接明講「用 node 規則做 page」。
- 最少請提供這 4 個資訊：
  - page 名稱，例如 `order_list`
  - 擺法，例如 `one`（扁平式）或 `multiple`（分資料夾式）
  - route 路徑，例如 `/template/page/demo/order-list`
  - route name，例如 `template.page.demo.order_list`
- 若你沒有特別說 namespace，我會沿用目前規則：`namespace hahaha\template\page\demo;`
- 若你沒有特別說位置，我會沿用目前規則放在 `template/page/node/...`
- 若你沒有特別說資料來源，我會先建立 `hahaha_config_xxx.php`，不會先建立 `hahaha_model_xxx.php`

- 建議你這樣對我說：
  - `請用 node 規則幫我做一個 page，名稱 order_list，採 one，放在 template/page/node/order_list，route 是 /template/page/demo/order-list，route name 是 template.page.demo.order_list。`
  - `請用 node 規則幫我做一個 page，名稱 customer_report，採 multiple，要有 controller/view/config/test，route 是 /template/page/demo/customer-report。`
  - `請用現有 node template 複製一份新 page，從 one 範本產生 billing_center。`

- 如果你只說一句簡短需求，也可以：
  - `用 node 規則做一個 order_list page`
  - `用 multiple node 規則做 customer_report`
  - `從 node template 建一個 billing_center`

- 我收到這類需求時，預設會一起處理：
  - 建立 controller、view、config、test
  - 補 route
  - 刷新 classmap / autoload
  - 重建 node 分析快取
