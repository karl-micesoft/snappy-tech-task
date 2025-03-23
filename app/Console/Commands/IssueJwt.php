<?php

namespace App\Console\Commands;

use DateTimeImmutable;
use Illuminate\Console\Command;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;

class IssueJwt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:issue {--expired}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Issue valid JWT';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = InMemory::plainText(random_bytes(32));
        $expired = $this->option('expired');
        $token = (new JwtFacade())->issue(
            new Sha256(),
            $key,
            static fn (
                Builder $builder,
                DateTimeImmutable $issuedAt
            ): Builder => $builder
                ->issuedBy('https://api.my-awesome-app.io')
                ->expiresAt($issuedAt->modify(
                    $expired
                        ? '+0 minutes'
                        : '+60 minutes'
                ))
        );

        $this->info($token->toString());
    }
}
