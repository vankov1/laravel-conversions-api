<?php

namespace Esign\ConversionsApi\Tests\Feature\View\Components;

use Esign\ConversionsApi\Tests\Support\Events\ContactEvent;
use Esign\ConversionsApi\Tests\TestCase;
use Esign\ConversionsApi\View\Components\DataLayerVariable;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

class DataLayerVariableTest extends TestCase
{
    use InteractsWithViews;

    /** @test */
    public function it_can_render_the_view()
    {
        $event = (new ContactEvent())->setEventId('9a97e3f0-3dbb-4d74-bf05-a42f330f843d');
        $component = $this->component(DataLayerVariable::class, [
            'event' => $event,
        ]);

        $component->assertSee(
            'window.dataLayer.push({"event":"Contact","conversionsApiContactEventId":"9a97e3f0-3dbb-4d74-bf05-a42f330f843d"});',
            false
        );
    }

    /** @test */
    public function it_can_render_anonymously()
    {
        $view = $this->blade('
            <x-conversions-api::data-layer-variable
                :arguments="[\'event\' => \'contact\']"
            />
        ');

        $view->assertSee('window.dataLayer.push({"event":"contact"});', false);
    }
}