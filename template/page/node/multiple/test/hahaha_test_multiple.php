<?php

namespace hahaha\template\page\demo;

use Tests\TestCase;

class hahaha_test_multiple extends TestCase
{
    public function test_multiple_page_can_render(): void
    {
        $response_ = $this->get('/template/page/demo/multiple');

        $response_->assertStatus(200);
        $response_->assertSee('Node Multiple Template');
        $response_->assertSee('Focus');
        $response_->assertSee('Collect');
    }

    public function test_multiple_page_can_switch_focus(): void
    {
        $response_ = $this->get('/template/page/demo/multiple?focus=classmap');

        $response_->assertStatus(200);
        $response_->assertSee('Same Namespace');
        $response_->assertSee('Stable Cache');
    }
}
