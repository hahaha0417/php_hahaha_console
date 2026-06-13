<?php

namespace hahaha\template\page\demo;

use hahaha\hahaha_instance_clear;

class hahaha_config_multiple
{
    use hahaha_instance_clear;

    public $Page_Title_ = '';

    public $Page_Subtitle_ = '';

    public $Focus_Options_ = [];

    public $Selected_Focus_ = 'cache';

    public $Timeline_Items_ = [];

    public function Initial($focus = 'cache')
    {
        $this->Focus_Options_ = [
            'cache' => '分析快取',
            'tree' => '樹狀結構',
            'classmap' => 'Classmap',
        ];

        if (! array_key_exists($focus, $this->Focus_Options_)) {
            $focus = 'cache';
        }

        $this->Page_Title_ = 'Node Multiple Template';
        $this->Page_Subtitle_ = '第二個模板示範每個 template 目錄可直接套用 node 規則，並保留 controller、config、view、test 子資料夾。';
        $this->Selected_Focus_ = $focus;
        $this->Timeline_Items_ = $this->Timeline_Items_Resolve($focus);

        return $this;
    }

    public function Timeline_Items_Resolve($focus = 'cache')
    {
        if ($focus == 'tree') {
            return [
                ['step' => '01', 'title' => 'Find Template', 'description' => '先找出 classmap roots 裡符合 node 規則的 template 資料夾。'],
                ['step' => '02', 'title' => 'Read Tree', 'description' => '再遞迴整理 controller、view、config、test 等子資料夾與檔案。'],
                ['step' => '03', 'title' => 'Open Target', 'description' => '最後只針對需要的模板檔案深入閱讀，節省分析時間。'],
            ];
        } elseif ($focus == 'classmap') {
            return [
                ['step' => 'A', 'title' => 'Free Location', 'description' => '檔案位置由 classmap 控制，不必跟 PSR-4 路徑硬綁定。'],
                ['step' => 'B', 'title' => 'Same Namespace', 'description' => '同一個模板規則目錄下的 PHP 檔案可共用 namespace。'],
                ['step' => 'C', 'title' => 'Stable Cache', 'description' => '分析快取只要看樹與分類，就能快速定位要讀的模板檔案。'],
            ];
        }

        return [
            ['step' => '1', 'title' => 'Collect', 'description' => '快取命令先蒐集 routes、database、packages、template tree 與 tests。'],
            ['step' => '2', 'title' => 'Compress', 'description' => '再輸出成 markdown 與 json，適合給 Codex 當預讀上下文。'],
            ['step' => '3', 'title' => 'Focus', 'description' => '需要深入時再打開單一模板資料夾，避免整個專案反覆掃描。'],
        ];
    }
}
