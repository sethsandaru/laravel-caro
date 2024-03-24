<?php

namespace Tests\Feature\Frontend;

use Tests\TestCase;

class IndexPageTest extends TestCase
{
    public function testViewIndexPageOk()
    {
        $this->get('/')->assertSeeText('Laravel Caro');
    }
}
