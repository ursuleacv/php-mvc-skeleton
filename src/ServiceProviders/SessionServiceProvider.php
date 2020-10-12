<?php
declare(strict_types=1);

namespace PhpMvcCore\ServiceProviders;

use Dflydev\FigCookies\Modifier\SameSite;
use Dflydev\FigCookies\SetCookie;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use League\Container\ServiceProvider\AbstractServiceProvider;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

class SessionServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        SessionMiddleware::class
    ];

    public function boot()
    {
        //
    }

    /**
     * ------------------------------------------------- --------------------------------------
     * PSR-7 Storage-less HTTP Sessions
     * ------------------------------------------------- --------------------------------------
     * This Middleware enables the use of Sessions without using super globals or files, it is
     * a safer way and allows to solve limitations of conventional sessions,
     * the data is stored in a cookie, sensitive information should not be stored.
     * ------------------------------------------------- --------------------------------------
     * @see https://github.com/psr7-sessions/storageless
     */
    public function register()
    {
        $this->getLeagueContainer()->add(SessionMiddleware::class, function () {
            return new SessionMiddleware(
                new Sha256(),
                env('APP_KEY'),
                env('APP_KEY'),
                SetCookie::create('psr7-session')
                    ->withSecure(false)
                    ->withHttpOnly(true)
                    ->withSameSite(SameSite::lax())
                    ->withPath('/'),
                new Parser(),
                1200,
                new SystemClock(new \DateTimeZone('UTC'))
            );
        });
    }
}
