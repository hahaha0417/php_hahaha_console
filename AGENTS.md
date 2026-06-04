# Laravel Boost

<laravel-boost-guidelines>
=== 核心技術棧 ===

## 執行環境
- php 8.3
- laravel/framework v13
- laravel/prompts v0
- laravel/boost v2
- laravel/mcp v0
- laravel/pail v1
- laravel/pint v1
- phpunit/phpunit v12
- tailwindcss v4

## 一般規範
- 除非使用者明確要求，不可修改 `**/skills/**` 下的任何檔案。
- 命名請語意清楚，布林語意優先使用 `is*` / `has*`。
- 優先遵循現有專案慣例，避免混用風格。

## 編碼保護（重要）
- 產生或修改檔案時，請使用 `pwsh`（PowerShell 7+），不要使用 `powershell.exe`（5.1）。
- 目標編碼固定為 UTF-8 No-BOM。
- 避免使用未明確指定編碼的整檔覆寫方式。

## 專案上下文
- 需要時優先參考：
  - `storage/app/ai-context/code-summary.md`
  - `storage/app/ai-context/project-structure.md`
  - `storage/app/ai-context/`

## 文件查詢
- 對框架行為不確定時，先用 `search-docs`。

## Artisan
- 不熟的指令先看 `php artisan list` 與 `php artisan [command] --help`。
- 路由檢查使用 `php artisan route:list`。

## 測試
- 有行為變更就新增或調整測試。
- 先跑最小範圍，再視需要擴大測試集。

## Pint
- 針對異動檔執行：`vendor/bin/pint --dirty --format=agent`

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
