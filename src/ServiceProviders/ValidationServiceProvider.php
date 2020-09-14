<?php
declare(strict_types=1);

namespace PhpMvcCore\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;
use PhpMvcCore\ValidationRules\Exists;
use PhpMvcCore\ValidationRules\Unique;
use Sirius\Validation\RuleFactory;

class ValidationServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        RuleFactory::class
    ];

    public function boot(): void
    {
        //
    }

    public function register()
    {
        $container = $this->getContainer();
        $ruleFactory = new RuleFactory();
        $ruleFactory->register('unique', Unique::class);
        $ruleFactory->register('exists', Exists::class);
        $container->share(RuleFactory::class, $ruleFactory);
    }
}
