<?php
namespace SocialiteProviders\YouTube;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected $scopes = ['https://www.googleapis.com/auth/youtube.readonly'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://accounts.google.com/o/oauth2/auth', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://accounts.google.com/o/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://www.googleapis.com/youtube/v3/channels?part=snippet&mine=true', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true)['items'][0];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'], 'nickname' => $user['snippet']['title'],
            'name' => null, 'email' => null,
            'avatar' => $user['snippet']['thumbnails']['high']['url'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        $user = $this->mapUserToObject($this->getUserByToken(
            $token = $this->getAccessToken($this->getCode())
        ));

        return $user->setToken($token);
    }
}
