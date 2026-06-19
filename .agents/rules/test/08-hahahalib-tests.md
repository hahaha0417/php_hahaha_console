# hahahalib / test 規範

- 規則：`library/hahahalib` 是本專案另一組 classmap library 原始碼位置，新增或調整 `library/hahahalib` 相關類別時，優先沿用該目錄下既有結構，例如 `aws`、`command`、`curl`、`file`、`function`、`generate`、`lock`、`orm`、`pdo`、`trait`。
  - 目前範例：`library/hahahalib/command/...`
  - 目前範例：`library/hahahalib/function/...`
  - 目前範例：`library/hahahalib/trait/...`
- 規則：`library/hahahalib` 相關測試檔固定放在 `library/hahahalib/test`。
  - 建議：`library/hahahalib/test/command/...`
  - 建議：`library/hahahalib/test/function/...`
  - 避免：`tests/Feature/...`
  - 避免：`tests/Unit/...`
- 規則：`library/hahahalib/test` 內的資料夾層級應盡量對應 `library/hahahalib` 被測類別的結構，方便從原始碼直接找到對應測試。
  - 建議：`command/...` 對應 `library/hahahalib/command/...`
  - 建議：`function/...` 對應 `library/hahahalib/function/...`
  - 建議：`trait/...` 對應 `library/hahahalib/trait/...`
- 規則：只要需求明確屬於 `library/hahahalib`，Codex 預設應先到 `library/hahahalib` 與 `library/hahahalib/test` 找相關檔案，再決定是否需要查看 `library/hahaha_laravel_lib/`、`app/`、`code/`、`routes/` 或其他區域。
