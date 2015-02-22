<?php
namespace SocialiteProviders\YouTube;

use SocialiteProviders\Manager\SocialiteWasCalled;

class YouTubeExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('youtube', __NAMESPACE__.'\Provider');
    }
}
