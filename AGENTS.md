# Laravel Boost

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/framework (LARAVEL) - v13
- laravel/octane (OCTANE) - v2
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== octane/core rules ===

# Octane

- Octane boots the application once and reuses it across requests, so singletons persist between requests.
- The Laravel container's `scoped` method may be used as a safe alternative to `singleton`.
- Never inject the container, request, or config repository into a singleton's constructor; use a resolver closure or `bind()` instead:

```php
// Bad
$this->app->singleton(Service::class, fn (Application $app) => new Service($app['request']));

// Good
$this->app->singleton(Service::class, fn () => new Service(fn () => request()));
```

- Never append to static properties, as they accumulate in memory across requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>

## 專案命名規範（可執行版）

- 規則：資料表名稱使用 `hahaha_xxx_xxx`。
  - 建議：`hahaha_user_profiles`、`hahaha_order_items`
  - 避免：`user_profiles`、`hahahaUserProfiles`
- 規則：PHP 變數名稱使用 `xxx_xxx_`。
  - 建議：`$Order_Total`、`$User_Id`
  - 避免：`$orderTotal`、`$userId`
- 規則：PHP 區域變數名稱使用 `xxx_xxx_`。
  - 建議：`$order_total_`、`$user_id_`
  - 避免：`$orderTotal`、`$userId`
- 規則：Model / Controller / Job / Service 識別命名需帶角色語意前綴。
  - 建議：`hahaha_job_sync_orders`、`hahaha_service_discount_calculator`
  - 避免：`sync`、`service1`
- 規則：若既有方法命名已採 `xxx_` 前綴，新增方法需延續。
  - 建議：`Order_Create`、`Discount_Apply`
  - 避免：同一類別混用 `createOrder()` 與 `order_create_`
- 規則：常數名稱使用 `XXX_XXX`（全大寫底線）。
  - 建議：`MAX_RETRY_COUNT`、`DEFAULT_TIMEOUT_SECONDS`
  - 避免：`MaxRetryCount`、`default_timeout_seconds`
- 規則：布林欄位與布林變數優先使用 `is*` / `has*`。
  - 建議：`is_active`、`has_discount`
  - 避免：`active_flag`、`discount_enabled`（若非既有欄位）
- 規則：Blade 檔名與區塊命名遵循 Laravel 慣例並保持一致。
  - 建議：`resources/views/orders/index.blade.php`
  - 避免：`resources/views/Orders/IndexBlade.php`
- 規則：Migration 檔名遵守 Laravel 預設 timestamp + snake_case。
  - 建議：`2026_05_31_00001_create_hahaha_orders_table.php` `2026_05_31_00002_create_hahaha_orders_table.php`
  - 避免：`createOrdersTable.php`
- 規則：Enum 值使用 `UPPER_SNAKE_CASE`。
  - 建議：`PENDING_APPROVAL`、`PAYMENT_FAILED`
  - 避免：`PendingApproval`、`paymentFailed`

## enum / config 規範

- 規則：本專案 `code/enum` 內優先使用一般 PHP class 常數，不使用 PHP `enum`。
  - 建議：`class hahaha_enum_animal { public const DOG = 'DOG'; }`
  - 避免：`enum hahaha_enum_animal: string { case DOG = 'DOG'; }`
- 規則：`code/config` 內的設定對照表，key 直接使用 enum class 常數值。
  - 建議：`hahaha_enum_animal::DOG => '狗'`
  - 避免：`hahaha_enum_animal::DOG->value => '狗'`
- 規則：需要分類切換的 config，於 `Initial($type = '1')` 內手動列舉各組內容，保留可直接編輯的寫法。
  - 建議：`if ($type == '1') { ... } elseif ($type == '2') { ... }`
  - 避免：用自動推導或 `cases()` 產生，造成後續不易手動調整順序與內容

## node / template page 規範

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
