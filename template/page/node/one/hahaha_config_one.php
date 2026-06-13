<?php

namespace hahaha\template\page\demo;

use hahaha\hahaha_instance_clear;

class hahaha_config_one
{
    use hahaha_instance_clear;

    public $Page_Title_ = '';

    public $Page_Subtitle_ = '';

    public $Selected_Type_ = 'featured';

    public $Type_Options_ = [];

    public $One_Cards_ = [];

    public function Initial($type = 'featured')
    {
        $this->Type_Options_ = [
            'featured' => '精選組合',
            'starter' => '入門組合',
            'team' => '團隊組合',
        ];

        if (! array_key_exists($type, $this->Type_Options_)) {
            $type = 'featured';
        }

        $this->Page_Title_ = 'Node One Template';
        $this->Page_Subtitle_ = '這個模板示範 one 可直接當成 node 規則模板目錄，不需要再包一層 node。';
        $this->Selected_Type_ = $type;
        $this->One_Cards_ = $this->One_Cards_Resolve($type);

        return $this;
    }

    public function One_Cards_Resolve($type = 'featured')
    {
        if ($type == 'starter') {
            return [
                [
                    'title' => 'Starter Template',
                    'description' => '適合快速複製成新頁面，保留最小可用的 node MVC 模板結構。',
                    'tag' => 'Starter',
                ],
                [
                    'title' => 'Direct Template',
                    'description' => 'one 模板直接放 controller、config、view、test，不再多一層 node。',
                    'tag' => 'Direct',
                ],
            ];
        } elseif ($type == 'team') {
            return [
                [
                    'title' => 'Team Board',
                    'description' => '多人協作時可從這個模板快速複製出新頁面。',
                    'tag' => 'Team',
                ],
                [
                    'title' => 'Cache Ready',
                    'description' => '分析快取會直接把 template 目錄裡的頁面模板也納入索引。',
                    'tag' => 'Cache',
                ],
                [
                    'title' => 'Classmap Ready',
                    'description' => 'classmap 可自由調整位置，不受 PSR-4 路徑限制。',
                    'tag' => 'Classmap',
                ],
            ];
        }

        return [
            [
                'title' => 'Featured MVC',
                'description' => 'Controller 只負責接 request 與 view，頁面資料由 config 提供。',
                'tag' => 'MVC',
            ],
            [
                'title' => 'Template Tree',
                'description' => '分析快取會把 one 模板目錄本身視為 node 規則模板。',
                'tag' => 'Tree',
            ],
            [
                'title' => 'Token Saver',
                'description' => '給 Codex 先讀快取，再視需要打開單一模板資料夾。',
                'tag' => 'AI',
            ],
        ];
    }
}
