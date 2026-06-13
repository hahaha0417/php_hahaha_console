<?php

namespace hahaha\template\page\demo;

use Tests\TestCase;

class hahaha_test_one extends TestCase
{
    public function test_one_page_can_render_from_template_directory(): void
    {
        $response_ = $this->get('/template/page/demo/one');

        $response_->assertStatus(200);
        $response_->assertSee('Node One Template');
        $response_->assertSee('Node MVC Template');
        $response_->assertSee('Token Saver');
    }

    public function test_one_page_can_switch_type_by_query_string(): void
    {
        $response_ = $this->get('/template/page/demo/one?type=team');

        $response_->assertStatus(200);
        $response_->assertSee('Team Board');
        $response_->assertSee('Classmap Ready');
    }
}
