# AGENTS

本檔作為專案規則入口。細節規則已拆到 `.agents/rules/` 方便維護。

## 讀取原則

- 每次處理需求前，先讀本檔。
- 再依需求只讀對應規則檔，不必每次把全部規則重新展開。
- 若規則衝突，優先順序如下：
  1. 本檔「最高優先」規則
  2. `.agents/rules/workflow/00-laravel-boost.md`
  3. 對應用途規則檔
  4. 既有程式碼實作慣例

## 最高優先

- 遵守 Laravel Boost / Laravel 13 / Octane 2 / PHPUnit 12 / Tailwind 4 專案規範。
- 修改前先依需求讀對應規則檔，不要大範圍盲讀整個專案。
- 不要未經同意新增依賴、改動大目錄結構、刪除測試。
- 沒事不要主動建立或補測試檔，只有需求明確要求、風險高、或真的有必要驗證時才做。
- 若有 PHP 檔異動，結束前要執行 `vendor/bin/pint --dirty --format agent`。
- 若有測試異動，至少跑最小相關測試。
- 只在使用者明確要求時建立文件檔。

## 規則索引

### 工作流程 / Laravel / Boost 通用規則

- [`workflow/00-laravel-boost.md`](.agents/rules/workflow/00-laravel-boost.md)

### 命名規則

- [`naming/01-project-naming.md`](.agents/rules/naming/01-project-naming.md)

### 產生器 / page 規則

- [`generator/02-node-template-page.md`](.agents/rules/generator/02-node-template-page.md)

### 測試相關規則

- [`test/03-library-tests.md`](.agents/rules/test/03-library-tests.md)
- [`test/04-app-tests.md`](.agents/rules/test/04-app-tests.md)
- [`test/05-code-tests.md`](.agents/rules/test/05-code-tests.md)
- [`test/06-tool-tests.md`](.agents/rules/test/06-tool-tests.md)
- [`test/07-template-tests.md`](.agents/rules/test/07-template-tests.md)
- [`test/08-hahahalib-tests.md`](.agents/rules/test/08-hahahalib-tests.md)

## 需求對應讀法

- Laravel / Artisan / 測試 / Pint / Octane / Boost 工具：
  - 先讀 [`workflow/00-laravel-boost.md`](.agents/rules/workflow/00-laravel-boost.md)
- 命名、class、常數、enum、config：
  - 先讀 [`naming/01-project-naming.md`](.agents/rules/naming/01-project-naming.md)
- `template/`、`tool/`、node page、page template：
  - 先讀 [`generator/02-node-template-page.md`](.agents/rules/generator/02-node-template-page.md)
- `library/hahaha_laravel_lib` 相關需求：
  - 先讀 [`test/03-library-tests.md`](.agents/rules/test/03-library-tests.md)
- `app` 相關需求：
  - 先讀 [`test/04-app-tests.md`](.agents/rules/test/04-app-tests.md)
- `code` 相關需求：
  - 先讀 [`test/05-code-tests.md`](.agents/rules/test/05-code-tests.md)
- `tool` 相關需求：
  - 先讀 [`test/06-tool-tests.md`](.agents/rules/test/06-tool-tests.md)
- `template` 相關需求：
  - 先讀 [`test/07-template-tests.md`](.agents/rules/test/07-template-tests.md)
- `library/hahahalib` 相關需求：
  - 先讀 [`test/08-hahahalib-tests.md`](.agents/rules/test/08-hahahalib-tests.md)

## node 分析快取讀取順序

不論是 node 或非 node 需求，都先依這個順序縮小範圍：

1. `storage/app/ai-context/node/work-target-analysis.md`
2. `storage/app/ai-context/node/page-node-analysis.md`
3. `storage/app/ai-context/node/project-analysis.md`
4. 最後才打開和需求直接相關的 `controller`、`view`、`config`、`test` 檔案

避免一開始就大範圍逐檔掃描 `template/`、`tool/`、`code/`、`library/`、`app/`、`routes/`、`resources/`。
