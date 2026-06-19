# 專案命名規範

## 可執行版命名規範

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
- 規則：PHP class、function、變數不要使用 `private` 或 `protected`，全部使用 `public`。
  - 建議：`public string $Order_Total;`、`public function Order_Create(): void`
  - 避免：`private string $orderTotal;`、`protected function createOrder(): void`
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
