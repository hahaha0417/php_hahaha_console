# library / Tests 規範

- 規則：`library/hahaha_laravel_lib` 是本專案 Laravel library 原始碼的主要位置，新增或調整 library 相關類別時，優先放在這個目錄下並依現有子目錄結構延伸。
  - 目前範例：`library/hahaha_laravel_lib/Console/Commands/...`
  - 目前範例：`library/hahaha_laravel_lib/Services/...`
  - 目前範例：`library/hahaha_laravel_lib/Providers/...`
- 規則：`library/hahaha_laravel_lib` 內的測試檔固定放在 `library/hahaha_laravel_lib/Tests`，不要改放到根目錄 `tests/`。
  - 建議：`library/hahaha_laravel_lib/Tests/Services/agents/skills/github/hahaha_test_agents_skills_github_service_Test.php`
  - 建議：`library/hahaha_laravel_lib/Tests/Console/Commands/ai/node/hahaha_test_cache_node_project_analysis_command_Test.php`
  - 避免：`tests/Feature/HahahaAgentSkillGithubServiceTest.php`
- 規則：`library/hahaha_laravel_lib/Tests` 內的資料夾層級應盡量對應 `library/hahaha_laravel_lib` 被測類別的結構，方便從原始碼直接找到對應測試。
  - 建議：`Services/agents/skills/github/...` 對應 `library/hahaha_laravel_lib/Services/agents/skills/github/...`
  - 建議：`Console/Commands/ai/node/...` 對應 `library/hahaha_laravel_lib/Console/Commands/ai/node/...`
- 規則：只要需求明確屬於 `library/hahaha_laravel_lib`，Codex 預設應先到 `library/hahaha_laravel_lib` 與 `library/hahaha_laravel_lib/Tests` 找相關檔案，再決定是否需要查看其他區域。
